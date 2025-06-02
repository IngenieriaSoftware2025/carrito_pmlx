import { Modal } from "bootstrap";
import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormVentas = document.getElementById('FormVentas');
const selectCliente = document.getElementById('venta_cliente_id');
const BtnCargarProductos = document.getElementById('BtnCargarProductos');
const BtnGuardarVenta = document.getElementById('BtnGuardarVenta');
const BtnModificarVenta = document.getElementById('BtnModificarVenta');
const BtnLimpiarVenta = document.getElementById('BtnLimpiarVenta');
const seccionProductos = document.getElementById('seccionProductos');
const seccionCarrito = document.getElementById('seccionCarrito');
const productosDisponibles = document.getElementById('productosDisponibles');
const carritoItems = document.getElementById('carritoItems');
const totalVenta = document.getElementById('totalVenta');

let carrito = [];
let productos = [];

const CargarClientes = async () => {
    try {
        const respuesta = await fetch('/carrito_pmlx/ventas/clientes');
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            selectCliente.innerHTML = '<option value="">-- SELECCIONE UN CLIENTE --</option>';
            
            datos.data.forEach(cliente => {
                selectCliente.innerHTML += `
                    <option value="${cliente.usuario_id}">
                        ${cliente.usuario_nombres} ${cliente.usuario_apellidos}
                    </option>
                `;
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los clientes"
        });
    }
};

const CargarProductos = async () => {
    if (!selectCliente.value) {
        Swal.fire({
            icon: "warning",
            title: "Cliente requerido",
            text: "Debe seleccionar un cliente primero"
        });
        return;
    }

    try {
        const respuesta = await fetch('/carrito_pmlx/productos/disponibles');
        const datos = await respuesta.json();
        
        if (datos.codigo == 1) {
            productos = datos.data;
            MostrarProductos();
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: datos.mensaje || "Error al cargar productos"
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudieron cargar los productos"
        });
    }
};

const MostrarProductos = () => {
    productosDisponibles.innerHTML = '';
    
    if (!productos || productos.length === 0) {
        productosDisponibles.innerHTML = '<tr><td colspan="6" class="text-center">No hay productos disponibles</td></tr>';
        return;
    }
    
    productos.forEach(producto => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>
                <input type="checkbox" class="form-check-input producto-check" 
                       data-id="${producto.pro_id}">
            </td>
            <td>${producto.pro_nombre}</td>
            <td>Q. ${parseFloat(producto.pro_precio).toFixed(2)}</td>
            <td>${producto.pro_cantidad}</td>
            <td>
                <input type="number" class="form-control form-control-sm cantidad-input" 
                       data-id="${producto.pro_id}" 
                       min="1" max="${producto.pro_cantidad}" 
                       value="1" disabled>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-primary agregar-btn" 
                        data-id="${producto.pro_id}" disabled>
                    Agregar
                </button>
            </td>
        `;
        productosDisponibles.appendChild(fila);
    });

    seccionProductos.style.display = 'block';
    AgregarEventosProductos();
};

const AgregarEventosProductos = () => {
    document.querySelectorAll('.producto-check').forEach(check => {
        check.addEventListener('change', function() {
            const id = this.dataset.id;
            const cantidadInput = document.querySelector(`[data-id="${id}"].cantidad-input`);
            const agregarBtn = document.querySelector(`[data-id="${id}"].agregar-btn`);
            
            if (this.checked) {
                cantidadInput.disabled = false;
                agregarBtn.disabled = false;
            } else {
                cantidadInput.disabled = true;
                agregarBtn.disabled = true;
            }
        });
    });

    document.querySelectorAll('.agregar-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            AgregarAlCarrito(id);
        });
    });
};

const AgregarAlCarrito = (productoId) => {
    const producto = productos.find(p => p.pro_id == productoId);
    const cantidad = parseInt(document.querySelector(`[data-id="${productoId}"].cantidad-input`).value);
    
    if (cantidad > producto.pro_cantidad) {
        Swal.fire({
            icon: "error",
            title: "Stock insuficiente",
            text: `Solo hay ${producto.pro_cantidad} unidades disponibles`
        });
        return;
    }

    const existe = carrito.findIndex(item => item.pro_id == productoId);
    
    if (existe !== -1) {
        carrito[existe].cantidad = cantidad;
        carrito[existe].subtotal = cantidad * producto.pro_precio;
    } else {
        carrito.push({
            pro_id: productoId,
            nombre: producto.pro_nombre,
            precio: producto.pro_precio,
            cantidad: cantidad,
            subtotal: cantidad * producto.pro_precio
        });
    }

    ActualizarCarrito();
    
    if (!document.getElementById('venta_id').value) {
        BtnGuardarVenta.style.display = 'inline-block';
    }
    
    document.querySelector(`[data-id="${productoId}"].producto-check`).checked = false;
    document.querySelector(`[data-id="${productoId}"].cantidad-input`).disabled = true;
    document.querySelector(`[data-id="${productoId}"].agregar-btn`).disabled = true;
};

const ActualizarCarrito = () => {
    carritoItems.innerHTML = '';
    let total = 0;

    carrito.forEach((item, index) => {
        total += item.subtotal;
        
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${item.nombre}</td>
            <td>Q. ${parseFloat(item.precio).toFixed(2)}</td>
            <td>
                <input type="number" class="form-control form-control-sm" 
                       value="${item.cantidad}" min="1" 
                       onchange="CambiarCantidad(${index}, this.value)">
            </td>
            <td>Q. ${parseFloat(item.subtotal).toFixed(2)}</td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" 
                        onclick="QuitarDelCarrito(${index})">
                    Quitar
                </button>
            </td>
        `;
        carritoItems.appendChild(fila);
    });

    totalVenta.textContent = `Q. ${total.toFixed(2)}`;

    if (carrito.length > 0) {
        seccionCarrito.style.display = 'block';
    } else {
        seccionCarrito.style.display = 'none';
        BtnGuardarVenta.style.display = 'none';
    }
};

window.CambiarCantidad = (index, nuevaCantidad) => {
    const item = carrito[index];
    const producto = productos.find(p => p.pro_id == item.pro_id);
    
    if (nuevaCantidad > producto.pro_cantidad) {
        Swal.fire({
            icon: "error",
            title: "Stock insuficiente",
            text: `Solo hay ${producto.pro_cantidad} unidades disponibles`
        });
        ActualizarCarrito();
        return;
    }

    carrito[index].cantidad = parseInt(nuevaCantidad);
    carrito[index].subtotal = parseInt(nuevaCantidad) * item.precio;
    ActualizarCarrito();
};

window.QuitarDelCarrito = (index) => {
    carrito.splice(index, 1);
    ActualizarCarrito();
};

const GuardarVenta = async (event) => {
    event.preventDefault();
    BtnGuardarVenta.disabled = true;

    if (!selectCliente.value) {
        Swal.fire({
            icon: "warning",
            title: "Cliente requerido",
            text: "Debe seleccionar un cliente"
        });
        BtnGuardarVenta.disabled = false;
        return;
    }

    if (carrito.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Carrito vacío",
            text: "Debe agregar al menos un producto"
        });
        BtnGuardarVenta.disabled = false;
        return;
    }

    const formData = new FormData();
    formData.append('venta_cliente_id', selectCliente.value);
    formData.append('productos', JSON.stringify(carrito));

    try {
        const respuesta = await fetch('/carrito_pmlx/ventas/guardarAPI', {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();

        if (datos.codigo == 1) {
            await Swal.fire({
                icon: "success",
                title: "Éxito",
                text: datos.mensaje
            });

            LimpiarTodo();
            BuscarVentas();
        } else {
            await Swal.fire({
                icon: "error",
                title: "Error",
                text: datos.mensaje
            });
        }
    } catch (error) {
        await Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error de conexión"
        });
    }
    
    BtnGuardarVenta.disabled = false;
};


const BuscarVentas = async () => {
    try {
        const respuesta = await fetch('/carrito_pmlx/ventas/buscarAPI');
        const datos = await respuesta.json();

        if (datos.codigo == 1 && datos.data) {
            // Verificar que datos.data existe y es un array
            const ventasData = Array.isArray(datos.data) ? datos.data : [];
            
            datatable.clear();
            datatable.rows.add(ventasData);
            datatable.draw(false);
            
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: `${ventasData.length} ventas encontradas`,
                showConfirmButton: false,
                timer: 2000,
                toast: true
            });
        } else {
            // Manejar caso cuando no hay datos
            datatable.clear();
            datatable.draw(false);
            
            console.log('No se encontraron ventas o error en la respuesta');
        }
    } catch (error) {
        console.error('Error al buscar ventas:', error);
        
        // Limpiar tabla en caso de error
        datatable.clear();
        datatable.draw(false);
    }
};

const datatable = new DataTable('#TableVentas', {
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'venta_id',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Cliente', 
            data: 'usuario_nombres',
            render: (data, type, row) => {
                return `${row.usuario_nombres || ''} ${row.usuario_apellidos || ''}`;
            }
        },
        { 
            title: 'Total', 
            data: 'venta_total',
            render: (data, type, row) => {
                return `Q. ${parseFloat(data || 0).toFixed(2)}`;
            }
        },
        { 
            title: 'Fecha', 
            data: 'venta_fecha',
            render: (data, type, row) => {
                if (!data) return 'N/A';
                const fecha = new Date(data);
                return fecha.toLocaleString('es-GT');
            }
        },
        {
            title: 'Acciones',
            data: 'venta_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                    <button class='btn btn-info btn-sm' onclick="VerDetalle(${data})">
                        Ver Detalle
                    </button>
                    <button class='btn btn-warning btn-sm' onclick="ModificarVenta(${data})">
                        Modificar
                    </button>
                    <button class='btn btn-success btn-sm' onclick="GenerarFactura(${data})">
                        Factura
                    </button>
                `;
            }
        }
    ]
});

window.VerDetalle = async (ventaId) => {
    try {
        const respuesta = await fetch(`/carrito_pmlx/ventas/detalle?id=${ventaId}`);
        const datos = await respuesta.json();

        if (datos.codigo == 1) {
            const venta = datos.venta;
            const detalles = datos.detalles;

            let contenido = `
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Cliente:</strong> ${venta.usuario_nombres} ${venta.usuario_apellidos}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha:</strong> ${new Date(venta.venta_fecha).toLocaleString('es-GT')}
                    </div>
                </div>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            detalles.forEach(detalle => {
                contenido += `
                    <tr>
                        <td>${detalle.pro_nombre}</td>
                        <td>${detalle.detalle_cantidad}</td>
                        <td>Q. ${parseFloat(detalle.detalle_precio_unitario).toFixed(2)}</td>
                        <td>Q. ${parseFloat(detalle.detalle_subtotal).toFixed(2)}</td>
                    </tr>
                `;
            });

            contenido += `
                    </tbody>
                    <tfoot>
                        <tr class="table-info">
                            <td colspan="3"><strong>TOTAL:</strong></td>
                            <td><strong>Q. ${parseFloat(venta.venta_total).toFixed(2)}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            `;

            document.getElementById('contenidoDetalleVenta').innerHTML = contenido;
            document.getElementById('BtnGenerarFactura').onclick = () => GenerarFactura(ventaId);
            
            const modal = new Modal(document.getElementById('modalDetalleVenta'));
            modal.show();
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se pudo obtener el detalle"
        });
    }
};

window.ModificarVenta = async (ventaId) => {
    try {
        const respuesta = await fetch(`/carrito_pmlx/ventas/detalle?id=${ventaId}`);
        const datos = await respuesta.json();

        if (datos.codigo == 1) {
            const venta = datos.venta;
            const detalles = datos.detalles;

            selectCliente.value = venta.venta_cliente_id;
            document.getElementById('venta_id').value = venta.venta_id;

            const respuestaProductos = await fetch('/carrito_pmlx/productos/disponibles');
            const datosProductos = await respuestaProductos.json();
            
            if (datosProductos.codigo == 1) {
                productos = datosProductos.data;
                MostrarProductos();
            }

            carrito = detalles.map(detalle => ({
                pro_id: detalle.detalle_producto_id,
                nombre: detalle.pro_nombre,
                precio: parseFloat(detalle.detalle_precio_unitario),
                cantidad: parseInt(detalle.detalle_cantidad),
                subtotal: parseFloat(detalle.detalle_subtotal)
            }));

            ActualizarCarrito();

            BtnGuardarVenta.style.display = 'none';
            BtnCargarProductos.style.display = 'none';
            BtnModificarVenta.style.display = 'inline-block';
            BtnLimpiarVenta.style.display = 'inline-block';

            window.scrollTo({
                top: 0
            });

            Swal.fire({
                position: 'top-end',
                icon: 'info',
                title: 'Puedes Modificar tu compra',
                showConfirmButton: false,
                timer: 2000,
                toast: true
            });
            
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: datos.mensaje || "No se pudo cargar la venta"
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al cargar la venta"
        });
    }
};

window.GenerarFactura = (ventaId) => {
    const url = `/carrito_pmlx/facturas/generar?venta_id=${ventaId}`;
    window.open(url, '_blank');
};

const ModificarVentaSubmit = async (event) => {
    event.preventDefault();
    BtnModificarVenta.disabled = true;

    if (carrito.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "Carrito vacío",
            text: "Debe agregar al menos un producto"
        });
        BtnModificarVenta.disabled = false;
        return;
    }

    const ventaId = document.getElementById('venta_id').value;
    
    if (!ventaId) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "No se encontró el ID de la venta"
        });
        BtnModificarVenta.disabled = false;
        return;
    }

    const formData = new FormData();
    formData.append('venta_id', ventaId);
    formData.append('productos', JSON.stringify(carrito));

    try {
        const respuesta = await fetch('/carrito_pmlx/ventas/modificarAPI', {
            method: 'POST',
            body: formData
        });
        
        const datos = await respuesta.json();

        if (datos.codigo == 1) {
            await Swal.fire({
                icon: "success",
                title: "Éxito",
                text: datos.mensaje,
                confirmButtonText: "OK"
            });

            LimpiarTodo();
            BuscarVentas();
            
            setTimeout(() => {
                Swal.fire({
                    position: 'top-end',
                    icon: 'info',
                    title: 'Tabla actualizada',
                    text: 'Los cambios se han guardado correctamente',
                    showConfirmButton: false,
                    timer: 2500,
                    toast: true
                });
            }, 1000);
        } else {
            Swal.fire({
                icon: "error",
                title: "Error",
                text: datos.mensaje
            });
        }
    } catch (error) {
        Swal.fire({
            icon: "error",
            title: "Error",
            text: "Error al modificar la venta"
        });
    }
    
    BtnModificarVenta.disabled = false;
};

const LimpiarTodo = () => {
    selectCliente.value = '';
    carrito = [];
    productos = [];
    productosDisponibles.innerHTML = '';
    carritoItems.innerHTML = '';
    totalVenta.textContent = 'Q. 0.00';
    seccionProductos.style.display = 'none';
    seccionCarrito.style.display = 'none';
    
    BtnGuardarVenta.style.display = 'none';
    BtnCargarProductos.style.display = 'inline-block';
    BtnModificarVenta.style.display = 'none';
    BtnLimpiarVenta.style.display = 'inline-block';
    
    document.getElementById('venta_id').value = '';
};

// Inicialización
CargarClientes();
BuscarVentas(); 

// Event Listeners
BtnCargarProductos.addEventListener('click', CargarProductos);
FormVentas.addEventListener('submit', GuardarVenta);
BtnLimpiarVenta.addEventListener('click', LimpiarTodo);
BtnModificarVenta.addEventListener('click', ModificarVentaSubmit);