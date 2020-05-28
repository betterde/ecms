<?php

namespace App\Http\Controllers\Auth;

use App\Models\Certificate;
use Throwable;
use Google_Client;
use App\Models\User;
use GuzzleHttp\Client;
use App\Models\Customer;
use Tymon\JWTAuth\JWTGuard;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Auth\EloquentUserProvider;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * 用户认证逻辑控制器
 *
 * Date: 2019/12/8
 * @author George
 * @package App\Http\Controllers\Auth
 */
class AuthenticationController extends Controller
{
    use AuthenticatesUsers;

    /**
     * AuthenticationController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:users,customer', ['except' => ['signin', 'applet', 'issue']]);
    }

    /**
     * 用户登陆逻辑
     *
     * Date: 2018/10/15
     * @author George
     * @param Request $request
     * @param Hasher $hasher
     * @return JsonResponse
     * @throws ValidationException
     */
    public function signin(Request $request, Hasher $hasher)
    {
        $credentials = $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ], [
            'username.required' => '请输入您的账户',
            'password.required' => '请输入您的密码',
        ]);

        $guard = $request->get('guard', $this->guard());

        $username = Arr::pull($credentials, 'username');
        $credentials[$this->username($username)] = $username;

        if ($guard == 'customer') {
            $provider = new EloquentUserProvider($hasher, Customer::class);
        } else {
            $provider = new EloquentUserProvider($hasher, User::class);
        }

        /**
         * 根据用户凭证获取用户信息
         *
         * @var User|Customer $user
         */
        $user = $provider->retrieveByCredentials($credentials);

        if ($user && $provider->validateCredentials($user, $credentials)) {
            try {
                if (!$token = $this->guard()->login($user)) {
                    return failed('认证失败，用户名或密码不正确', 401);
                }
            } catch (JWTException $exception) {
                return internalError();
            }

            if ($guard == 'customer') {
                return success([
                    'id' => $user->id,
                    'name' => $user->name,
                    'mobile' => $user->mobile,
                    'user_type' => 'customer',
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => config('jwt.ttl') * 60,
                ]);
            }

            return success([
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => 'user',
                'access_token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ]);
        }

        return failed('认证失败，用户名或密码不正确', 401);
    }

    /**
     * Date: 2020/4/27
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @throws Throwable
     * @author George
     */
    public function issue(Request $request)
    {
        $params = $this->validate($request, [
            'access_token' => 'required',
            'platform' => 'required',
            'guard' => 'in:user,customer'
        ]);

        switch ($params['platform']) {
            case 'Google':
                $httpClient = new Client([
                    'proxy' => 'socks5h://127.0.0.1:1080',
                    'verify' => false
                ]);
                $client = new Google_Client(['client_id' => config('services.google.client_id')]);
                $client->setHttpClient($httpClient);
                $payload = $client->verifyIdToken($params['access_token']);
                if ($payload) {
                    /**
                     * @var Certificate $certificate
                     */
                    $certificate = Certificate::with('ownerable')->where('identity', $payload['sub'])->first();
                    if ($certificate) {
                        /**
                         * 根据用户凭证获取用户信息
                         *
                         * @var User|Customer $user
                         */
                        $user = $certificate->ownerable;
                    } else {
                        if ($params['guard'] === 'user') {
                            $user = User::where('email', $payload['email'])->first();
                        } else {
                            $user = Customer::where('email', $payload['email'])->first();
                        }

                        if (is_null($user)) {
                            return failed('用户信息不存在！');
                        }

                        Certificate::create([
                            'owner_id' => $user->getKey(),
                            'owner_type' => $params['guard'],
                            'identity' => $payload['sub'],
                            'platform' => $params['platform'],
                        ]);
                    }

                    try {
                        if (!$token = $this->guard()->login($user)) {
                            return failed('认证失败，用户名或密码不正确', 401);
                        }
                    } catch (JWTException $exception) {
                        return internalError();
                    }

                    return success([
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'access_token' => $token,
                        'token_type' => 'Bearer',
                        'expires_in' => config('jwt.ttl') * 60,
                    ]);
                } else {
                    return failed('无效的用户凭证！');
                }
            default:
                return failed('目前未接入该平台！');
        }
    }

    /**
     * Applet auth session
     *
     * Date: 2019/12/22
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function session(Request $request)
    {
        $code = $request->get('code', null);
        if (is_null($code)) {
            return failed('请求失败，无法获取Code。', 422);
        }

        $client = new Client();
        $response = $client->get('https://api.weixin.qq.com/sns/jscode2session', [
            'query' => [
                'appid' => env('APPLET_ID'),
                'secret' => env('APPLET_SECRET'),
                'js_code' => $code,
                'grant_type' => 'authorization_code',
            ]
        ]);

        $result = json_decode($response->getBody()->getContents(), JSON_UNESCAPED_UNICODE);
        if ($response->getStatusCode() !== 200) {
            return internalError($result['errmsg']);
        }

        return success($result);
    }

    /**
     * Get user profile
     *
     * Date: 2019/12/8
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function profile(Request $request)
    {
        $user = $request->user();
        $user instanceof User ? $user->type = 'user' : $user->type = 'customer';
        return success($user);
    }

    /**
     * 注销登陆
     *
     * Date: 2018/10/14
     * @author George
     * @return JsonResponse
     */
    public function signout()
    {
        $this->guard()->logout();
        return message('注销成功');
    }

    /**
     * 获取用户凭证字段
     *
     * Date: 2018/9/9
     * @param string $useranme
     * @return string
     * @author George
     */
    public function username(string $useranme)
    {
        if (filter_var($useranme, FILTER_VALIDATE_EMAIL) !== false) {
            return 'email';
        }
        return 'mobile';
    }

    /**
     * 获取Guard实例
     *
     * Date: 2018/10/14
     * @author George
     * @return JWTGuard
     */
    protected function guard()
    {
        return Auth::guard('users');
    }
}
