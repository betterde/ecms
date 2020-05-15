<?php

namespace App\Http\Controllers\Auth;

use Exception;
use Throwable;
use App\Models\Customer;
use App\Models\Invitation;
use Tymon\JWTAuth\JWTGuard;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Date: 2020/5/12
     * @param Request $request
     * @return JsonResponse|mixed
     * @throws Throwable
     * @author George
     */
    public function register(Request $request)
    {
        if ($request->hasValidSignature(false) === false) {
            return failed('该邀请连接错误或已经失效！', 400);
        }

        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        try {
            if (!$token = $this->guard()->login($user)) {
                return failed('认证失败，用户名或密码不正确', 401);
            }
        } catch (JWTException $exception) {
            return internalError();
        }

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return stored([
            'id' => $user->id,
            'name' => $user->name,
            'mobile' => $user->mobile,
            'user_type' => 'customer',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => config('jwt.ttl') * 60,
        ], '恭喜你，注册成功!');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:customers'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'initiator' => ['required', 'uuid'],
            'initiator_type' => ['required', 'in:user,customer']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return Customer|null
     * @throws Throwable
     */
    protected function create(array $data)
    {
        try {
            DB::beginTransaction();
            $invitation = Invitation::where('signature', $data['signature'])->firstOrFail();
            $attributes = [
                'id' => Str::uuid(),
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'referrer' => $data['initiator'],
                'referrer_type' => $data['initiator_type']
            ];
            $customer = Customer::create($attributes);
            $invitation->status = 'registered';
            $invitation->saveOrFail();
            DB::commit();
            return $customer;
        } catch (Throwable $exception) {
            DB::rollBack();
            throw new Exception('注册用户失败，请稍后再试！');
        }
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
