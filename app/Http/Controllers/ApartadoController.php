<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\Client;
use App\Models\ApartadoItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApartadoController extends Controller
{
    public function index()
    {
        $apartados = Apartado::with('client')->orderBy('due_date')->get();
        $clients = Client::all();
        $apartadoItems = ApartadoItem::all();
        $products = Product::all();

        return view('apartados.index', compact('apartados', 'clients', 'apartadoItems', 'products'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'total_amount' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $apartado = Apartado::create([
                'client_id' => $validatedData['client_id'],
                'total_amount' => $validatedData['total_amount'],
                'amount_paid' => $validatedData['amount_paid'],
                'due_date' => $validatedData['due_date'],
                'status' => 'vigente',
            ]);

            foreach ($validatedData['items'] as $item) {
                ApartadoItem::create([
                    'apartado_id' => $apartado->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);

                $producto = Product::find($item['product_id']);
                $producto->stock -= $item['quantity'];
                $producto->save();
            }

            DB::commit();

            return redirect()->route('apartados.index')->with('success', 'Apartado creado con éxito.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Hubo un error al crear el apartado: ' . $e->getMessage());
        }
    }
}