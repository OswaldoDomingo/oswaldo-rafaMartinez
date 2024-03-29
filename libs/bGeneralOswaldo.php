<?php
// include('bGeneral.php');
function sinEspaciosInermedios($frase) {
	$texto = trim(preg_replace('/ +/', '', $frase));
	return $texto;
}
// Creo este archivo donde implemento las funciones que hago para los formularios 
// Función para comprobar si es un correo válido
// Eliminar todos los espacios en blanco
// Comprobar si el nombre empieza por una letra
// Comprobar si tiene 64@192.60.3
/*
 Una dirección de correo electrónico válida debe cumplir las siguientes condiciones:
Contener "@"
La longitud de la parte local (antes del símbolo "@") debe estar comprendida entre 1 y 64 caracteres.
La longitud de la parte de dominio (después del símbolo "@") debe estar comprendida entre 4 y 255 caracteres.
La longitud total debe ser menor o igual a 256 caracteres.
La parte local y la parte de dominio deben comenzar por una letra o dígito y no deben contener dos símbolos "." consecutivos
La parte local y la parte de dominio pueden contener letras, números y los caracteres ".", "_" y "-".
La parte del dominio debe terminar con un símbolo "." y entre dos y cuatro caracteres alfabéticos.
"/^(?=.{1,256}$)([A-ZÑa-zñ0-9][A-ZÑa-zñ0-9_.-]{0,62}[A-ZÑa-zñ0-9])@([A-ZÑa-zñ0-9][A-ZÑa-zñ0-9.-]{1,252}[A-ZÑa-zñ0-9]\.[A-Za-z]{2,4})$/"
 */
$errores = [];

function cCorreo($correo, &$errores){
    $patron = "/^(?=.{1,256}$)([A-ZÑa-zñ0-9][A-ZÑa-zñ0-9_.-]{0,62}[A-ZÑa-zñ0-9])@([A-ZÑa-zñ0-9][A-ZÑa-zñ0-9.-]{1,252}[A-ZÑa-zñ0-9]\.[A-Za-z]{2,4})$/";
    $temp = "";
    $correoValido = false;
    if(isset($_REQUEST [$correo]) || trim($_REQUEST[$correo])===''){
        $temp = strip_tags($_REQUEST [$correo]); //Quitamos las etiquetas 
        $temp = sinEspaciosInermedios($temp); //Se eliminan los espacios intermedios
        $temp = trim($temp);//Sin espacios a los laterales
        //Si ser quieren quitar los acentos y caracteres especiales implementar aquí la sanitación
        if(preg_match($patron, $temp)){
            $correoValido = true;
        }else{
            $errores[$correo] = "Error en el campo $correo";
        }
    }
    return $correoValido;
 }

 // Para la contraseña ha de ser de al menos 4 caracteres y máximo de 16
 // Puede llevar letras números y caracteres especiales excepto las < >
 function cPassword($pass, &$errores){
    $passValido = false;
    $temp = "";
    $patron = '/^[a-zA-Z0-9!@#$%^&*()_+\-=\[\]{};\'":\\|,\/?~]{4,16}$/';
    if (isset($_REQUEST[$pass]) || trim($_REQUEST[$pass]) !=='') {
        $temp = strip_tags($_REQUEST[$pass]);
        $temp = sinEspaciosInermedios($temp); //Se eliminan los espacios intermedios
        $temp = trim($temp);//Sin espacios a los laterales
        if(preg_match($patron, $temp)){
            $passValido = true;
        }else{
            $errores[$pass] = "Error en el campo $pass";
    }
}
    return $passValido;
 }

 function validarFecha($fecha, &$errores, $formato = 'Y-m-d') {
    // Crear un objeto DateTime a partir de la fecha proporcionada
    $d = DateTime::createFromFormat($formato, $fecha);
        // Verificar si la fecha es válida
    $fechaValida =  $d && $d->format($formato) === $fecha;

    // Si la fecha no es válida, agregar un error al array de errores y devolver la fecha
    if(!$fechaValida){
        $errores['fechaNacimientoRegUser'] = "Error en el campo fechaNacimientoRegUser";
        return $fecha;
    }
    // Crear un objeto DateTime para la fecha actual
    $hoy = new DateTime();
     // Calcular la diferencia en años entre la fecha actual y la fecha de nacimiento
    $edad = $hoy->diff($d)->y;
    // Si la edad es menor que 18, agregar un error a la matriz de errores
    if($edad < 18){
        $errores['fechaNacimientoRegUser'] = "El usuario debe ser mayor de 18 años";
    }
    // Devolver la fecha
    return $fecha;
    
}
// Función para comprobar si el usuario y la contraseña son válidos
function usuarioValido($correo, $contrasena) {
    // Abrir el archivo de usuarios para lectura
    $archivoUsuarios = fopen("../ficheros/usuarios.txt", "r");
    // Comprueba si el archivo se abrió correctamente
    if ($archivoUsuarios) {
        // Leer el archivo línea por línea
        while (($linea = fgets($archivoUsuarios)) !== false) {
            // Descomponer la línea en sus partes y comprobar si coinciden con los datos del formulario
            // explode() divide la línea en un array usando ';' como delimitador
            // list() asigna cada elemento del array a una variable
            list($fechaAlta, $nombre, $correoArchivo, $claveArchivo, $fechaNacimiento, $rutaFoto, $idioma, $descripcion) = explode(';', trim($linea));
            // Comprueba si el correo y la contraseña proporcionados coinciden con los del archivo
            if ($correo === $correoArchivo && $contrasena === $claveArchivo) {
                // Si los datos coinciden, cerrar el archivo y retornar verdadero
                fclose($archivoUsuarios);
                return true;
            }
        }
        // Cerrar el archivo si no se encuentra el usuario
        fclose($archivoUsuarios);
    }
    // Retornar falso si el usuario no es válido
    return false;
} 