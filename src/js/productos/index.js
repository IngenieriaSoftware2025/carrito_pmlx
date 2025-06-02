import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const FormProductos = document.getElementById('FormProductos');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const InputProductoPrecio = document.getElementById('pro_precio');
const InputProductoCantidad = document.getElementById('pro_cantidad');

const ValidarPrecio = () => {
    const precio = InputProductoPrecio.value;

    if (precio.length < 1) {
        InputProductoPrecio.classList.remove('is-valid', 'is-invalid');
    } else {
        if (precio <= 0) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Precio inválido",
                text: "El precio debe ser mayor a cero",
                showConfirmButton: true,
            });

            InputProductoPrecio.classList.remove('is-valid');
            InputProductoPrecio.classList.add('is-invalid');
        } else {
            InputProductoPrecio.classList.remove('is-invalid');
            InputProductoPrecio.classList.add('is-valid');
        }
    }
}

const ValidarCantidad = () => {
    const cantidad = InputProductoCantidad.value;

    if (cantidad.length < 1) {
        InputProductoCantidad.classList.remove('is-valid', 'is-invalid');
    } else {
        if (cantidad < 0) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Cantidad inválida",
                text: "La cantidad no puede ser negativa",
                showConfirmButton: true,
            });

            InputProductoCantidad.classList.remove('is-valid');
            InputProductoCantidad.classList.add('is-invalid');
        } else {
            InputProductoCantidad.classList.remove('is-invalid');
            InputProductoCantidad.classList.add('is-valid');
        }
    }
}

const GuardarProducto = async (event) => {
    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormProductos, ['pro_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(FormProductos);

    const url = '/carrito_pmlx/productos/guardarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarProductos();

        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error)
    }
    BtnGuardar.disabled = false;
}

const BuscarProductos = async () => {
    const url = '/carrito_pmlx/productos/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {
            datatable.clear().draw();
            datatable.rows.add(data).draw();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error)
    }
}

const datatable = new DataTable('#TableProductos', {
    dom: `
        <"row mt-3 justify-content-between" 
            <"col" l> 
            <"col" B> 
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between" 
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'pro_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Nombre del Producto', 
            data: 'pro_nombre',
            width: '40%'
        },
        { 
            title: 'Precio Unitario', 
            data: 'pro_precio',
            width: '15%',
            render: (data, type, row) => {
                return `Q. ${parseFloat(data).toFixed(2)}`;
            }
        },
        { 
            title: 'Stock Disponible', 
            data: 'pro_cantidad',
            width: '15%',
            render: (data, type, row) => {
                const cantidad = parseInt(data);
                let badge = '';
                
                if (cantidad === 0) {
                    badge = '<span class="badge bg-danger">Sin Stock</span>';
                } else if (cantidad <= 5) {
                    badge = '<span class="badge bg-warning">Stock Bajo</span>';
                } else {
                    badge = '<span class="badge bg-success">Disponible</span>';
                }
                
                return `${cantidad} ${badge}`;
            }
        },
        {
            title: 'Acciones',
            data: 'pro_id',
            width: '25%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning btn-sm modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.pro_nombre}"  
                         data-precio="${row.pro_precio}"  
                         data-cantidad="${row.pro_cantidad}"  
                         title="Modificar producto">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger btn-sm eliminar mx-1' 
                         data-id="${data}"
                         title="Eliminar producto">
                        <i class="bi bi-x-circle me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset

    document.getElementById('pro_id').value = datos.id
    document.getElementById('pro_nombre').value = datos.nombre
    document.getElementById('pro_precio').value = datos.precio
    document.getElementById('pro_cantidad').value = datos.cantidad

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

const limpiarTodo = () => {
    FormProductos.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    const inputs = FormProductos.querySelectorAll('input');
    inputs.forEach(input => {
        input.classList.remove('is-valid', 'is-invalid');
    });
}

const ModificarProducto = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormProductos, [''])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(FormProductos);

    const url = '/carrito_pmlx/productos/modificarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarProductos();

        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error)
    }
    BtnModificar.disabled = false;
}

const EliminarProducto = async (e) => {
    const idProducto = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea eliminar este producto?",
        text: 'El producto será desactivado pero no eliminado permanentemente por ya no contar con Stock en la tienda',
        showConfirmButton: true,
        confirmButtonText: 'Sí, eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/carrito_pmlx/productos/eliminar?id=${idProducto}`;
        const config = {
            method: 'GET'
        }

        try {
            const consulta = await fetch(url, config);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarProductos();
            } else {
                await Swal.fire({
                    position: "center",
                    icon: "error",
                    title: "Error",
                    text: mensaje,
                    showConfirmButton: true,
                });
            }

        } catch (error) {
            console.log(error)
        }
    }
}



BuscarProductos();

datatable.on('click', '.eliminar', EliminarProducto);
datatable.on('click', '.modificar', llenarFormulario);
FormProductos.addEventListener('submit', GuardarProducto);
InputProductoPrecio.addEventListener('change', ValidarPrecio);
InputProductoCantidad.addEventListener('change', ValidarCantidad);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarProducto);