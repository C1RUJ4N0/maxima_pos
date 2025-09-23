<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Provider;

class ProvidersController extends Controller
{
    public function index()
    {
        $proveedores = Provider::all();
        return view('providers', compact('proveedores'));
    }
}