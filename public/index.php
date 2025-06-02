<?php 
require_once __DIR__ . '/../includes/app.php';

use Controllers\UsuarioController;
use MVC\Router;
use Controllers\AppController;
use Controllers\FacturaController;
use Controllers\ProductoController;
use Controllers\VentaController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

$router->get('/usuario', [UsuarioController::class, 'renderizarPAgina']);
$router->post('/usuarios/guardarAPI', [UsuarioController::class, 'guardarAPI']);
$router->get('/usuarios/buscarAPI', [UsuarioController::class, 'buscarAPI']);
$router->post('/usuarios/modificarAPI', [UsuarioController::class, 'modificarAPI']);

//Rutas Productos
$router->get('/productos', [ProductoController::class, 'renderizarPagina']);
$router->post('/productos/guardarAPI', [ProductoController::class, 'guardarAPI']);
$router->get('/productos/buscarAPI', [ProductoController::class, 'buscarAPI']);
$router->post('/productos/modificarAPI', [ProductoController::class, 'modificarAPI']);
$router->get('/productos/eliminar', [ProductoController::class, 'EliminarAPI']);
$router->get('/productos/disponibles', [ProductoController::class, 'productosDisponiblesAPI']);

// RUTAS VENTAS
$router->get('/ventas', [VentaController::class, 'renderizarPagina']);
$router->post('/ventas/guardarAPI', [VentaController::class, 'guardarAPI']);
$router->get('/ventas/buscarAPI', [VentaController::class, 'buscarAPI']);
$router->get('/ventas/detalle', [VentaController::class, 'obtenerDetalleAPI']);
$router->post('/ventas/modificarAPI', [VentaController::class, 'modificarAPI']);
$router->get('/ventas/clientes', [VentaController::class, 'obtenerClientesAPI']);

//Rutas Factura 
$router->get('/facturas/generar', [FacturaController::class, 'generarPDF']);
$router->get('/facturas/previsualizar', [FacturaController::class, 'previsualizarFactura']);
$router->get('/facturas/descargar', [FacturaController::class, 'descargarFactura']);



$router->comprobarRutas();