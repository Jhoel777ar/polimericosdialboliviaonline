<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Factura</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .title {
            font-size: 22px;
            color: #1a73e8;
        }

        .details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .details div {
            width: 48%;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.05);
        }

        .details p {
            margin: 8px 0;
            font-size: 14px;
            line-height: 1.8;
        }

        .details p strong {
            color: #333;
        }

        .content {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .content th,
        .content td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .content th {
            background-color: #1a73e8;
            color: #fff;
            font-size: 16px;
        }

        .content td {
            background-color: #fff;
            font-size: 14px;
        }

        .content td,
        .content th {
            text-align: center;
        }

        .totals {
            display: flex;
            justify-content: flex-end;
            font-size: 18px;
            font-weight: bold;
            margin-top: 20px;
            border-top: 2px solid #ddd;
            padding-top: 15px;
        }

        .totals p {
            margin: 5px 20px;
            padding: 8px 15px;
            background-color: #f1f1f1;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 40px;
            color: #999;
        }

        .footer p {
            margin: 10px 0;
        }

        .page-break {
            page-break-before: always;
        }

        .no-break {
            page-break-inside: avoid;
        }

        .unique-code {
            font-size: 12px;
            position: absolute;
            color: #999;
            font-weight: bold;
        }

        @media print {
            body {
                margin: 0;
            }

            .container {
                max-width: 100%;
                padding: 15px;
                margin: 10px;
            }

            .content th,
            .content td {
                padding: 8px;
            }

            .totals {
                font-size: 14px;
                text-align: left;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div
            style="text-align: center; padding: 15px; background-color: #ffcccc; color: #cc0000; font-size: 16px; font-weight: bold; border: 2px solid #cc0000; border-radius: 8px; margin: 20px 0;">
            IMPORTANTE: Debe subir su comprobante de pago. Si no lo hace, esta nota de venta será inválida.
        </div>
        <div class="header">
            <div class="company-name">Poliméricos Dial Bolivia</div>
            <div class="title">Nota de PreVenta</div>
        </div>
        <div class="details">
            <div>
                <p><strong>Venta ID:</strong> {{ $venta_id }}</p>
                <p><strong>Fecha de Venta:</strong> {{ $fecha_venta }}</p>
                <p><strong>Cliente:</strong> {{ $usuario->name }}</p>
                <p><strong>Email:</strong> {{ $usuario->email }}</p>
                <p><strong>CI:</strong> {{ $usuario->ci }}</p>
                <p><strong>Teléfono:</strong> {{ $usuario->telefono }}</p>
            </div>
            <div>
                <p><strong>Dirección de Envío:</strong> {{ $direccion }}</p>
                <p><strong>Estado del Envío:</strong> {{ $estado_envio }}</p>
                <p><strong>Tipo de Entrega:</strong> {{ $tipo_entrega }}</p>
                <p><strong>Estado de venta:</strong> {{ $estado }}</p>
            </div>
        </div>
        <table class="content">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($productos as $detalle)
                    <tr class="no-break">
                        <td>{{ $detalle->producto->nombre }}</td>
                        <td>{{ $detalle->cantidad }}</td>
                        <td>Bs. {{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td>Bs. {{ number_format($detalle->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="totals">
            <p><strong>Total con Descuento Aplicado:</strong> Bs. {{ number_format($venta->Totalcondescuento, 2) }}
            </p>
            <p><strong>Total sin Descuento:</strong> Bs. {{ number_format($venta->Total, 2) }}</p>
        </div>
        <div class="unique-code">
            <p>Código Único de Venta: {{ $codigo_unico }}</p>
        </div>
        <div class="footer">
            <p>Gracias por su compra. ¡Nos vemos pronto!</p>
            <p>Su compra esta vigente durante una semana sin no cancela o sube el comprobante de pago se eliminara su
                compra.</p>
        </div>
        <div class="comprobantes">
            <h3>Comprobantes de Pago</h3>
            @if ($comprobantes->isEmpty())
                <p style="color: red; font-weight: bold;">No se ha subido ningún comprobante de pago.</p>
            @else
                @foreach ($comprobantes as $comprobante)
                    <div class="comprobante-item"
                        style="margin-bottom: 20px; padding: 10px; border: 1px solid #ddd; border-radius: 8px;">
                        <p><strong>Fecha de Subida:</strong> {{ $comprobante->Fecha_Subida }}</p>
                        <p><strong>Monto Reportado:</strong> Bs. {{ number_format($comprobante->monto_reportado, 2) }}
                        </p>
                        <p><strong>Estado:</strong>
                            @if ($comprobante->estado == 'pendiente')
                                <span style="color: orange;">Pendiente</span>
                            @elseif($comprobante->estado == 'aprobado')
                                <span style="color: green;">Aprobado</span>
                            @else
                                <span style="color: red;">Rechazado</span>
                            @endif
                        </p>

                        @if ($comprobante->image_url)
                            <div style="text-align: center;">
                                <img src="{{ public_path('storage/' . $comprobante->image_url) }}"
                                    alt="Comprobante de Pago"
                                    style="width: 700px; height: auto; border-radius: 8px; box-shadow: 2px 2px 5px rgba(0,0,0,0.1);">
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
    <div class="page-break"></div>
</body>

</html>
