<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    /**
     * Send a reset link to the given user.
     *
     * @param Request $request
     * @param Hasher $hasher
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function send(Request $request, Hasher $hasher)
    {
        $credentials = $this->validate($request, [
            'email' => 'required|email'
        ]);

        $provider = new EloquentUserProvider($hasher, User::class);
        /**
         * @var User $user
         */
        $user = $provider->retrieveByCredentials($credentials);

        if (is_null($user)) {
            return failed('邮箱不存在！');
        }

        $token = Password::broker()->createToken($user);
        $user->sendPasswordResetNotification($token);

        return message('邮件已发送到您的邮箱，请注意查收！');
    }
}
