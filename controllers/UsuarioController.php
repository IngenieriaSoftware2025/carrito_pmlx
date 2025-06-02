<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Usuarios;
use MVC\Router;

class UsuarioController extends ActiveRecord
{

    public static function renderizarPagina(Router $router)
    {
        $router->render('usuarios/index', []);
    }

    public static function guardarAPI()
    {

        getHeadersApi();

        // echo json_encode($_POST);
        // return;


        $_POST['usuario_apellidos'] = htmlspecialchars($_POST['usuario_apellidos']);

        $cantidad_apellidos = strlen($_POST['usuario_apellidos']);

        if ($cantidad_apellidos < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el apellido debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['usuario_nombres'] = htmlspecialchars($_POST['usuario_nombres']);

        $cantidad_nombres = strlen($_POST['usuario_nombres']);


        if ($cantidad_nombres < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'La cantidad de digitos que debe de contener el nombre debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['usuario_telefono'] = filter_var($_POST['usuario_telefono'], FILTER_VALIDATE_INT);

        if (strlen($_POST['usuario_telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'La cantidad de digitos de telefono debe de ser igual a 8'
            ]);
            return;
        }

        $_POST['usuario_nit'] = filter_var($_POST['usuario_nit'], FILTER_SANITIZE_NUMBER_INT);
        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);

        if (!filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'El correo electronico ingresado es invalido'
            ]);
            return;
        }
        $_POST['usuario_estado'] = htmlspecialchars($_POST['usuario_estado']);

        $_POST['usuario_fecha'] = date('Y-m-d H:i:s', strtotime($_POST['usuario_fecha']));

        $estado = $_POST['usuario_estado'];

        if ($estado == "P" || $estado == "F" || $estado == "C") {


            try {


                // $data = new Usuarios();

                $data = new Usuarios([
                    'usuario_nombres' => $_POST['usuario_nombres'],
                    'usuario_apellidos' => $_POST['usuario_apellidos'],
                    'usuario_nit' => $_POST['usuario_nit'],
                    'usuario_telefono' => $_POST['usuario_telefono'],
                    'usuario_correo' => $_POST['usuario_correo'],
                    'usuario_estado' => $_POST['usuario_estado'],
                    'usuario_fecha' => $_POST['usuario_fecha'],
                    'usuario_situacion' => 1
                ]);

                $crear = $data->crear();

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Exito el usuario ha sido registrado correctamente'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar',
                    'detalle' => $e->getMessage(),
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'Los detinos solo puedes ser "P, F, C"'
            ]);
            return;
        }
    }

    public static function buscarAPI()
    {

        try {

            // $data = Usuarios::all();

            $sql = "SELECT * FROM usuarios where usuario_situacion = 1";
            $data = self::fetchArray($sql);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios obtenidos correctamente',
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener los usuarios',
                'detalle' => $e->getMessage(),
            ]);
        }
    }


    public static function modificarAPI()
    {

        getHeadersApi();

        $id = $_POST['usuario_id'];
        $_POST['usuario_apellidos'] = htmlspecialchars($_POST['usuario_apellidos']);

        $cantidad_apellidos = strlen($_POST['usuario_apellidos']);

        if ($cantidad_apellidos < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La cantidad de digitos que debe de contener el apellido debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['usuario_nombres'] = htmlspecialchars($_POST['usuario_nombres']);

        $cantidad_nombres = strlen($_POST['usuario_nombres']);


        if ($cantidad_nombres < 2) {

            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'La cantidad de digitos que debe de contener el nombre debe de ser mayor a dos'
            ]);
            return;
        }

        $_POST['usuario_telefono'] = filter_var($_POST['usuario_telefono'], FILTER_VALIDATE_INT);

        if (strlen($_POST['usuario_telefono']) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'La cantidad de digitos de telefono debe de ser igual a 8'
            ]);
            return;
        }

        $_POST['usuario_nit'] = filter_var($_POST['usuario_nit'], FILTER_SANITIZE_NUMBER_INT);
        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);
        $_POST['usuario_fecha'] = date('Y-m-d H:i', strtotime($_POST['usuario_fecha']));

        if (!filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'El correo electronico ingresado es invalido'
            ]);
            return;
        }
        $_POST['usuario_estado'] = htmlspecialchars($_POST['usuario_estado']);

        $estado = $_POST['usuario_estado'];

        if ($estado == "P" || $estado == "F" || $estado == "C") {


            try {


                $data = Usuarios::find($id);
                // $data->sincronizar($_POST);
                $data->sincronizar([
                    'usuario_nombres' => $_POST['usuario_nombres'],
                    'usuario_apellidos' => $_POST['usuario_apellidos'],
                    'usuario_nit' => $_POST['usuario_nit'],
                    'usuario_telefono' => $_POST['usuario_telefono'],
                    'usuario_correo' => $_POST['usuario_correo'],
                    'usuario_fecha' => $_POST['usuario_fecha'],
                    'usuario_situacion' => 1
                ]);
                $data->actualizar();

                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'La informacion del usuario ha sido modificada exitosamente'
                ]);
            } catch (Exception $e) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al guardar',
                    'detalle' => $e->getMessage(),
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mennsaje' => 'Los detinos solo puedes ser "P, F, C"'
            ]);
            return;
        }
    }

    public static function EliminarAPI()
    {

        try {

            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            // $data = Usuarios::find($id);
            // $data->sincronizar([
            // 'usuario_situacion' => 0,
            // ]);
            // $data->actualizar();

            // $data = Usuarios::find($id);
            // $data->eliminar();


            $ejecutar = Usuarios::EliminarUsuarios($id);


            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El registro ha sido eliminado correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al Eliminar',
                'detalle' => $e->getMessage(),
            ]);
        }
    }
}
