<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venta;
use App\Models\User;
use App\Models\Producto;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ComprobantePago;

class FacturaController extends Controller
{
     public function generarFactura($id)
    {
        try {
            $venta = Venta::with(['usuario', 'detalles.producto', 'envios'])->findOrFail($id);
            if (!$venta || !$venta->usuario || $venta->detalles->isEmpty()) {
                return response()->json(['error' => 'Datos incompletos para generar la factura'], 404);
            }
            $comprobantes = ComprobantePago::where('ID_Venta', $venta->id)->get();
            $usuario = $venta->usuario;
            $direccion = optional($venta->envios)->Dirrecion ?? 'Recogida en tienda';
            $estado_envio = optional($venta->envios)->Estado_Envio ?? 'No aplica';
            $descuento = $venta->Totalcondescuento;
            $total = $venta->Total;
            $estado = $venta->Estado;
            $productos = $venta->detalles;
            $tipo_entrega = $venta->tipo_entrega;
            $fecha_venta = $venta->Fecha_Venta;
            $venta_id = $venta->id;
            $codigo_unico = uniqid('venta-', true);
            if (!view()->exists('pdf.factura')) {
                return response()->json(['error' => 'Vista de factura no encontrada'], 500);
            }
            $pdf = Pdf::loadView('pdf.factura', compact('venta', 'usuario', 'direccion', 'estado_envio', 'productos', 'descuento', 'total', 'estado', 'tipo_entrega', 'fecha_venta', 'venta_id', 'codigo_unico', 'comprobantes'));
            return response()->json([
                'pdf' => base64_encode($pdf->output())
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'detalle' => $e->getMessage()], 500);
        }
    }

    public function generarFacturaFilament($id)
    {
        try {
            $venta = Venta::with(['usuario', 'detalles.producto', 'envios'])->findOrFail($id);
            if (!$venta || !$venta->usuario || $venta->detalles->isEmpty()) {
                return response()->json(['error' => 'Datos incompletos para generar la factura'], 404);
            }
            $comprobantes = ComprobantePago::where('ID_Venta', $venta->id)->get();
            $usuario = $venta->usuario;
            $direccion = optional($venta->envios)->Direccion ?? 'Recogida en tienda';
            $estado_envio = optional($venta->envios)->Estado_Envio ?? 'No aplica';
            $descuento = $venta->Totalcondescuento;
            $total = $venta->Total;
            $estado = $venta->Estado;
            $productos = $venta->detalles;
            $tipo_entrega = $venta->tipo_entrega;
            $fecha_venta = $venta->Fecha_Venta;
            $venta_id = $venta->id;
            $codigo_unico = uniqid('venta-', true);

            if (!view()->exists('pdf.factura')) {
                return response()->json(['error' => 'Vista de factura no encontrada'], 500);
            }
            $pdf = Pdf::loadView('pdf.factura', compact('venta', 'usuario', 'direccion', 'estado_envio', 'productos', 'descuento', 'total', 'estado', 'tipo_entrega', 'fecha_venta', 'venta_id', 'codigo_unico', 'comprobantes'));
            return $pdf->download("factura_{$venta_id}.pdf");
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error interno del servidor', 'detalle' => $e->getMessage()], 500);
        }
    }
}
