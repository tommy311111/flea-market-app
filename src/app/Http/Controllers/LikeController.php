<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggle(Request $request, Item $item)
    {
        $likedItems = $request->cookie('liked_items', ''); // Cookieから取得（カンマ区切り）

        $likedArray = $likedItems === '' ? [] : explode(',', $likedItems);

        if (in_array($item->id, $likedArray)) {
            // いいね解除
            $likedArray = array_diff($likedArray, [$item->id]);
        } else {
            // いいね追加
            $likedArray[] = $item->id;
        }

        $newLikedItems = implode(',', $likedArray);

        // Cookieの有効期限は例えば30日
        return redirect()->back()->withCookie(cookie('liked_items', $newLikedItems, 60*24*30));
    }
}
