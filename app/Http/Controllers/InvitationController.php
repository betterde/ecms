<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->get('size', 15);
        $query = Invitation::query();
        $query->with('initiator');

        $query->when($initiator = $request->get('initiator'), function (Builder $query, $initiator) {
            return $query->when('initiator_id', $initiator);
        });

        $query->when($account = $request->get('account'), function (Builder $query, $account) {
            return $query->where('account', 'like', "%$account%");
        });

        $query->orderByDesc('id');
        return success($query->paginate($size));
    }

    /**
     * Date: 2020/5/11
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     * @author George
     */
    public function store(Request $request)
    {
        $attributes = $this->validate($request, [
            'mode' => 'required|in:mobile,email',
            'account' => 'required',
            'expires' => 'required|integer',
        ]);

        $user = $request->user();
        $model = get_class($user);
        $attributes['initiator_id'] = $user->id;
        $attributes['initiator_type'] = Str::lower(class_basename($model));
        $expires = now()->addMinutes($attributes['expires']);
        $url = URL::temporarySignedRoute('auth.register', $expires, [
            'account' => $attributes['account'],
            'initiator' => $attributes['initiator_id'],
            'initiator_type' => $attributes['initiator_type']
        ], false);
        $attributes['expires'] = $expires->getTimestamp();
        $attributes['signature'] = Str::afterLast($url, 'signature=');
        $invitation = Invitation::create($attributes);
        return success($invitation);
    }

    public function show()
    {

    }

    public function destroy()
    {

    }
}
