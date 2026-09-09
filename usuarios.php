<?php

require 'cliente-pdo.php';

// Iniciar sesión
session_start();

if (!(isset($_SESSION['loguedUser']) && $_SESSION['rol'] == 'admin')) {
  header("location: /index.php");
}

try {

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['Aceptar'])) {
      /*** almacenar en variables los datos recibidos del formulario ***/
      $id_usuario = $_POST['id_usuario'] ?? null;
      $usuario = $_POST['usuario'] ?? null;
      $password = $_POST['password'] ?? null;
      $password2 = $_POST['password2'] ?? null;
      $rol = $_POST['rol'] ?? null;

      /*** operación UPDATE EN LA BASE DE DATOS ***/
      if ($id_usuario) {

        if ((!isset($_POST['password']) || empty($_POST['password'])) && (!isset($_POST['password2']) || empty($_POST['password2']))) {
          comando_pdo_corrector("UPDATE sqlrank_usuarios SET usuario=?,rol=? WHERE id_usuario=?", array($usuario,  $rol, $id_usuario));
        } elseif (isset($_POST['password']) && !empty($_POST['password']) && isset($_POST['password2']) && !empty($_POST['password2']) &&  $_POST['password'] === $_POST['password2']) {
          $password = password_hash($password, PASSWORD_BCRYPT);
          comando_pdo_corrector("UPDATE sqlrank_usuarios SET usuario=?,password=?,rol=? WHERE id_usuario=?", array($usuario, $password, $rol, $id_usuario));
        } else {
          throw new Exception("Ambas contraseñas deben coincidir");
        }
      }
      /*** operación INSERT EN LA BASE DE DATOS ***/
      else {

        if (!isset($_POST['password']) || empty($_POST['password'])) {
          throw new Exception("La contraseña no puede quedar vacía");
        }
        if (!isset($_POST['password2']) || empty($_POST['password2']) ||  $_POST['password'] !== $_POST['password2']) {
          throw new Exception("Ambas contraseñas deben coincidir");
        }

        $password = password_hash($password, PASSWORD_BCRYPT);
        comando_pdo_corrector("INSERT INTO sqlrank_usuarios (usuario,password,rol) VALUES (?, ?, ?)", array($usuario, $password, $rol));
      }
    } elseif (isset($_POST['Borrar'])) {
      $id_usuario = $_POST['id_usuario'] ?? null;
      comando_pdo_corrector("DELETE FROM sqlrank_usuarios WHERE id_usuario=?", array($id_usuario));
    }
  }
} catch (Exception $e) {
  echo "Se ha producido una excepción mysql";
}

try {
  $usuarios = consulta_pdo_corrector("SELECT * FROM sqlrank_usuarios");
} catch (Exception $e) {
  echo "Se ha producido una excepción mysql";
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Gestión de usuarios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body>




  <div class="d-flex" style="min-height: 100vh;">

    <!-- Sidebar -->


    <div class="bg-dark text-white"
      style="width:220px; height:100vh; position:fixed; top:0; left:0; flex-shrink:0; overflow-y:auto;">
      <?php require('menu.php'); ?>
    </div>

    <!-- Contenido principal -->
    <div class="flex-grow-1 p-3" style="margin-left:220px; margin-top:20px; margin-bottom:50px;">
      <div class="container-fluid">




        <h1>Gestión de usuarios</h1>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal" onclick="limpiar_formulario()">Nuevo usuario</button>

        <table class="table">
          <tr>
            <th>id_usuario</th>
            <th>usuario</th>

            <th>rol</th>
            <th colspan='2'>acciones</th>
          </tr>
          <?php foreach ($usuarios as $usuario) : ?>
            <tr>
              <td> <?= $usuario['id_usuario'] ?> </td>
              <td> <?= $usuario['usuario'] ?> </td>

              <td> <?= $usuario['rol'] ?> </td>
              <td> <input type="submit" class="btn btn-primary" name="Editar" value="Editar" data-bs-toggle="modal" data-bs-target="#myModal" onclick="rellenar_formulario(event)"> </td>
              <td>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method='post'>
                  <input type='hidden' name='id_usuario' value="<?= $usuario['id_usuario'] ?>">
                  <input type="submit" class="btn btn-danger" name="Borrar" value="Borrar">
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </table>

        <!-- The Modal -->
        <div class="modal" id="myModal">
          <div class="modal-dialog">
            <div class="modal-content">

              <!-- Modal Header -->
              <div class="modal-header">
                <h4 class="modal-title">Usuarios</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <!-- Modal body -->
              <div class="modal-body">

                <form id="formulario" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                  <label for="id_usuario">Id_usuario:</label><br>
                  <input type="text" id="id_usuario" name="id_usuario" readonly><br>
                  <label for="usuario">Usuario:</label><br>
                  <input type="text" id="usuario" name="usuario"><br>
                  <label for="password">Password:</label><br>
                  <input type="text" minlength="24" id="password" name="password"><br>
                  <label for="password2">Repetir Password:</label><br>
                  <input type="text" minlength="24" id="password2" name="password2"><br>
                  <label for="rol">Rol:</label><br>
                  <input type="text" id="rol" name="rol"><br>

                  <br><br>

                </form>

              </div>

              <!-- Modal footer -->
              <div class="modal-footer">
                <input type="submit" class="btn btn-primary" name="Aceptar" value="Aceptar" form="formulario"><br>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Cerrar</button>
              </div>

            </div>
          </div>
        </div>


      </div>
    </div>
  </div>


  <!--/div-->

  <script>
    function rellenar_formulario(event) {
      const columnas = event.target.parentElement.parentElement.children;
      document.getElementById("id_usuario").value = columnas[0].innerText;
      document.getElementById("usuario").value = columnas[1].innerText;
      document.getElementById("rol").value = columnas[2].innerText;
    }

    function limpiar_formulario() {
      document.getElementById("formulario").reset();
    }
  </script>
</body>

</html>