<?php
require 'cliente-pdo.php';

// Iniciar sesión
session_start();

if (isset($_SESSION['loguedUser'])) {
    $_SESSION['loguedUser'] = null;
    unset($_SESSION['loguedUser']);
    header("location: /index.php");
}
