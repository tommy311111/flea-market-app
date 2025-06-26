<?php

namespace App\Http\Controllers\Item;

use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        Comment::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'body'    => $request->input('body'),
        ]);

        return redirect()->back()->with('success', 'コメントを投稿しました。');
    }
}
