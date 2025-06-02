import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import { data } from "jquery";

const FormUsuarios = document.getElementById('FormUsuarios');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const InputUsuarioTelefono = document.getElementById('usuario_telefono');
const usuario_nit = document.getElementById('usuario_nit');


const ValidarTelefono = () => {

    const CantidadDigitos = InputUsuarioTelefono.value


    if (CantidadDigitos.length < 1) {

        InputUsuarioTelefono.classList.remove('is-valid', 'is-invalid');

    } else {

        if (CantidadDigitos.length != 8) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "Revise el numero de telefono",
                text: "La cantidad de digitos debe ser mayor o igual 8  digitos",
                showConfirmButton: true,
            });

            InputUsuarioTelefono.classList.remove('is-valid');
            InputUsuarioTelefono.classList.add('is-invalid');

        } else {
            InputUsuarioTelefono.classList.remove('is-invalid');
            InputUsuarioTelefono.classList.add('is-valid');
        }

    }
}


function validarNit() {
    const nit = usuario_nit.value.trim();

    let nd, add = 0;

    if (nd = /^(\d+)-?([\dkK])$/.exec(nit)) {
        nd[2] = (nd[2].toLowerCase() === 'k') ? 10 : parseInt(nd[2], 10);

        for (let i = 0; i < nd[1].length; i++) {
            add += ((((i - nd[1].length) * -1) + 1) * parseInt(nd[1][i], 10));
        }
        return ((11 - (add % 11)) % 11) === nd[2];
    } else {
        return false;
    }
}

const EsValidoNit = () => {

    validarNit();

    if (validarNit()) {
        usuario_nit.classList.add('is-valid');
        usuario_nit.classList.remove('is-invalid');
    } else {
        usuario_nit.classList.remove('is-valid');
        usuario_nit.classList.add('is-invalid');

        Swal.fire({
            position: "center",
            icon: "error",
            title: "NIT INVALIDO",
            text: "El numero de nit ingresado es invalido",
            showConfirmButton: true,
        });

    }
}


const GuardarUsuario = async (event) => {

    event.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(FormUsuarios, ['usuario_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
    }

    const body = new FormData(FormUsuarios);

    const url = '/carrito_pmlx/usuarios/guardarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        console.log(datos)
        const { codigo, mensaje } = datos

        if (codigo == 1) {

            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarUsuarios();

        } else {

            await Swal.fire({
                position: "center",
                icon: "info",
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

const BuscarUsuarios = async () => {

    const url = '/carrito_pmlx/usuarios/buscarAPI';
    const config = {
        method: 'GET'
    }

    try {

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos

        if (codigo == 1) {

            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            datatable.clear().draw();
            datatable.rows.add(data).draw();

        } else {

            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }


    } catch (error) {
        console.log(error)
    }
}


const datatable = new DataTable('#TableUsuarios', {
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
            data: 'usuario_id',
            width: '%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { title: 'Nombre', data: 'usuario_nombres' },
        { title: 'Apellidos', data: 'usuario_apellidos' },
        { title: 'Correo ', data: 'usuario_correo' },
        { title: 'Telefono ', data: 'usuario_telefono' },
        { title: 'Nit', data: 'usuario_nit' },
        { title: 'Fecha', data: 'usuario_fecha' },
        {
            title: 'Destino',
            data: 'usuario_estado',
            render: (data, type, row) => {

                const estado = row.usuario_estado

                if (estado == "P") {
                    return "PRESENTE"
                } else if (estado == "F") {
                    return "FALTANDO"
                } else if (estado == "C") {
                    return "COMISION"
                }
            }
        },
        {
            title: 'Acciones',
            data: 'usuario_id',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.usuario_nombres}"  
                         data-apellidos="${row.usuario_apellidos}"  
                         data-nit="${row.usuario_nit}"  
                         data-telefono="${row.usuario_telefono}"  
                         data-correo="${row.usuario_correo}"  
                         data-estado="${row.usuario_estado}"  
                         data-fecha="${row.usuario_fecha}"  
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});


const llenarFormulario = (event) => {

    const datos = event.currentTarget.dataset

    document.getElementById('usuario_id').value = datos.id
    document.getElementById('usuario_nombres').value = datos.nombre
    document.getElementById('usuario_apellidos').value = datos.apellidos
    document.getElementById('usuario_nit').value = datos.nit
    document.getElementById('usuario_telefono').value = datos.telefono
    document.getElementById('usuario_correo').value = datos.correo
    document.getElementById('usuario_estado').value = datos.estado
    document.getElementById('usuario_fecha').value = datos.fecha

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0
    });

}

const limpiarTodo = () => {

    FormUsuarios.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}



const ModificarUsuario = async (event) => {

    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(FormUsuarios, [''])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
    }

    const body = new FormData(FormUsuarios);

    const url = '/carrito_pmlx/usuarios/modificarAPI';
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
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarUsuarios();

        } else {

            await Swal.fire({
                position: "center",
                icon: "info",
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


const EliminarUsuarios = async (e) => {

    const idUsuario = e.currentTarget.dataset.id

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: 'Esta completamente seguro que desea eliminar este registro',
        showConfirmButton: true,
        confirmButtonText: 'Si, Eliminar',
        confirmButtonColor: 'red',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {

        const url = `/carrito_pmlx/usuarios/eliminar?id=${idUsuario}`;
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
                    title: "Exito",
                    text: mensaje,
                    showConfirmButton: true,
                });

                BuscarUsuarios();
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



BuscarUsuarios();
datatable.on('click', '.eliminar', EliminarUsuarios);
datatable.on('click', '.modificar', llenarFormulario);
FormUsuarios.addEventListener('submit', GuardarUsuario);
usuario_nit.addEventListener('change', EsValidoNit);
InputUsuarioTelefono.addEventListener('change', ValidarTelefono);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarUsuario);