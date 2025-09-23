<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Client;
use App\Models\Venta;
use App\Models\VentaItem;
use App\Models\Apartado;
use App\Models\ApartadoItem;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $products = Product::all();
        $clients = Client::all();

        return view('dashboard', compact('products', 'clients'));
    }

    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "price" => $product->price,
                "quantity" => 1
            ];
        }

        session()->put('cart', $cart);
        return response()->json(['cart' => $cart]);
    }

    public function removeFromCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }
        return response()->json(['cart' => $cart]);
    }

    public function updateCart(Request $request, $id)
    {
        $cart = session()->get('cart', []);
        $quantity = (int) $request->input('quantity', 0);
        if(isset($cart[$id]) && $quantity > 0) {
            $cart[$id]['quantity'] = $quantity;
            session()->put('cart', $cart);
        }
        return response()->json(['cart' => $cart]);
    }

    public function clearCart()
    {
        session()->forget('cart');
        return response()->json(['message' => 'Carrito limpiado']);
    }

    public function createVenta(Request $request)
    {
        $cart = session()->get('cart', []);
        if(empty($cart)) {
            return back()->with('error', 'El carrito está vacío.');
        }

        DB::transaction(function() use ($request, $cart) {
            $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            $venta = Venta::create([
                'client_name' => $request->client_name,
                'total_amount' => $total,
            ]);

            foreach($cart as $productId => $item) {
                VentaItem::create([
                    'venta_id' => $venta->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $product = Product::find($productId);
                $product->decrement('stock', $item['quantity']);
            }

            session()->forget('cart');
        });

        return back()->with('success', 'Venta realizada correctamente.');
    }

    public function createApartado(Request $request)
    {
        $cart = session()->get('cart', []);
        if(empty($cart)) {
            return back()->with('error', 'El carrito está vacío.');
        }

        DB::transaction(function() use ($request, $cart) {
            $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            $apartado = Apartado::create([
                'client_name' => $request->client_name,
                'total_amount' => $total,
                'amount_paid' => $request->amount_paid ?? 0,
                'status' => 'vigente',
            ]);

            foreach($cart as $productId => $item) {
                ApartadoItem::create([
                    'apartado_id' => $apartado->id,
                    'product_id' => $productId,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }

            session()->forget('cart');
        });

        return back()->with('success', 'Apartado creado correctamente.');
    }
}