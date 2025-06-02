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
    // Limpiar cualquier output buffer
    if (ob_get_contents()) ob_clean();
    
    // Headers para JSON
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');

    try {
        // Verificar que es una petición POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Método no permitido. Use POST.'
            ]);
            exit;
        }

        // Verificar que el ID esté presente
        if (!isset($_POST['usuario_id']) || empty($_POST['usuario_id'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de usuario requerido'
            ]);
            exit;
        }

        $id = filter_var($_POST['usuario_id'], FILTER_SANITIZE_NUMBER_INT);

        // Verificar campos requeridos
        $camposRequeridos = [
            'usuario_nombres', 'usuario_apellidos', 'usuario_telefono', 
            'usuario_nit', 'usuario_correo', 'usuario_estado', 'usuario_fecha'
        ];
        
        foreach ($camposRequeridos as $campo) {
            if (!isset($_POST[$campo]) || trim($_POST[$campo]) === '') {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => "Campo requerido faltante: $campo"
                ]);
                exit;
            }
        }

        // Limpiar y validar datos
        $nombres = htmlspecialchars(trim($_POST['usuario_nombres']));
        $apellidos = htmlspecialchars(trim($_POST['usuario_apellidos']));
        
        if (strlen($nombres) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre debe tener al menos 2 caracteres'
            ]);
            exit;
        }

        if (strlen($apellidos) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido debe tener al menos 2 caracteres'
            ]);
            exit;
        }

        // Validar teléfono
        $telefono = filter_var($_POST['usuario_telefono'], FILTER_SANITIZE_NUMBER_INT);
        if (!$telefono || strlen($telefono) != 8) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El teléfono debe tener exactamente 8 dígitos'
            ]);
            exit;
        }

        // Validar email
        $correo = filter_var(trim($_POST['usuario_correo']), FILTER_VALIDATE_EMAIL);
        if (!$correo) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico no es válido'
            ]);
            exit;
        }

        // Validar estado
        $estado = htmlspecialchars(trim($_POST['usuario_estado']));
        if (!in_array($estado, ['P', 'F', 'C'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El estado debe ser P, F o C'
            ]);
            exit;
        }

        // Validar y formatear fecha
        $fecha = $_POST['usuario_fecha'];
        if (empty($fecha)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La fecha es requerida'
            ]);
            exit;
        }

        // Convertir fecha a formato de base de datos
        $fechaFormateada = date('Y-m-d H:i:s', strtotime($fecha));
        if (!$fechaFormateada) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Formato de fecha inválido'
            ]);
            exit;
        }

        // Buscar el usuario
        $usuario = Usuarios::find($id);
        if (!$usuario) {
            http_response_code(404);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Usuario no encontrado'
            ]);
            exit;
        }

        // Actualizar datos
        $usuario->sincronizar([
            'usuario_nombres' => $nombres,
            'usuario_apellidos' => $apellidos,
            'usuario_nit' => filter_var($_POST['usuario_nit'], FILTER_SANITIZE_NUMBER_INT),
            'usuario_telefono' => $telefono,
            'usuario_correo' => $correo,
            'usuario_estado' => $estado,
            'usuario_fecha' => $fechaFormateada,
            'usuario_situacion' => 1
        ]);

        $resultado = $usuario->actualizar();

        if ($resultado) {
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuario modificado exitosamente'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al actualizar el usuario'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'Error interno del servidor',
            'detalle' => $e->getMessage()
        ]);
    }
    
    exit;
}

   public static function eliminarAPI()
{
    try {
        $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        // Verificar que el ID sea válido
        if (!$id || $id <= 0) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'ID de usuario inválido'
            ]);
            return;
        }

        // Buscar el usuario
        $data = Usuarios::find($id);
        
        if (!$data) {
            http_response_code(404);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Usuario no encontrado'
            ]);
            return;
        }

        // ELIMINACIÓN LÓGICA: Cambiar situación a 0 en lugar de eliminar físicamente
        $data->sincronizar([
            'usuario_situacion' => 0
        ]);
        
        $resultado = $data->actualizar();

        if ($resultado) {
            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El registro ha sido eliminado correctamente'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al actualizar el registro'
            ]);
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'codigo' => 0,
            'mensaje' => 'Error al eliminar usuario',
            'detalle' => $e->getMessage(),
        ]);
    }
}
}
