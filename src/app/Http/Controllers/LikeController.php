<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle($id)
    {
        $user = Auth::user();

        $like = Like::withTrashed()
            ->where('user_id', $user->id)
            ->where('item_id', $id)
            ->first();

        if ($like) {
            if ($like->trashed()) {
                $like->restore(); // いいね復元
            } else {
                $like->delete(); // いいね解除
            }
        } else {
            Like::create([
                'user_id' => $user->id,
                'item_id' => $id,
            ]);
        }

        return redirect()->route('items.show', ['item' => $id]);

    }
}
