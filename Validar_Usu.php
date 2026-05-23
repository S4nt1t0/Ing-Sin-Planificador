<?php 
session_start(); 
mysqli_report(MYSQLI_REPORT_OFF);
$link = mysqli_connect("localhost", "santito", "DBZczspoponp10!", "SistemasII");


if (!$link) {
    die("Error de conexión: " . mysqli_connect_error());
}

$usu= $_REQUEST['Nombre']; 
$pas= $_REQUEST['Password'];

$result = mysqli_query($link, "SELECT Id_Usuario, Nombre, Correo, Password FROM usuario WHERE Nombre='$usu'");

if ($row = mysqli_fetch_array($result)) {
    if ($row["Password"] == $pas) {
        
        // guardamos las datos de sesion del usuario
        $_SESSION["k_username"]=$row['Nombre'];           
        $_SESSION["id_usuario"]=$row['Id_Usuario'];
        $_SESSION["usuario"]=$row['Nombre'];
        $_SESSION["correo"]=$row['Correo'];
        
        header("Location: IndexPrincipal.php");
        exit();
    } else {
        header("Location: Index.php");
        exit();
    }
} else {
    header("Location: Index.php");
    exit();
}
