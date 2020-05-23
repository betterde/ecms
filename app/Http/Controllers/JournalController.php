<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $size = $request->get('size', 15);
        $sort = $request->get('sort', 'id');
        $descend = (boolean)$request->get('descend', false);

        $query = Journal::query();
        $query->with(['journalable' => function (MorphTo $query) {
            $query->select(['id', 'name']);
        }]);

        $query->when($user_type = $request->get('user_type'), function (Builder $builder) use ($user_type) {
            $builder->where('user_type', $user_type);
        });

        $query->when($user_id = $request->get('user_id'), function (Builder $builder) use ($user_id) {
            $builder->where('user_id', $user_id);
        });

        $query->when($date = $request->get('date'), function (Builder $builder) use ($date) {
            $builder->whereDate('created_at', $date);
        });

        $query->when($action = $request->get('action'), function (Builder $builder) use ($action) {
            $builder->where('action', $action);
        });

        if ($descend) {
            $query->orderByDesc($sort);
        } else{
            $query->orderBy($sort);
        }

        return success($query->paginate($size));
    }

    public function show(Journal $journal)
    {
        $journal->journalable = $journal->journalable()->first();
        return success($journal);
    }
}
