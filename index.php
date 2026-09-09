<?php
require 'cliente-pdo.php';

// Iniciar sesión
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  if (!isset($_POST['usuario']) || empty($_POST['usuario']) || !isset($_POST['password']) || empty($_POST['password'])) {
    $info = 'Debes rellenar el campo usuario y contraseña';
  } else {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];
    $consulta = "SELECT * FROM sqlrank_usuarios WHERE usuario=?";
    $filas = consulta_pdo_corrector($consulta, array($usuario));

    if (count($filas) == 1 && password_verify($password, $filas[0]['password'])) {
      $_SESSION['loguedUser'] = $filas[0]['usuario'];
      $_SESSION['IDloguedUser'] = $filas[0]['id_usuario'];
      $_SESSION['rol'] = $filas[0]['rol'];
      header("location: /cuestionario.php?categoria=sql01-consultas");
    } else {
      $info = "Error. No se ha podido iniciar sesión";
    }
  }
}


?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <title>SQLRank 2.0</title>
</head>

<body>

  <div class="d-flex flex-column overflow-hidden min-vh-100 vh-100">

    <header style="color: white; background-color: #6699ff;">
      <h1 class="text-center">BASES DE DATOS</h1>
      <h2 class="text-center">Corrector automático de prácticas SQL</h2>
    </header>

    <main role="main" class="flex-grow-1 overflow-auto">
      <div class="container col-xs-12 col-sm-6 col-md-4 col-lg-3 ">
        <div class="row mt-3">
          <div class="col">
            <form id="formulario" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
              <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Login</h3>

              <div class="mb-3">
                <label for="usuario" class="form-label">Nombre de usuario</label>
                <input type="text" class="form-control" name="usuario" id="usuario">

              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password">
              </div>

              <button type="submit" class="btn btn-primary">Aceptar</button>
            </form>

          </div>
        </div>
      </div>
    </main>

    <footer class="container-fluid text-center flex-grow-0 flex-shrink-1" style="color: white; background-color: #6699ff;">
      <small>SQLRank 2.0</small>
    </footer>

  </div>

</body>

</html>