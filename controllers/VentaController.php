<?php

namespace Controllers;

use Controllers\ProductoController;
use Exception;
use Model\ActiveRecord;
use Model\Ventas;
use Model\VentaDetalles;
use Model\Productos;
use Model\Usuarios;
use MVC\Router;

class VentaController extends ActiveRecord{
    
    public static function renderizarPagina(Router $router){
        $router->render('ventas/index', []);
    }

    //Guardar
    public static function guardarAPI(){
        getHeadersApi();

        if (empty($_POST['venta_cliente_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar un cliente'
            ]);
            return;
        }

        if (empty($_POST['productos'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar al menos un producto'
            ]);
            return;
        }

        $productos = is_string($_POST['productos']) ? json_decode($_POST['productos'], true) : $_POST['productos'];
        
        if (!is_array($productos)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Formato de productos inválido'
            ]);
            return;
        }

        $total_venta = 0;

        foreach ($productos as $p) {
            $pro_id = $p['pro_id'];
            $cantidad_solicitada = $p['cantidad'];

            $stock_disponible = ProductoController::ValidarStockProducto($pro_id);

            if ($stock_disponible < $cantidad_solicitada) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "Stock insuficiente para producto ID: {$pro_id}. Disponible: {$stock_disponible}, solicitado: {$cantidad_solicitada}"
                ]);
                return;
            }

            $precio_unitario = $p['precio'];
            $subtotal = $cantidad_solicitada * $precio_unitario;
            $total_venta += $subtotal;
        }

        try {
            $venta = new Ventas([
                'venta_cliente_id' => $_POST['venta_cliente_id'],
                'venta_total' => $total_venta,
                'venta_fecha' => date('Y-m-d H:i:s'),
                'venta_situacion' => 1
            ]);

            $resultado_venta = $venta->crear();
            $venta_id = $resultado_venta['id'];

            foreach ($productos as $p) {
                $pro_id = $p['pro_id'];
                $cantidad = $p['cantidad'];
                $precio_unitario = $p['precio'];
                $subtotal = $cantidad * $precio_unitario;

                $detalle = new VentaDetalles([
                    'detalle_venta_id' => $venta_id,
                    'detalle_producto_id' => $pro_id,
                    'detalle_cantidad' => $cantidad,
                    'detalle_precio_unitario' => $precio_unitario,
                    'detalle_subtotal' => $subtotal
                ]);

                $detalle->crear();

                ProductoController::ActualizarStockProducto($pro_id, $cantidad);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta registrada correctamente',
                'venta_id' => $venta_id
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    //Buscar - FUNCIÓN CORREGIDA
    public static function buscarAPI(){
        try {
            $data = self::ObtenerVentasConClientes();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Ventas obtenidas correctamente',
                'data' => $data ?: [] // Asegurar que siempre sea un array
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener las ventas',
                'detalle' => $e->getMessage(),
                'data' => [] // Asegurar que siempre haya un array vacío
            ]);
        }
    }

    //Obtener
    public static function obtenerDetalleAPI(){
        try {
            $venta_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
            
            $venta = self::ObtenerVentaPorId($venta_id);
            $detalles = self::ObtenerDetallesPorVenta($venta_id);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Detalle de venta obtenido correctamente',
                'venta' => $venta,
                'detalles' => $detalles
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el detalle',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    public static function modificarAPI(){
        getHeadersApi();

        if (empty($_POST['venta_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de venta requerido'
            ]);
            return;
        }

        $venta_id = $_POST['venta_id'];

        if (empty($_POST['productos'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Debe seleccionar al menos un producto'
            ]);
            return;
        }

        $productos = is_string($_POST['productos']) ? json_decode($_POST['productos'], true) : $_POST['productos'];
        
        if (!is_array($productos)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Formato de productos inválido'
            ]);
            return;
        }

        $total_venta = 0;

        try {
            self::RestaurarStockDeVenta($venta_id);

            self::EliminarDetallesPorVenta($venta_id);

            foreach ($productos as $p) {
                $pro_id = $p['pro_id'];
                $cantidad_solicitada = $p['cantidad'];

                $stock_disponible = ProductoController::ValidarStockProducto($pro_id);
                
                if ($stock_disponible < $cantidad_solicitada) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => "Stock insuficiente para producto ID: {$pro_id}. Disponible: {$stock_disponible}, solicitado: {$cantidad_solicitada}"
                    ]);
                    return;
                }

                $precio_unitario = $p['precio'];
                $subtotal = $cantidad_solicitada * $precio_unitario;
                $total_venta += $subtotal;
            }

            $venta = Ventas::find($venta_id);
            $venta->sincronizar([
                'venta_total' => $total_venta,
                'venta_fecha' => date('Y-m-d H:i:s')
            ]);
            $venta->actualizar();

            foreach ($productos as $p) {
                $pro_id = $p['pro_id'];
                $cantidad = $p['cantidad'];
                $precio_unitario = $p['precio'];
                $subtotal = $cantidad * $precio_unitario;

                $detalle = new VentaDetalles([
                    'detalle_venta_id' => $venta_id,
                    'detalle_producto_id' => $pro_id,
                    'detalle_cantidad' => $cantidad,
                    'detalle_precio_unitario' => $precio_unitario,
                    'detalle_subtotal' => $subtotal
                ]);

                $detalle->crear();

                ProductoController::ActualizarStockProducto($pro_id, $cantidad);
            }

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Venta modificada correctamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar la venta',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    //Obtener clientes
    public static function obtenerClientesAPI(){
        try {
            $sql = "SELECT usuario_id, usuario_nombres, usuario_apellidos, usuario_correo 
                    FROM usuarios WHERE usuario_situacion = 1";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Clientes obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los clientes',
                'detalle' => $e->getMessage(),
            ]);
        }
    }

    // MÉTODOS CORREGIDOS PARA FUNCIONAR CON TU ESTRUCTURA
    public static function ObtenerVentasConClientes(){
        $sql = "SELECT v.venta_id, v.venta_total, v.venta_fecha, 
                       u.usuario_nombres, u.usuario_apellidos, u.usuario_id
                FROM ventas v 
                INNER JOIN usuarios u ON v.venta_cliente_id = u.usuario_id 
                WHERE v.venta_situacion = 1 ORDER BY v.venta_fecha DESC";
        return self::fetchArray($sql);
    }

    public static function ObtenerVentaPorId($id){
        $sql = "SELECT v.*, u.usuario_nombres, u.usuario_apellidos, u.usuario_correo, u.usuario_id
                FROM ventas v INNER JOIN usuarios u ON v.venta_cliente_id = u.usuario_id 
                WHERE v.venta_id = $id AND v.venta_situacion = 1";
        return self::fetchFirst($sql);
    }

    public static function ObtenerDetallesPorVenta($venta_id){
        $sql = "SELECT vd.*, p.pro_nombre FROM venta_detalles vd 
                INNER JOIN productos p ON vd.detalle_producto_id = p.pro_id 
                WHERE vd.detalle_venta_id = $venta_id ORDER BY p.pro_nombre";
        return self::fetchArray($sql);
    }

    public static function EliminarDetallesPorVenta($venta_id){
        return self::SQL("DELETE FROM venta_detalles WHERE detalle_venta_id = $venta_id");
    }

    public static function RestaurarStockDeVenta($venta_id){
        $detalles = self::fetchArray("SELECT detalle_producto_id, detalle_cantidad 
                                     FROM venta_detalles WHERE detalle_venta_id = $venta_id");
        
        foreach($detalles as $d){
            self::SQL("UPDATE productos SET pro_cantidad = pro_cantidad + " . 
                     $d['detalle_cantidad'] . " WHERE pro_id = " . $d['detalle_producto_id']);
        }
        return true;
    }
}