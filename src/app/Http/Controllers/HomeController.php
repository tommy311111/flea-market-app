<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;

class HomeController extends Controller
{
    public function index()
{
    $items = Item::all();

    return view('public.index', compact('items'));

}

}
