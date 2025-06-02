<?php

namespace Model;

class VentaDetalles extends ActiveRecord {

    public static $tabla = 'venta_detalles';
    public static $columnasDB = [
        'detalle_venta_id',
        'detalle_producto_id',
        'detalle_cantidad',
        'detalle_precio_unitario',
        'detalle_subtotal'
    ];

    public static $idTabla = 'detalle_id';
    public $detalle_id;
    public $detalle_venta_id;
    public $detalle_producto_id;
    public $detalle_cantidad;
    public $detalle_precio_unitario;
    public $detalle_subtotal;

    public function __construct($args = []){
        $this->detalle_id = $args['detalle_id'] ?? null;
        $this->detalle_venta_id = $args['detalle_venta_id'] ?? 0;
        $this->detalle_producto_id = $args['detalle_producto_id'] ?? 0;
        $this->detalle_cantidad = $args['detalle_cantidad'] ?? 0;
        $this->detalle_precio_unitario = $args['detalle_precio_unitario'] ?? 0;
        $this->detalle_subtotal = $args['detalle_subtotal'] ?? 0;
    }
}