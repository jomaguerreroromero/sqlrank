<?php

require 'cliente-pdo.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    try {

        if (!isset($_POST['password']) || empty($_POST['password'])) {
            $info = 'La contraseña no puede quedar vacía';
        } elseif (!isset($_POST['password2']) || empty($_POST['password2']) ||  $_POST['password'] !== $_POST['password2']) {
            $info = 'Ambas contraseñas deben coincidir';
        } else {

            /** escribir en la variable $pass el password encriptado **/
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            comando_pdo_corrector("INSERT INTO sqlrank_usuarios (usuario,password,rol) VALUES (?, ?, ?)", array('admin', $password, 'admin'));

            $info = "Usuario administrador creado satisfactoriamente";
        }
    } catch (Exception $e) {
        $info = "Error. No se ha podido crear el usuario administrador";
    }
}


?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <title>Alta del administrador</title>
</head>

<body>

    <div class="container-fluid mt-3">

        <h1>Alta del administrador</h1>
        <form id="formulario" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">

            <label for="password">Contraseña</label><br>
            <input type="text" name="password" id="password" /><br>
            <label for="password2">Repetir contraseña</label><br>
            <input type="text" name="password2" id="password2" /><br>
            <input type="submit" value="Enviar" />
        </form>
        <p><?= $info ?></p>

    </div>
</body>

</html>