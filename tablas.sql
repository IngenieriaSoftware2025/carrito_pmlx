create database lopez 

CREATE TABLE usuarios (
    usuario_id SERIAL PRIMARY KEY,
    usuario_nombres VARCHAR(255),
    usuario_apellidos VARCHAR(255),
    usuario_nit INT,
    usuario_telefono INT,
    usuario_correo VARCHAR(100),
    usuario_estado CHAR(1),
    usuario_fecha DATETIME YEAR TO SECOND,
    usuario_situacion SMALLINT DEFAULT 1
);


CREATE TABLE productos (
    pro_id SERIAL PRIMARY KEY,
    pro_nombre VARCHAR(255),
    pro_precio INT,
    pro_cantidad INT,
    pro_situacion SMALLINT DEFAULT 1
);


CREATE TABLE ventas (
    venta_id SERIAL PRIMARY KEY,
    venta_cliente_id INT, 
    venta_total DECIMAL(10,2),
    venta_fecha DATETIME YEAR TO SECOND,
    venta_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (venta_cliente_id) REFERENCES usuarios(usuario_id)
);


CREATE TABLE venta_detalles (
    detalle_id SERIAL PRIMARY KEY,
    detalle_venta_id INT,
    detalle_producto_id INT, 
    detalle_cantidad INT,
    detalle_precio_unitario INT,
    detalle_subtotal INT,
    FOREIGN KEY (detalle_venta_id) REFERENCES ventas(venta_id),
    FOREIGN KEY (detalle_producto_id) REFERENCES productos(pro_id)
);