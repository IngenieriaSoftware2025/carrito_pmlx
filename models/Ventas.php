<?php

namespace Model;

class Ventas extends ActiveRecord {

    public static $tabla = 'ventas';
    public static $columnasDB = [
        'venta_cliente_id',
        'venta_total',
        'venta_fecha',
        'venta_situacion'
    ];

    public static $idTabla = 'venta_id';
    public $venta_id;
    public $venta_cliente_id;
    public $venta_total;
    public $venta_fecha;
    public $venta_situacion;

    public function __construct($args = []){
        $this->venta_id = $args['venta_id'] ?? null;
        $this->venta_cliente_id = $args['venta_cliente_id'] ?? 0;
        $this->venta_total = $args['venta_total'] ?? 0;
        $this->venta_fecha = $args['venta_fecha'] ?? '';
        $this->venta_situacion = $args['venta_situacion'] ?? 1;
    }
}