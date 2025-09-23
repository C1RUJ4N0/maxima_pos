<?php

namespace App\Http\Controllers;

use App\Models\Apartado;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\ItemVenta;
use App\Models\ItemApartado;
use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class VentaController extends Controller
{
    public function getDashboardData()
    {
        try {
            $productos = Producto::all();
            $clientes = Cliente::all();

            return response()->json([
                'productos' => $productos,
                'clientes' => $clientes
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching dashboard data: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los datos.', 'error' => $e->getMessage()], 500);
        }
    }

    public function storeProducto(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'descripcion' => 'nullable|string',
            ]);

            $producto = Producto::create($request->all());

            DB::commit();
            return response()->json(['message' => 'Producto creado con éxito.', 'producto' => $producto], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating product: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear el producto.', 'error' => $e->getMessage()], 500);
        }
    }

    public function updateProducto(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'nombre' => 'required|string|max:255',
                'precio' => 'required|numeric|min:0',
                'stock' => 'required|integer|min:0',
                'descripcion' => 'nullable|string',
            ]);

            $producto = Producto::findOrFail($id);
            $producto->update($request->all());

            DB::commit();
            return response()->json(['message' => 'Producto actualizado con éxito.', 'producto' => $producto], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating product: ' . $e->getMessage());
            return response()->json(['message' => 'Error al actualizar el producto.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroyProducto($id)
    {
        try {
            DB::beginTransaction();
            $producto = Producto::findOrFail($id);
            $producto->delete();
            DB::commit();
            return response()->json(['message' => 'Producto eliminado con éxito.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting product: ' . $e->getMessage());
            return response()->json(['message' => 'Error al eliminar el producto.', 'error' => $e->getMessage()], 500);
        }
    }

    public function storeVenta(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'items' => 'required|array',
                'items.*.producto_id' => 'required|exists:productos,id',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.precio' => 'required|numeric|min:0',
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            $venta = Venta::create([
                'cliente_id' => $request->cliente_id,
                'monto_total' => $total,
            ]);

            foreach ($request->items as $item) {
                ItemVenta::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                ]);

                $producto = Producto::find($item['producto_id']);
                $producto->stock -= $item['cantidad'];
                $producto->save();
            }

            DB::commit();
            return response()->json(['message' => 'Venta creada con éxito.', 'venta' => $venta], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating sale: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear la venta.', 'error' => $e->getMessage()], 500);
        }
    }

    public function storeApartado(Request $request)
    {
        try {
            DB::beginTransaction();
            $request->validate([
                'cliente_id' => 'required|exists:clientes,id',
                'items' => 'required|array',
                'items.*.producto_id' => 'required|exists:productos,id',
                'items.*.cantidad' => 'required|integer|min:1',
                'items.*.precio' => 'required|numeric|min:0',
            ]);

            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['cantidad'] * $item['precio'];
            }

            $apartado = Apartado::create([
                'cliente_id' => $request->cliente_id,
                'monto_total' => $total,
                'fecha_vencimiento' => Carbon::now()->addDays(7),
                'estado' => 'vigente',
            ]);

            foreach ($request->items as $item) {
                ItemApartado::create([
                    'apartado_id' => $apartado->id,
                    'producto_id' => $item['producto_id'],
                    'cantidad' => $item['cantidad'],
                    'precio' => $item['precio'],
                ]);
            }

            DB::commit();
            return response()->json(['message' => 'Apartado creado con éxito.', 'apartado' => $apartado], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating apartado: ' . $e->getMessage());
            return response()->json(['message' => 'Error al crear el apartado.', 'error' => $e->getMessage()], 500);
        }
    }

    public function getApartados()
    {
        try {
            $apartados = Apartado::with('cliente')->get()->map(function($apartado) {
                return [
                    'nombre_cliente' => $apartado->cliente->nombre,
                    'telefono' => $apartado->cliente->numero_telefono,
                    'monto' => $apartado->monto_total,
                    'fecha_vencimiento' => $apartado->fecha_vencimiento,
                    'estado' => $apartado->estado
                ];
            });
            return response()->json(['apartados' => $apartados]);
        } catch (\Exception $e) {
            Log::error('Error fetching apartados: ' . $e->getMessage());
            return response()->json(['message' => 'Error al obtener los apartados.'], 500);
        }
    }
}