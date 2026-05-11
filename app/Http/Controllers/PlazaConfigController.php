<?php

namespace App\Http\Controllers;

use App\Models\PlazaConfig;
use Illuminate\Http\Request;

class PlazaConfigController extends Controller
{
    /**
     * Muestra el resumen de la configuración del perímetro
     * Apunta a: resources\views\admin\GPS\index.blade.php
     */
    public function index()
    {
        $config = PlazaConfig::first() ?? new PlazaConfig([
            'latitude' => 0,
            'longitude' => 0,
            'radius_meters' => 100
        ]);
        
        return view('admin.GPS.index', compact('config'));
    }

    /**
     * Muestra el formulario para editar el perímetro
     * Apunta a: resources\views\admin\GPS\plaza_perimeter.blade.php
     */
    public function edit()
    {
        // Busca el único registro de configuración o crea una instancia vacía
        $config = PlazaConfig::first() ?? new PlazaConfig([
            'latitude' => 0,
            'longitude' => 0,
            'radius_meters' => 100
        ]);
        
        return view('admin.GPS.plaza_perimeter', compact('config'));
    }

    /**
     * Actualiza los datos de la plaza
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius_meters' => 'required|integer|min:1',
        ]);

        // Actualiza el registro con ID 1 o lo crea
        PlazaConfig::updateOrCreate(['plaza_config_id' => 1], $validated);

        return back()->with('success', '✓ Perímetro de seguridad actualizado correctamente.');
    }
}