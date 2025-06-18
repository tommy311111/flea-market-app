<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExhibitionRequest;
use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'recommend'); // 'recommend' or 'mylist'
        $user = Auth::user();

        if ($page === 'mylist' && $user) {
            // ログインユーザーが「いいね」した商品だけ表示
            // ここではuser->likes()リレーションがあると仮定
            $items = $user->likedItems()->get();  
        } elseif ($page === 'recommend') {
            if ($user) {
                // ログインユーザーが出品した商品は除外して取得
                $items = Item::where('user_id', '!=', $user->id)->get();
            } else {
                // ログインしていなければ全商品取得
                $items = Item::get();
            }
        } else {
            // マイリストタブかつ未ログインの場合は空の結果
            $items = collect([]); // 空コレクション
        }

        return view('public.index', compact('user','items', 'page'));
    }

    // 商品詳細表示
    public function show($id)
    {
        $item = Item::with(['user', 'comments.user.profile', 'categories'])->withCount('comments','likes')->findOrFail($id);
        $liked = Auth::check() ? Auth::user()->likes()->where('item_id', $id)
        ->whereNull('deleted_at')
        ->exists() : false;

        return view('items.detail', compact('item', 'liked'));
    }

    // 出品画面表示
    public function create()
    {
        $categories = Category::all();
        $conditions = Item::CONDITIONS;
        return view('items.sell', compact('categories','conditions'));
    }

    // 出品処理：ExhibitionRequest を使う
    public function store(ExhibitionRequest $request)
    {
        // 元のファイル名を取得
    $originalName = $request->file('image')->getClientOriginalName();
    // 空白や「+」を「_」に置換
    $safeName = str_replace([' ', '+'], '_', $originalName);

    // ファイルを指定した名前で保存
    $path = $request->file('image')->storeAs('public/images/items', $safeName);

        $item = new Item();
        $item->user_id = Auth::id();
        $item->name = $request->name;
        $item->description = $request->description;
        $item->price = $request->price;
        $item->image = $safeName;  // 変更した安全なファイル名を保存
        $item->condition = $request->condition;
        $item->save();

        $item->categories()->sync($request->categories);

        return redirect('/')->with('success', '商品を出品しました。');
    }
}
