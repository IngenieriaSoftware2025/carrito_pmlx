<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;

class FacturaController extends ActiveRecord
{
    public static function generarPDF()
    {
        echo "<h1>🔧 DEBUG FACTURA</h1>";
        echo "<p>Controlador cargado correctamente</p>";
        
        try {
            // Obtener ID de venta
            $id_venta = $_GET['venta_id'] ?? 0;
            echo "<p>✅ ID Venta recibido: {$id_venta}</p>";
            
            if (!$id_venta) {
                throw new Exception("ID de venta requerido");
            }

            // Mostrar información básica sin conexión a BD
            echo "<p>✅ Generando factura...</p>";
            
            // Intentar obtener datos reales de la BD
            $datos_venta = null;
            $productos = [];
            
            try {
                echo "<p>🔍 Intentando conectar a la base de datos...</p>";
                
                // Intentar diferentes métodos de conexión
                $db = null;
                
                // Método 1: self::getDB()
                try {
                    $db = self::getDB();
                    echo "<p>✅ Conexión establecida con self::getDB()</p>";
                } catch (Exception $e) {
                    echo "<p>❌ Error con self::getDB(): " . $e->getMessage() . "</p>";
                }
                
                // Método 2: Conexión directa si el anterior falló
                if (!$db) {
                    try {
                        // Intentar conexión directa (ajusta estos valores según tu configuración)
                        $host = 'localhost';
                        $dbname = 'informx'; // Tu base de datos
                        $username = 'informx'; // Según vi en tu screenshot
                        $password = ''; // Ajusta si tienes password
                        
                        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8";
                        $db = new \PDO($dsn, $username, $password, [
                            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                        ]);
                        echo "<p>✅ Conexión directa establecida</p>";
                    } catch (Exception $e) {
                        echo "<p>❌ Error conexión directa: " . $e->getMessage() . "</p>";
                    }
                }
                
                // Solo continuar si tenemos conexión
                if ($db) {
                    // Primero, ver qué tablas existen
                    try {
                        $stmt = $db->query("SHOW TABLES");
                        $tablas = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                        echo "<p>📋 Tablas disponibles: " . implode(', ', $tablas) . "</p>";
                    } catch (Exception $e) {
                        echo "<p>❌ Error listando tablas: " . $e->getMessage() . "</p>";
                    }
                    
                    // Buscar venta en la tabla correcta
                    try {
                        $query = "SELECT v.*, u.usuario_nombres, u.usuario_apellidos, u.usuario_correo, u.usuario_id
                                 FROM ventas v 
                                 LEFT JOIN usuarios u ON v.venta_cliente_id = u.usuario_id 
                                 WHERE v.venta_id = ? LIMIT 1";
                        $stmt = $db->prepare($query);
                        $stmt->execute([$id_venta]);
                        $resultado = $stmt->fetch();
                        
                        if ($resultado) {
                            echo "<p>✅ Venta encontrada con datos del cliente</p>";
                            echo "<p>📊 Cliente: " . $resultado['usuario_nombres'] . " " . $resultado['usuario_apellidos'] . "</p>";
                            $datos_venta = $resultado;
                        } else {
                            echo "<p>❌ Venta con ID {$id_venta} no encontrada</p>";
                        }
                    } catch (Exception $e) {
                        echo "<p>❌ Error buscando venta: " . $e->getMessage() . "</p>";
                    }
                    
                    // Buscar productos de la venta
                    if ($datos_venta) {
                        echo "<p>🔍 Buscando productos en venta_detalles...</p>";
                        
                        try {
                            $query = "SELECT p.pro_nombre as pro_nombre, 
                                            vd.detalle_cantidad as detalle_cantidad, 
                                            vd.detalle_precio as detalle_precio_unitario, 
                                            (vd.detalle_cantidad * vd.detalle_precio) as detalle_subtotal
                                     FROM venta_detalles vd 
                                     INNER JOIN productos p ON vd.detalle_producto_id = p.pro_id 
                                     WHERE vd.detalle_venta_id = ?";
                            
                            $stmt = $db->prepare($query);
                            $stmt->execute([$id_venta]);
                            $productos = $stmt->fetchAll();
                            
                            if (!empty($productos)) {
                                echo "<p>✅ Productos encontrados: " . count($productos) . " productos</p>";
                                foreach ($productos as $prod) {
                                    echo "<p>  - " . $prod['pro_nombre'] . " (Cant: " . $prod['detalle_cantidad'] . ")</p>";
                                }
                            } else {
                                echo "<p>❌ No se encontraron productos para esta venta</p>";
                                
                                // Intentar mostrar qué hay en venta_detalles para debug
                                $debug_query = "SELECT * FROM venta_detalles WHERE detalle_venta_id = ?";
                                $debug_stmt = $db->prepare($debug_query);
                                $debug_stmt->execute([$id_venta]);
                                $debug_results = $debug_stmt->fetchAll();
                                echo "<p>🔍 Debug - Registros en venta_detalles: " . count($debug_results) . "</p>";
                            }
                        } catch (Exception $e) {
                            echo "<p>❌ Error buscando productos: " . $e->getMessage() . "</p>";
                        }
                        
                        // Normalizar datos de venta para que funcionen con el HTML
                        $datos_venta['venta_id'] = $datos_venta['venta_id'];
                        $datos_venta['venta_fecha'] = $datos_venta['venta_fecha'];
                        $datos_venta['venta_total'] = $datos_venta['venta_total'];
                        // Los datos de usuario ya están desde el JOIN
                    }
                } else {
                    echo "<p>❌ No se pudo establecer conexión a la base de datos</p>";
                }
                
            } catch (Exception $e) {
                echo "<p>❌ Error general: " . $e->getMessage() . "</p>";
            }
            
            // Si no se obtuvieron datos reales, usar ejemplos
            if (!$datos_venta) {
                echo "<p>⚠️ Usando datos de ejemplo (no se encontraron datos reales)</p>";
                $datos_venta = [
                    'venta_id' => $id_venta,
                    'venta_fecha' => date('Y-m-d H:i:s'),
                    'venta_total' => 100.00,
                    'usuario_nombres' => 'Cliente',
                    'usuario_apellidos' => 'Ejemplo',
                    'usuario_correo' => 'cliente@ejemplo.com',
                    'usuario_id' => 1
                ];
            }
            
            if (empty($productos)) {
                echo "<p>⚠️ Usando productos de ejemplo (no se encontraron productos reales)</p>";
                $productos = [
                    [
                        'pro_nombre' => 'Producto de Ejemplo 1',
                        'detalle_cantidad' => 2,
                        'detalle_precio_unitario' => 25.00,
                        'detalle_subtotal' => 50.00
                    ],
                    [
                        'pro_nombre' => 'Producto de Ejemplo 2',
                        'detalle_cantidad' => 1,
                        'detalle_precio_unitario' => 50.00,
                        'detalle_subtotal' => 50.00
                    ]
                ];
            }
            
            echo "<p>✅ Datos preparados, generando HTML...</p>";
            
            // Generar factura HTML directamente
            self::MostrarFacturaHTML($datos_venta, $productos);
            
        } catch (Exception $e) {
            echo "<div style='color: red; padding: 20px; background: #ffe6e6; border: 1px solid #ff0000; margin: 10px;'>";
            echo "<h3>❌ Error:</h3>";
            echo "<p>" . $e->getMessage() . "</p>";
            echo "</div>";
        }
    }
    
    private static function MostrarFacturaHTML($datos_venta, $productos)
    {
        $numero_factura = str_pad($datos_venta['venta_id'], 6, '0', STR_PAD_LEFT);
        $fecha_formateada = date('d/m/Y H:i', strtotime($datos_venta['venta_fecha']));
        
        echo '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Factura #' . $numero_factura . '</title>
            <style>
                body { 
                    font-family: Arial, sans-serif; 
                    margin: 0;
                    padding: 20px;
                    background: #f5f5f5;
                }
                .factura-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0,0,0,0.1);
                }
                .header {
                    background: linear-gradient(135deg, #007bff, #0056b3);
                    color: white;
                    padding: 30px;
                    text-align: center;
                    margin: -30px -30px 30px -30px;
                    border-radius: 10px 10px 0 0;
                }
                .header h1 {
                    margin: 0;
                    font-size: 2.5em;
                    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
                }
                .header p {
                    margin: 10px 0 0 0;
                    font-size: 1.2em;
                    opacity: 0.9;
                }
                .info-section {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 30px;
                    gap: 20px;
                }
                .info-box {
                    flex: 1;
                    padding: 20px;
                    background: #f8f9fa;
                    border-left: 4px solid #007bff;
                    border-radius: 0 5px 5px 0;
                }
                .info-box h3 {
                    margin-top: 0;
                    color: #007bff;
                    border-bottom: 2px solid #007bff;
                    padding-bottom: 5px;
                }
                .products-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin: 30px 0;
                    border-radius: 10px;
                    overflow: hidden;
                    box-shadow: 0 0 10px rgba(0,0,0,0.1);
                }
                .products-table th {
                    background: linear-gradient(135deg, #007bff, #0056b3);
                    color: white;
                    padding: 15px;
                    text-align: left;
                    font-weight: bold;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                }
                .products-table td {
                    padding: 15px;
                    border-bottom: 1px solid #eee;
                }
                .products-table tr:nth-child(even) {
                    background-color: #f8f9fa;
                }
                .products-table tr:hover {
                    background-color: #e3f2fd;
                    transition: background-color 0.3s ease;
                }
                .text-right { text-align: right; }
                .text-center { text-align: center; }
                .totals-section {
                    background: #f8f9fa;
                    padding: 25px;
                    border-radius: 10px;
                    margin-top: 20px;
                    border-left: 4px solid #28a745;
                }
                .total-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 8px 0;
                    font-size: 1.1em;
                }
                .total-row.final {
                    font-weight: bold;
                    font-size: 1.3em;
                    color: #007bff;
                    border-top: 2px solid #007bff;
                    margin-top: 10px;
                    padding-top: 15px;
                }
                .footer {
                    text-align: center;
                    margin-top: 40px;
                    padding-top: 20px;
                    border-top: 2px solid #007bff;
                    color: #666;
                }
                .print-button {
                    background: linear-gradient(135deg, #007bff, #0056b3);
                    color: white;
                    border: none;
                    padding: 15px 30px;
                    border-radius: 25px;
                    font-size: 1.1em;
                    font-weight: bold;
                    cursor: pointer;
                    margin: 20px 10px;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    box-shadow: 0 4px 15px rgba(0,123,255,0.3);
                    transition: all 0.3s ease;
                }
                .print-button:hover {
                    transform: translateY(-2px);
                    box-shadow: 0 6px 20px rgba(0,123,255,0.4);
                }
                @media print {
                    body { background: white; }
                    .print-button { display: none; }
                    .factura-container { box-shadow: none; }
                }
                @media (max-width: 768px) {
                    .info-section { flex-direction: column; }
                    .products-table th, .products-table td { padding: 10px 5px; font-size: 0.9em; }
                }
            </style>
        </head>
        <body>
            <div class="factura-container">
                <div class="header">
                    <h1>🧾 FACTURA</h1>
                    <p>Número: #' . $numero_factura . '</p>
                </div>
                
                <div class="info-section">
                    <div class="info-box">
                        <h3>🏢 Información de la Empresa</h3>
                        <p><strong>Sistema de Ventas S.A.</strong></p>
                        <p>📍 Ciudad de Guatemala, Guatemala</p>
                        <p>📞 Tel: (502) 2345-6789</p>
                        <p>✉️ ventas@sistema.com</p>
                        <p>🆔 NIT: 123456789</p>
                    </div>
                    
                    <div class="info-box">
                        <h3>👤 Datos del Cliente</h3>
                        <p><strong>' . htmlspecialchars($datos_venta['usuario_nombres'] . ' ' . $datos_venta['usuario_apellidos']) . '</strong></p>
                        <p>✉️ ' . htmlspecialchars($datos_venta['usuario_correo']) . '</p>
                        <p>🆔 Cliente #' . str_pad($datos_venta['usuario_id'], 4, '0', STR_PAD_LEFT) . '</p>
                    </div>
                    
                    <div class="info-box">
                        <h3>📅 Información de Venta</h3>
                        <p><strong>Fecha:</strong> ' . $fecha_formateada . '</p>
                        <p><strong>Estado:</strong> <span style="color: #28a745;">✅ PAGADA</span></p>
                        <p><strong>Serie:</strong> A001</p>
                        <p><strong>Moneda:</strong> Quetzales (GTQ)</p>
                    </div>
                </div>
                
                <h3 style="color: #007bff; border-bottom: 2px solid #007bff; padding-bottom: 10px; margin-bottom: 20px;">
                    🛍️ DETALLE DE PRODUCTOS
                </h3>
                
                <table class="products-table">
                    <thead>
                        <tr>
                            <th style="width: 8%;">#</th>
                            <th style="width: 42%;">🛍️ Producto</th>
                            <th style="width: 15%;" class="text-center">📦 Cantidad</th>
                            <th style="width: 17%;" class="text-right">💰 Precio Unit.</th>
                            <th style="width: 18%;" class="text-right">💵 Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';

        $contador = 1;
        $subtotal_general = 0;
        
        foreach ($productos as $producto) {
            $subtotal_general += $producto['detalle_subtotal'];
            
            echo '
                        <tr>
                            <td class="text-center">' . $contador . '</td>
                            <td><strong>' . htmlspecialchars($producto['pro_nombre']) . '</strong></td>
                            <td class="text-center">' . number_format($producto['detalle_cantidad'], 0) . '</td>
                            <td class="text-right">Q. ' . number_format($producto['detalle_precio_unitario'], 2) . '</td>
                            <td class="text-right">Q. ' . number_format($producto['detalle_subtotal'], 2) . '</td>
                        </tr>';
            $contador++;
        }

        $iva = $subtotal_general * 0.12;
        $total = $subtotal_general + $iva;

        echo '
                    </tbody>
                </table>
                
                <div class="totals-section">
                    <h4 style="margin-top: 0; color: #28a745;">💰 RESUMEN DE TOTALES</h4>
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span>Q. ' . number_format($subtotal_general, 2) . '</span>
                    </div>
                    <div class="total-row">
                        <span>IVA (12%):</span>
                        <span>Q. ' . number_format($iva, 2) . '</span>
                    </div>
                    <div class="total-row final">
                        <span>💵 TOTAL A PAGAR:</span>
                        <span>Q. ' . number_format($datos_venta['venta_total'], 2) . '</span>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px;">
                    <button class="print-button" onclick="window.print()">🖨️ Imprimir Factura</button>
                    <button class="print-button" onclick="window.history.back()" style="background: #6c757d;">↩️ Volver</button>
                </div>
                
                <div class="footer">
                    <p><strong>🎉 ¡Gracias por su compra!</strong></p>
                    <p>Esta factura fue generada el ' . date('d/m/Y H:i') . '</p>
                    <p style="font-size: 0.9em; margin-top: 15px;">
                        📞 Servicio al Cliente: (502) 2345-6789 | ✉️ soporte@sistema.com
                    </p>
                    <p style="font-size: 0.8em; color: #999;">
                        Sistema de Ventas © ' . date('Y') . ' - Todos los derechos reservados
                    </p>
                </div>
            </div>
        </body>
        </html>';
    }
}