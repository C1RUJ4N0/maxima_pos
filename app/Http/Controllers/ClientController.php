<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all();
        return view('clients', compact('clients'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ]);

        try {
            Client::create($validatedData);
            return response()->json(['success' => true, 'message' => 'Cliente creado con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error al crear el cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al crear el cliente.'], 500);
        }
    }

    public function update(Request $request, Client $client)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'phone_number' => 'nullable|string|max:255',
        ]);

        try {
            $client->update($validatedData);
            return response()->json(['success' => true, 'message' => 'Cliente actualizado con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error al actualizar el cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al actualizar el cliente.'], 500);
        }
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
            return response()->json(['success' => true, 'message' => 'Cliente eliminado con éxito.']);
        } catch (\Exception $e) {
            Log::error('Error al eliminar el cliente: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Error al eliminar el cliente.'], 500);
        }
    }
}