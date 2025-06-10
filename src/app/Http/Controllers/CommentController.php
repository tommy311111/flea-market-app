<?php

namespace App\Http\Controllers;
use App\Models\Item;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        // コメントを保存
        Comment::create([
            'item_id'  => $item->id,
            'user_id'  => Auth::id(),
            'body'     => $request->input('body'),
        ]);

        return redirect()->back()->with('success', 'コメントを投稿しました。');
    }
}
