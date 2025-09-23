<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;

abstract class Controller
{
    //
}

namespace App\Http\Controllers;



class ViewController extends Controller
{
    public function showStatistics()
    {
        return view('statistics');
    }

    public function showInventory()
    {
        return view('inventory');
    }

    public function showProviders()
    {
        return view('providers');
    }

    public function showApartados()
    {
        return view('apartados');
    }
}
