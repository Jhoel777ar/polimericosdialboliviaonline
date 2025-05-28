<?php

namespace App\Filament\Resources\VentaResource\Pages;

use App\Filament\Resources\VentaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\VentaDetalle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use Exception;

class CreateVenta extends CreateRecord
{
    protected static string $resource = VentaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return DB::transaction(function () use ($data) {
                $producto = Producto::find($data['ID_Producto']);
                if (!$producto || $producto->stock < $data['Cantidad']) {
                    $this->sendErrorNotification('Error en la venta', 'No hay suficiente stock disponible para este producto.');
                    throw new Exception('Stock insuficiente.');
                }
                $venta = Venta::create([
                    'ID_Usuario' => $data['ID_Usuario'],
                    'Cantidad' => $data['Cantidad'],
                    'Total' => $data['Total'],
                    'Estado' => $data['Estado'],
                    'Método_Pago' => $data['Método_Pago'],
                    'tipo_entrega' => $data['tipo_entrega'],
                ]);
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $data['ID_Producto'],
                    'cantidad' => $data['Cantidad'],
                    'precio_unitario' => $data['Precio_Unitario'],
                ]);
                $producto->decrement('stock', $data['Cantidad']);
                return $venta;
            });
        } catch (Exception $e) {
            $this->sendErrorNotification('Error en la venta', 'Ocurrió un error inesperado al procesar la venta.');
            throw $e;
        }
    }
    private function sendErrorNotification(string $title, string $message): void
    {
        Notification::make()
            ->title($title)
            ->body($message)
            ->danger()
            ->send();
    }
}
