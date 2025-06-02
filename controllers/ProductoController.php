<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Productos;
use MVC\Router;

class ProductoController extends ActiveRecord{
    public static function renderizarPagina(Router $router){
        $router->render('productos/index', []);
    }

    //Guardar Productos
    public static function guardarAPI(){
        getHeadersApi();

        $_POST['pro_nombre'] = htmlspecialchars($_POST['pro_nombre']);
        $cantidad_nombre = strlen($_POST['pro_nombre']);

        if ($cantidad_nombre < 2){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del producto debe tener al menos 2 caracteres'
            ]);
            return;
        }

        $nombre_repetido = trim(strtolower($_POST['pro_nombre']));
        $sql_verificar = "SELECT pro_id FROM productos 
                         WHERE LOWER(TRIM(pro_nombre)) = " . self::$db->quote($nombre_repetido) . "
                         AND pro_situacion = 1";
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe un producto con este nombre'
            ]);
            return;
        }

        $precio_validado = filter_var($_POST['pro_precio'], FILTER_VALIDATE_INT);
        if ($precio_validado === false || $precio_validado <= 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a cero y ser un número válido'
            ]);
            return;
        }
        $_POST['pro_precio'] = $precio_validado;

        $cantidad_validada = filter_var($_POST['pro_cantidad'], FILTER_VALIDATE_INT);
        if ($cantidad_validada === false || $cantidad_validada < 0){
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser un número válido y no puede ser negativa'
            ]);
            return;
        }
        $_POST['pro_cantidad'] = $cantidad_validada;

        try {
            $data = new Productos([
                'pro_nombre' => $_POST['pro_nombre'],
                'pro_precio' => $_POST['pro_precio'],
                'pro_cantidad' => $_POST['pro_cantidad'],
                'pro_situacion' => 1
            ]);

            $crear = $data->crear();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El producto ha sido registrado con éxito'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Buscar Productos
    public static function buscarAPI(){
        try {
            $sql = "SELECT * FROM productos WHERE pro_situacion = 1";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Modificar Productos
    public static function modificarAPI(){
        getHeadersApi();

        $id = $_POST['pro_id'];

        $_POST['pro_nombre'] = htmlspecialchars($_POST['pro_nombre']);
        $cantidad_nombre = strlen($_POST['pro_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del producto debe tener al menos 2 caracteres'
            ]);
            return;
        }

        $nombre_repetido = trim(strtolower($_POST['pro_nombre']));
        $sql_verificar = "SELECT pro_id FROM productos 
                         WHERE LOWER(TRIM(pro_nombre)) = " . self::$db->quote($nombre_repetido) . "
                         AND pro_situacion = 1 
                         AND pro_id != " . (int)$id;
        $nombre_existe = self::fetchFirst($sql_verificar);
        
        if ($nombre_existe) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Ya existe otro producto con este nombre'
            ]);
            return;
        }

        $precio_validado = filter_var($_POST['pro_precio'], FILTER_VALIDATE_INT);
        if ($precio_validado === false || $precio_validado <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El precio debe ser mayor a cero y ser un número válido'
            ]);
            return;
        }
        $_POST['pro_precio'] = $precio_validado;

        $cantidad_validada = filter_var($_POST['pro_cantidad'], FILTER_VALIDATE_INT);
        if ($cantidad_validada === false || $cantidad_validada < 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad debe ser un número válido y no puede ser negativa'
            ]);
            return;
        }
        $_POST['pro_cantidad'] = $cantidad_validada;

        try {
            $data = Productos::find($id);
            $data->sincronizar([
                'pro_nombre' => $_POST['pro_nombre'],
                'pro_precio' => $_POST['pro_precio'],
                'pro_cantidad' => $_POST['pro_cantidad'],
                'pro_situacion' => 1
            ]);

            $data->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'La información del producto ha sido modificada con éxito'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    //Eliminar Producto
    public static function EliminarAPI(){
        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            $stock_actual = self::ValidarStockProducto($id);
            
            if ($stock_actual > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No se puede eliminar el producto porque tiene existencias en stock',
                    'detalle' => "Stock actual: $stock_actual unidades. Debe agotar el stock antes de eliminar el producto."
                ]);
                return;
            }

            $sql_verificar = "SELECT pro_id, pro_nombre FROM productos WHERE pro_id = $id AND pro_situacion = 1";
            $producto = self::fetchFirst($sql_verificar);
            
            if (!$producto) {
                http_response_code(404);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El producto no existe o ya está inactivo'
                ]);
                return;
            }

            self::EliminarProducto($id, 0);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El producto ha sido desactivado correctamente',
                'detalle' => "Producto '{$producto['pro_nombre']}' desactivado exitosamente"
            ]);
        
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el producto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function productosDisponiblesAPI(){
        try {
            $data = self::ObtenerProductosDisponibles();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Productos disponibles obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los productos disponibles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function EliminarProducto($id, $situacion)
    {
        $sql = "UPDATE productos SET pro_situacion = $situacion WHERE pro_id = $id";
        return self::SQL($sql);
    }

    public static function ValidarStockProducto($id)
    {
        $sql = "SELECT pro_cantidad FROM productos WHERE pro_id = $id AND pro_situacion = 1";
        $resultado = self::fetchFirst($sql);
        return $resultado['pro_cantidad'] ?? 0;
    }

    public static function ActualizarStockProducto($id, $cantidad_vendida)
    {
        $sql = "UPDATE productos SET pro_cantidad = pro_cantidad - $cantidad_vendida WHERE pro_id = $id";
        return self::SQL($sql);
    }

    public static function ObtenerProductosDisponibles()
    {
        $sql = "SELECT * FROM productos WHERE pro_cantidad > 0 AND pro_situacion = 1 ORDER BY pro_nombre";
        return self::fetchArray($sql);
    }

    public static function ReactivarProducto($id)
    {
        return self::EliminarProducto($id, 1);
    }
}