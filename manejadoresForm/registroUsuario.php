<?php
session_start();
include(__DIR__ . "/../libs/bGeneral.php");
include(__DIR__ . "/../libs/funcionesFicheros.php");
include(__DIR__ . "/../libs/bGeneralOswaldo.php");
include(__DIR__ . "/../libs/config.php");

//muestra los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Directorio donde se guardan los datos del usuario
$ruta = RUTA_IMAGENES . "usuarios/";
$errores = [];
/**
 * nomnbreRegUser
 * correoRegUser
 * contrasenyaRegUser
 * fechaNacimientoRegUser
 * fotoPerfilRegUser
 * idiomaRegUser
 * comentariosRegUser
 * enviarRegUser
 */
if (isset($_POST['enviarRegUser'])) {
    //Recoger valores
    $nombre = recoge("nomnbreRegUser");

    echo "Punto de depuración 1: antes de validar correo<br>";
    $correo = cCorreo("correoRegUser", $errores);
    if($correo){$correoValidado = $_POST["correoRegUser"];}
    echo "Punto de depuración 2: después de validar correo<br>";

    echo "Punto de depuración 1: antes de validar pass<br>";
    $password = cPassword("contrasenyaRegUser", $errores);
    if($password){ $passwordValidado = $_POST["contrasenyaRegUser"];}
    echo "Punto de depuración 2: después de validar pass<br>";

    $rutaImagen = "../imagenes/usuarios/";

    //Valida fecha
    //Recogemos fecha si es buena, 
    echo "Punto de depuración 1: antes de validar fecha<br>";
    $fechaNacimiento = validarFecha($_POST['fechaNacimientoRegUser'], $errores);
    echo "Punto de depuración 2: después de validar fecha<br>";

    echo "Punto de depuración 1: antes de validar fofo<br>";
    $fotoPerfil = recoge("fotoPerfilRegUser"); //Hacer función que compruebe que es una imagen
    echo "Punto de depuración 2: después de validar foto<br>";

    //El idioma solo puede ser una de dos opciones, castellano o ingles. Poro ahora solo sanitizo con recoge() 
    echo "Punto de depuración 1: antes de idioma<br>";
    //$idioma = recoge("idiomaRegUser"); //Hacer función que compruebe si es español o ingles
    
    //CAMBIOS DE RECOGER CHECK BOX*************
    // Recoger los valores de los checkboxes
    $idiomas = isset($_POST['idiomaRegUser']) ? $_POST['idiomaRegUser'] : [];
    // Definir los valores válidos
    $valoresValidos = ['ingles', 'castellano'];
    // Llamar a cCheck con los valores de los checkboxes
    $valido = cCheck($idiomas, 'idiomaRegUser', $errores, $valoresValidos, false);
    // Si cCheck devuelve true, devolver los idiomas seleccionados
    // Si cCheck devuelve true, almacenar los idiomas seleccionados en una variable
    $idiomasSeleccionados = [];
    if ($valido) {
        $idiomasSeleccionados = $idiomas;
    }
    $idiomasSeleccionadosString = implode(", ", $idiomasSeleccionados);
    echo "Punto de depuración 2: después de idioma<br>";
    //FIN CAMBIOS DE RECOGER CHECK BOX*************

    //Recoger los comentarios se sanitiza con recoge y se cambian los saltos linea por <br>
    echo "Punto de depuración 1: antes de comentario<br>";
    $comentarios = recoge("comentariosRegUser");
    $comentarios = str_replace(PHP_EOL, "<br>", $comentarios);
    echo "Punto de depuración 2: después de comentario<br>";

    echo "<b>Punto de depuración: después de todas las validaciones<b><br>";
    var_dump($errores);
    
    $fotoPerfil= "";

    if(isset ($_FILES["fotoPerfilRegUser"]) && isset ($_FILES["fotoPerfilRegUser"]["name"]) && $_FILES["fotoPerfilRegUser"]["name"] != "")
    {
        $fotoPerfil = $ruta . $_FILES["fotoPerfilRegUser"]["name"];
    }  
    
    //Fecha de alta al servicio
    $fechaAlta = date("d-m-Y H:i:s");

    if (empty($errores)) {
        //Ponerle nombre a la foto y subirla al directorio $rutaImagen, averiguar el array de formatos

        //Todos estos datos debemos escribirlos en un fichero cada registro separado por un ";"
        $datos_usuario = "$fechaAlta;$nombre;$correoValidado;$passwordValidado;$fechaNacimiento;$fotoPerfil;$idiomasSeleccionadosString;$comentarios\r\n";

        // Abrir/Crear archivo
        $archivo = fopen(__DIR__ . '/../ficheros/usuarios.txt', 'a');

        // Escribir en el archivo
        fwrite($archivo, $datos_usuario);

        // Cerrar archivo
        fclose($archivo);
        header("Location: ../manejadoresForm/login.php");
        exit();
    } else {
        
        $mensajeError = "<br><br><br><br><div><b>" . $messageError . array_values($errores)[0] . "</b></div>";
        echo($mensajeError);
        
    }
} else {
    include("../vistas/formRegistroUsuario.php");
}
