<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Models\Soporte;

class SoporteController extends Controller
{
    public function index()
    {
        $soportes = Soporte::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();
        $puedeCrearSoporte = $soportes->isEmpty();
        return Inertia::render('Soporte/Soporte', [
            'soportes' => $soportes,
            'puedeCrearSoporte' => $puedeCrearSoporte
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:150',
            'description' => 'required|string',
        ]);
        try {
            Soporte::create([
                'user_id' => Auth::id(),
                'subject' => $request->subject,
                'description' => $request->description,
            ]);
            return redirect()->route('soporte.index')->with('success', 'Soporte creado correctamente');
        } catch (\Exception $e) {
            return redirect()->route('soporte.index')->with('error', 'Error al crear el soporte');
        }
    }
    public function update(Request $request, $id)
    {
        $soporte = Soporte::findOrFail($id);
        if ($soporte->user_id !== Auth::id()) {
            return redirect()->route('soporte.index')->with('error', 'No tienes permisos para editar este soporte');
        }
        try {
            $soporte->update([
                'subject' => $request->subject,
                'description' => $request->description,
                'status' => 'pending',
            ]);
            return redirect()->route('soporte.index')->with('success', 'Soporte actualizado');
        } catch (\Exception $e) {
            return redirect()->route('soporte.index')->with('error', 'Error al actualizar el soporte');
        }
    }
    public function destroy($id)
    {
        $soporte = Soporte::findOrFail($id);
        if ($soporte->user_id !== Auth::id()) {
            return redirect()->route('soporte.index')->with('error', 'No tienes permisos para eliminar este soporte');
        }
        try {
            $soporte->delete();
            return redirect()->route('soporte.index')->with('success', 'Soporte eliminado');
        } catch (\Exception $e) {
            return redirect()->route('soporte.index')->with('error', 'Error al eliminar el soporte');
        }
    }
}
