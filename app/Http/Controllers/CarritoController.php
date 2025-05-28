<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Auth;
use App\Models\Carrito;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Exception;
use App\Models\Venta;
use App\Models\VentaDetalle;
use App\Models\Envio;
use App\Models\Cupon;
use App\Models\CodigoQR;

class CarritoController extends Controller
{
    public function agregarAlCarrito($id, Request $request)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);
        $producto = Producto::find($id);
        if (!$producto) {
            return response()->json(['error' => 'Producto no encontrado.'], 404);
        }
        if ($producto->activo == 0) {
            return response()->json(['error' => 'Producto inhabilitado.'], 400);
        }
        if ($producto->stock == 0) {
            return response()->json(['error' => 'Producto agotado.'], 400);
        }
        if ($request->cantidad > $producto->stock) {
            return response()->json(['error' => 'Cantidad excede el stock disponible.'], 400);
        }
        if (Auth::check()) {
            $usuario = Auth::user();
            $carritoItem = Carrito::firstOrCreate(
                ['ID_Usuario' => $usuario->id, 'ID_Producto' => $id],
                ['Cantidad' => 0]
            );
            $nuevaCantidad = $carritoItem->Cantidad + $request->cantidad;
            if ($nuevaCantidad > $producto->stock) {
                return response()->json(['error' => 'Cantidad total en el carrito excede el stock disponible.'], 400);
            }
            $carritoItem->Cantidad = $nuevaCantidad;
            $carritoItem->save();
        } else {
            $carrito = session()->get('carrito', []);
            $nuevaCantidad = ($carrito[$id]['cantidad'] ?? 0) + $request->cantidad;

            if ($nuevaCantidad > $producto->stock) {
                return response()->json(['error' => 'Cantidad total en el carrito excede el stock disponible.'], 400);
            }
            $carrito[$id] = [
                'id' => $producto->id,
                'nombre' => $producto->nombre,
                'descripcion' => $producto->descripcion,
                'precio' => $producto->precio,
                'imagen_url' => $producto->imagen_url,
                'cantidad' => $nuevaCantidad,
                'stock' => $producto->stock,
            ];

            session()->put('carrito', $carrito);
        }
        return response()->json(['success' => 'Producto agregado al carrito exitosamente.', 'redirect_url' => route('carrito.ver')]);
    }

    public function eliminarDelCarrito($id)
    {
        if (Auth::check()) {
            Carrito::where('ID_Usuario', Auth::id())->where('ID_Producto', $id)->delete();
        } else {
            $carrito = session()->get('carrito', []);
            unset($carrito[$id]);
            session()->put('carrito', $carrito);
        }
        return response()->json(['success' => true]);
    }

    public function verCarrito()
    {
        $usuario = Auth::user();
        $carrito = [];
        $fechaActual = now();
        $codigosQR = CodigoQR::where('fecha_expiracion', '>=', $fechaActual)
            ->orderBy('fecha_expiracion', 'desc')
            ->get();
        if ($usuario) {
            $carritoItems = Carrito::with('producto')
                ->where('ID_Usuario', $usuario->id)
                ->get();
            foreach ($carritoItems as $item) {
                $producto = $item->producto;
                if (!$producto->activo || $producto->stock === 0) {
                    $item->delete();
                    continue;
                }
                $item->Cantidad = min($item->Cantidad, $producto->stock);
                $item->save();
                $carrito[$item->ID_Producto] = [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio,
                    'imagen_url' => $producto->imagen_url,
                    'cantidad' => $item->Cantidad,
                    'stock' => $producto->stock,
                    'activo' => $producto->activo,
                ];
            }
        } else {
            $sessionCarrito = session()->get('carrito', []);
            foreach ($sessionCarrito as $id => $item) {
                $producto = Producto::find($id);
                if (!$producto || !$producto->activo || $producto->stock === 0) {
                    unset($sessionCarrito[$id]);
                    continue;
                }
                $sessionCarrito[$id] = [
                    'id' => $producto->id,
                    'nombre' => $producto->nombre,
                    'descripcion' => $producto->descripcion,
                    'precio' => $producto->precio,
                    'imagen_url' => $producto->imagen_url,
                    'cantidad' => min($item['cantidad'], $producto->stock),
                    'stock' => $producto->stock,
                    'activo' => $producto->activo,
                ];
            }
            session()->put('carrito', $sessionCarrito);
            $carrito = $sessionCarrito;
        }
        return inertia('Ventas/Carrito', [
            'carrito' => $carrito,
            'usuario' => $usuario,
            'codigosQR' => $codigosQR,
        ]);
    }

    public function actualizarCantidad(Request $request, $id)
    {
        $request->validate([
            'cantidad' => 'required|integer|min:1',
        ]);
        $producto = Producto::find($id);
        if (!$producto || !$producto->activo || $producto->stock === 0) {
            return response()->json(['error' => 'El producto no está disponible.'], 400);
        }
        if ($request->cantidad > $producto->stock) {
            return response()->json(['error' => 'Cantidad excede el stock disponible.'], 400);
        }
        if (Auth::check()) {
            $usuario = Auth::user();
            $carritoItem = Carrito::where('ID_Usuario', $usuario->id)->where('ID_Producto', $id)->first();

            if ($carritoItem) {
                $carritoItem->Cantidad = $request->cantidad;
                $carritoItem->save();
            }
        } else {
            $carrito = session()->get('carrito', []);
            if (isset($carrito[$id])) {
                $carrito[$id]['cantidad'] = $request->cantidad;
                session()->put('carrito', $carrito);
            }
        }
        return response()->json(['success' => true]);
    }

    //nuevo 

    public function validarStock(Request $request)
    {
        try {
            $request->validate([
                'productos' => 'required|array|min:1',
                'productos.*.id' => 'required|integer|exists:productos,id',
                'productos.*.cantidad' => 'required|integer|min:1',
                'productos.*.precio' => 'required|numeric|min:0',
                'direccion' => 'required_if:tipo_entrega,envio|nullable|string|max:200',
            ]);
            $productos = $request->input('productos');
            $direccionEnvio = $request->input('direccion');
            $totalConDescuento = $request->input('totalConDescuento');
            $cuponId = $request->input('cuponId');
            $tipoEntrega = $request->input('tipo_entrega');
            $ids = array_column($productos, 'id');
            $productosDb = Producto::whereIn('id', $ids)
                ->get(['id', 'nombre', 'stock', 'precio', 'activo']);
            $errores = [];
            foreach ($productos as $producto) {
                $productoDb = $productosDb->firstWhere('id', $producto['id']);

                if (!$productoDb) {
                    $errores[] = 'Producto con ID ' . $producto['id'] . ' no encontrado.';
                    continue;
                }
                if (!$productoDb->activo) {
                    $errores[] = 'El producto ' . $productoDb->nombre . ' está deshabilitado.';
                    continue;
                }
                if ($productoDb->stock < $producto['cantidad']) {
                    $errores[] = 'No hay suficiente stock para ' . $productoDb->nombre . '.';
                    continue;
                }
                if ($productoDb->precio != $producto['precio']) {
                    $errores[] = 'El precio del producto ' . $productoDb->nombre . ' ha cambiado. Precio actual: Bs. ' . $productoDb->precio . '.';
                    continue;
                }
            }
            if (!empty($errores)) {
                return response()->json(['message' => $errores], 400);
            }
            if (Auth::check()) {
                $usuario = Auth::user();
                $data = $request->validate([
                    'nombre' => 'required|string|max:255',
                    'ci' => 'required|string|unique:users,ci,' . $usuario->id,
                    'telefono' => 'required|string',
                ]);
                /** @var \App\Models\User $usuario */
                $usuario->update([
                    'name' => $data['nombre'],
                    'ci' => $data['ci'],
                    'telefono' => $data['telefono'],
                ]);
                // Aquí llamamos a la función hacerVenta
                $ventaResponse = $this->hacerVenta($productos, $usuario, $direccionEnvio, $totalConDescuento, $cuponId, $tipoEntrega);
                if ($ventaResponse) {
                    return response()->json(['venta_id' => $ventaResponse->id], 201);
                } else {
                    return response()->json(['message' => 'Error al realizar la venta.'], 400);
                }
            } else {
                $data = $request->validate([
                    'email' => 'required|email|unique:users,email',
                    'nombre' => 'required|string|max:255',
                    'ci' => 'required|string|unique:users,ci',
                    'telefono' => 'required|string',
                ]);
                $user = User::create([
                    'email' => $data['email'],
                    'name' => $data['nombre'],
                    'ci' => $data['ci'],
                    'telefono' => $data['telefono'],
                    'password' => bcrypt($data['ci']),
                ]);
                Auth::login($user);
                // Aquí llamamos a la función hacerVenta
                $ventaResponse = $this->hacerVenta($productos, $user, $direccionEnvio, $totalConDescuento, $cuponId, $tipoEntrega);
                if ($ventaResponse) {
                    return response()->json(['venta_id' => $ventaResponse->id], 201);
                } else {
                    return response()->json(['message' => 'Error al realizar la venta.'], 400);
                }
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Error inesperado: ' . $e->getMessage()], 500);
        }
    }
    //hcaer venta
    public function hacerVenta(array $productos, $usuario, $direccionEnvio, $totalConDescuento, $cuponId, $tipoEntrega)
    {
        try {
            DB::beginTransaction();
            $total = 0;
            $cantidadTotal = 0;
            $errores = [];
            foreach ($productos as $producto) {
                $productoDb = Producto::where('id', $producto['id'])
                    ->lockForUpdate()
                    ->first();
                if (!$productoDb) {
                    $errores[] = 'Producto con ID ' . $producto['id'] . ' no encontrado.';
                    continue;
                }
                if (!$productoDb->activo) {
                    $errores[] = 'El producto ' . $productoDb->nombre . ' está deshabilitado.';
                    continue;
                }
                if ($productoDb->stock < $producto['cantidad']) {
                    $errores[] = 'No hay suficiente stock para ' . $productoDb->nombre . '.';
                    continue;
                }
                if ($productoDb->precio != $producto['precio']) {
                    $errores[] = 'El precio del producto ' . $productoDb->nombre . ' ha cambiado. Precio actual: Bs. ' . $productoDb->precio . '.';
                    continue;
                }
                $total += $producto['cantidad'] * $producto['precio'];
                $cantidadTotal += $producto['cantidad'];
            }
            // Si hay errores, abortamos la venta
            if (!empty($errores)) {
                return response()->json(['errores' => $errores], 400);
            }
            // verificar decuento 
            $totalConDescuentoFinal = ($totalConDescuento == $total) ? 0 : $totalConDescuento;
            // Realizamos la venta
            $venta = Venta::create([
                'ID_Usuario' => $usuario->id,
                'Total' => $total,
                'Totalcondescuento' => $totalConDescuentoFinal,
                'Cantidad' => $cantidadTotal,
                'Estado' => 'pendiente',
                'Método_Pago' => 'transferencia',
                'tipo_entrega' => $tipoEntrega,
            ]);
            // Insertar en VentaDetalle y decrementar stock
            foreach ($productos as $producto) {
                VentaDetalle::create([
                    'venta_id' => $venta->id,
                    'producto_id' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'precio_unitario' => $producto['precio'],
                ]);
                $productoDb = Producto::findOrFail($producto['id']);
                $productoDb->decrement('stock', $producto['cantidad']);
            }
            // Crear un registro en la tabla envíos
            if ($tipoEntrega === 'envio') {
                $envio = Envio::create([
                    'ID_Venta' => $venta->id,
                    'Dirrecion' => $direccionEnvio,
                    'Estado_Envio' => 'pendiente',
                ]);
            }
            // Verificar el cupón
            if (!empty($cuponId)) {
                $cupon = Cupon::where('id', $cuponId)
                    ->lockForUpdate()
                    ->first();
                if ($cupon) {
                    if ($cupon->uso_maximo <= 0) {
                        DB::rollBack();
                        return response()->json(['message' => 'El cupón ha sido agotado.'], 400);
                    }
                    $cupon->uso_maximo -= 1;
                    $cupon->usos_actuales += 1;
                    $cupon->save();
                } else {
                    DB::rollBack();
                    return response()->json(['message' => 'Cupón no válido.'], 400);
                }
            }
            DB::commit();
            return $venta;
            return response()->json(['message' => 'Venta realizada exitosamente.', 'venta_id' => $venta->id, 'envio_id' => $envio->id], 201);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Error al realizar la venta: ' . $e->getMessage()], 500);
        }
    }

    //cupon validar 
    public function validar(Request $request)
    {
        try {
            $codigo = $request->input('codigo');
            $cupon = Cupon::where('codigo', $codigo)->first();

            if (!$cupon) {
                return response()->json(['success' => false, 'message' => 'Cupón no encontrado.'], 404);
            }
            if (!$cupon->activo) {
                return response()->json(['success' => false, 'message' => 'Este cupón no está activo.'], 400);
            }
            if ($cupon->uso_maximo <= 0) {
                return response()->json(['success' => false, 'message' => 'Este cupón está agotado.'], 400);
            }
            if ($cupon->fecha_expiracion && $cupon->fecha_expiracion < now()) {
                return response()->json(['success' => false, 'message' => 'Este cupón ya expiró.'], 400);
            }
            $descuento = $cupon->descuento ?? 0;
            $porcentaje = $cupon->porcentaje ?? 0;
            $tipoDescuento = $descuento > 0 ? 'descuento_fijo' : ($porcentaje > 0 ? 'porcentaje' : 'sin_descuento');
            $valorDescuento = $descuento > 0 ? $descuento . ' Bs' : ($porcentaje > 0 ? $porcentaje . '%' : '0');
            return response()->json([
                'success' => true,
                'message' => 'Cupón válido.',
                'cupon' => [
                    'id_cupon' => $cupon->id,
                    'codigo' => $cupon->codigo,
                    'tipo' => $tipoDescuento,
                    'valor' => $valorDescuento,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Hubo un error al validar el cupón.'], 500);
        }
    }
}
