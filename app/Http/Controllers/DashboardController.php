<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Añadimos el título para que el layout de app lo detecte
        return view('pages.dashboard.ecommerce', ['title' => 'Inicio | InvenCoo']);
    }
}