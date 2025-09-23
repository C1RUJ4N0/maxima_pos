<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class CartController extends Controller
{
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = $request->session()->get('cart', []);
        if(isset($cart[$id])) $cart[$id]['quantity']++;
        else $cart[$id] = ['id'=>$id,'name'=>$product->name,'price'=>$product->price,'quantity'=>1];
        $request->session()->put('cart', $cart);
        return redirect()->back();
    }

    public function remove(Request $request, $id)
    {
        $cart = $request->session()->get('cart', []);
        if(isset($cart[$id])) unset($cart[$id]);
        $request->session()->put('cart', $cart);
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
        $quantity = (int)$request->input('quantity',1);
        $cart = $request->session()->get('cart', []);
        if(isset($cart[$id])) $cart[$id]['quantity'] = $quantity;
        $request->session()->put('cart', $cart);
        return redirect()->back();
    }

    public function clear(Request $request)
    {
        $request->session()->forget('cart');
        return redirect()->back();
    }
}
