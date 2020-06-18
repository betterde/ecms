<?php

namespace App\Http\Controllers;

use Throwable;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Validation\ValidationException;

/**
 * Profile logic controller
 *
 * Date: 2020/4/17
 * @author George
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
     * Get user profile
     *
     * Date: 2020/4/17
     * @param User $user
     * @return JsonResponse
     * @author George
     */
    public function show(User $user)
    {
        return success($user);
    }

    /**
     * Modify user profile
     *
     * Date: 2020/4/17
     * @param User $user
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @author George
     */
    public function update(User $user, Request $request)
    {
        $attributes = $this->validate($request, [
            'name' => 'nullable',
            'email' => [
                'nullable',
                Rule::unique('users')->ignoreModel($user)
            ],
            'password' => 'nullable'
        ]);

        foreach ($attributes as $index => $attribute) {
            if (is_null($attribute) || empty($attribute)) {
                unset($attributes[$index]);
            }
        }

        if (empty($attributes)) {
            return message('请填写需要修改的内容', 422);
        }

        $attributes['password'] = Hash::make($attributes['password']);

        $user->update($attributes);
        return updated($user);
    }

    /**
     * Modify user password
     *
     * Date: 2020/5/15
     * @param Request $request
     * @param Hasher $hasher
     * @return JsonResponse
     * @throws Throwable
     * @author George
     */
    public function password(Request $request, Hasher $hasher)
    {
        $attributes = $request->validate([
            'old' => ['required', 'string', 'min:8'],
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $user = $request->user();
        if ($user instanceof Customer) {
            $provider = new EloquentUserProvider($hasher, Customer::class);
        } else {
            $provider = new EloquentUserProvider($hasher, User::class);
        }

        if ($provider->validateCredentials($user, ['password' => $attributes['old']])) {
            $user->password = Hash::make($attributes['password']);
            $user->saveOrFail();
            return message('密码修改成功！');
        }

        return failed('您输入的旧密码有误！', 400);
    }

    /**
     * Modify user address
     *
     * Date: 2020/5/15
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function address(Request $request)
    {
        $attributes = $request->validate([
            'address' => 'nullable|string',
        ]);

        $user = $request->user();
        if ($user instanceof Customer) {
            $user->update($attributes);
            return success($user);
        } else {
            return failed('用户类型不支持修改地址信息！', 400);
        }
    }

    /**
     * Modify user avatar
     *
     * Date: 2020/4/24
     * @param Request $request
     * @return mixed
     * @throws ValidationException
     * @author George
     */
    public function avatar(Request $request)
    {
        $attributes = $this->validate($request, [
            'avatar' => 'required|dimensions:min_width=256,min_height=256,max_width=1024,max_height=1024'
        ]);

        /**
         * @var UploadedFile $file
         */
        $file = $attributes['avatar'];
        $path = $file->store('avatar', 'public');

        /**
         * @var User $user
         */
        $user = Auth::user();
        $url = Storage::url($path);
        $user->update(['avatar' => $url]);

        return success([
            'url' => $url
        ]);
    }
}
