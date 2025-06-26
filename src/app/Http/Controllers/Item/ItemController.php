<?php

namespace App\Http\Controllers\Item;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExhibitionRequest;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $page = $request->query('page', 'recommend');
        $keyword = $request->input('keyword');
        $user = Auth::user();

        $items = Item::search($keyword, $page, $user);

        return view('public.index', compact('items', 'page', 'keyword'));
    }

    public function show($id)
    {
        $item = Item::with(['user', 'comments.user.profile', 'categories'])
                    ->withCount('comments', 'likes')
                    ->findOrFail($id);

        $liked = Auth::check()
            ? Auth::user()->likes()->where('item_id', $id)->whereNull('deleted_at')->exists()
            : false;

        return view('items.detail', compact('item', 'liked'));
    }

    public function create()
    {
        $categories = Category::all();
        $conditions = Item::CONDITIONS;

        return view('items.sell', compact('categories', 'conditions'));
    }

    public function store(ExhibitionRequest $request)
    {
        $originalName = $request->file('image')->getClientOriginalName();
        $safeName = str_replace([' ', '+'], '_', $originalName);
        $path = $request->file('image')->storeAs('public/images/items', $safeName);

        $item = new Item();
        $item->user_id = Auth::id();
        $item->name = $request->name;
        $item->description = $request->description;
        $item->brand_name = $request->brand_name;
        $item->price = $request->price;
        $item->image = $safeName;
        $item->condition = $request->condition;
        $item->save();

        $item->categories()->sync($request->category);

        return redirect('/')->with('success', '商品を出品しました。');
    }
}
