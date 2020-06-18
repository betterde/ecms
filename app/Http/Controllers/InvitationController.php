<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Customer;
use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use App\Notifications\RegisterInvitation;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    /**
     * Date: 2020/6/19
     * @param Request $request
     * @return JsonResponse
     * @author George
     */
    public function index(Request $request)
    {
        $size = $request->get('size', 15);
        $query = Invitation::query();
        $query->with('initiator');

        $query->when($initiator = $request->get('initiator'), function (Builder $query, $initiator) {
            return $query->where('initiator_id', $initiator);
        });

        $query->when($status = $request->get('status'), function (Builder $query, $status) {
            return $query->where('status', $status);
        });

        $query->when($account = $request->get('account'), function (Builder $query, $account) {
            return $query->where('account', 'like', "%$account%");
        });

        $query->orderByDesc('id');
        return success($query->paginate($size));
    }

    /**
     * Create invitation resource
     *
     * Date: 2020/5/11
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @author George
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'account' => ['required', 'email', 'unique:invitations']
        ]);
        $attributes = $validator->validate();
        $user = $request->user();
        $model = get_class($user);
        $attributes['initiator_id'] = $user->id;
        $attributes['initiator_type'] = Str::lower(class_basename($model));
        $expires = now()->addMinutes(30);
        $uri = URL::temporarySignedRoute('auth.register', $expires, [
            'account' => $attributes['account'],
            'initiator' => $attributes['initiator_id'],
            'initiator_type' => $attributes['initiator_type']
        ], false);
        $attributes['expires'] = $expires->getTimestamp();
        $attributes['signature'] = Str::afterLast($uri, 'signature=');
        $invitation = Invitation::create($attributes);
        $customer = new Customer(['email' => $attributes['account']]);
        $customer->notify(new RegisterInvitation([
            'inviter' => $user->name,
            'url' => url(str_replace('/api/auth', '', $uri))
        ]));
        return success($invitation);
    }

    /**
     * Get invitation resource by id
     *
     * Date: 2020/5/12
     * @param Invitation $invitation
     * @return JsonResponse
     * @author George
     */
    public function show(Invitation $invitation)
    {
        return success($invitation);
    }

    /**
     * Delete invitation resource by id
     *
     * Date: 2020/5/12
     * @param Invitation $invitation
     * @return JsonResponse
     * @throws Exception
     * @author George
     */
    public function destroy(Invitation $invitation)
    {
        $invitation->delete();
        return deleted();
    }
}
