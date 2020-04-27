<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

/**
 * Date: 2020/4/17
 * @author George
 * @package App\Http\Controllers
 */
class ProfileController extends Controller
{
    /**
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
     * 上传头像
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
