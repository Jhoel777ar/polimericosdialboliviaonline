<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Venta;
use Illuminate\Support\Facades\Auth;
use App\Models\CodigoQR;
use App\Models\ComprobantePago;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;

class VentaController extends Controller
{
    public function verVentas()
    {
        try {
            $user = Auth::user();
            //encriptar datos
            $user->email = Crypt::encryptString($user->email);
            $user->name = Crypt::encryptString($user->name);
            $user->profile_photo_path = $user->profile_photo_path ? Crypt::encryptString($user->profile_photo_path) : null;
            $user->google_id = $user->google_id ? Crypt::encryptString($user->google_id) : null;
            $user->ci = $user->ci ? Crypt::encryptString($user->ci) : null;
            $user->telefono = $user->telefono ? Crypt::encryptString($user->telefono) : null;
            $user->password = $user->password ? Crypt::encryptString($user->password) : null;
            $user->adminArk = Crypt::encryptString($user->adminArk);
            $user->estado = Crypt::encryptString($user->estado);

            $ventas = Venta::where('ID_Usuario', $user->id)
                ->with(['detalles.producto.color', 'detalles.producto.categoria', 'envios', 'comprobante_pago'])
                ->orderBy('Fecha_Venta', 'desc')
                ->get();
            $fechaActual = now();
            $codigosQR = CodigoQR::where('fecha_expiracion', '>=', $fechaActual)
                ->orderBy('fecha_expiracion', 'desc')
                ->get();
            return Inertia::render('Ventas/VerVentas', [
                'ventas' => $ventas,
                'codigosQR' => $codigosQR
            ]);
        } catch (\Exception $e) {
            return Inertia::render('Ventas/VerVentas', [
                'ventas' => [],
                'codigosQR' => [],
                'error' => 'Error al cargar las ventas. Inténtalo de nuevo más tarde.'
            ]);
        }
    }

    public function guardar(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ID_Venta' => 'required|exists:ventas,id',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'monto_reportado' => 'required|numeric|min:0',
            'description' => 'nullable|string'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
        try {
            ComprobantePago::where('ID_Venta', $request->ID_Venta)
                ->update(['estado' => 'pendiente']);
            $imagePath = $request->file('image')->store('comprobantes_pago', 'public');
            ComprobantePago::create([
                'ID_Venta' => $request->ID_Venta,
                'image_url' => $imagePath,
                'monto_reportado' => $request->monto_reportado,
                'description' => $request->description,
                'estado' => 'pendiente',
                'subido' => true
            ]);
            return response()->json(['message' => 'Comprobante guardado correctamente'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al guardar el comprobante'], 500);
        }
    }
}
