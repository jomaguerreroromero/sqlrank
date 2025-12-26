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
      $id_categoria = $_POST['id_categoria'] ?? null;
      $descripcion = $_POST['descripcion'] ?? null;
      $activo = $_POST['activo'] ?? 0;

      /*** operación UPDATE EN LA BASE DE DATOS ***/
      if ($id_categoria) {
        comando_pdo_sql("UPDATE sqlrank_categorias SET descripcion=?,activo=? WHERE id_categoria=?", array($descripcion, $activo, $id_categoria));
      }
      /*** operación INSERT EN LA BASE DE DATOS ***/
      else {
        comando_pdo_sql("INSERT INTO sqlrank_categorias (descripcion,activo) VALUES (?,?)", array($descripcion, $activo));
      }
    } elseif (isset($_POST['Borrar'])) {
      $id_categoria = $_POST['id_categoria'] ?? null;
      comando_pdo_sql("DELETE FROM sqlrank_categorias WHERE id_categoria=?", array($id_categoria));
    }
  }

  $categorias = consulta_pdo_sql("SELECT * FROM sqlrank_categorias");
} catch (Exception $e) {
  echo "Se ha producido una excepción mysql";
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Gestión de categorías</title>
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

        <h1>Gestión de categorías</h1>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal" onclick="limpiar_formulario()">Nueva categoría</button>

        <table class="table">
          <tr>
            <th>id_categoria</th>
            <th>descripcion</th>
            <th>activo</th>
            <th colspan='2'>acciones</th>
          </tr>
          <?php foreach ($categorias as $categoria) : ?>
            <tr>
              <td> <?= $categoria['id_categoria'] ?> </td>
              <td> <?= $categoria['descripcion'] ?> </td>
              <td> <?= $categoria['activo'] ?> </td>
              <td> <input type="submit" class="btn btn-primary" name="Editar" value="Editar" data-bs-toggle="modal" data-bs-target="#myModal" onclick="rellenar_formulario(event)"> </td>
              <td>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method='post'>
                  <input type='hidden' name='id_categoria' value="<?= $categoria['id_categoria'] ?>">
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
                <h4 class="modal-title">Categorías</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <!-- Modal body -->
              <div class="modal-body">

                <form id="formulario" action="<?= $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
                  <label for="id_categoria">Id_categoria:</label><br>
                  <input type="text" id="id_categoria" name="id_categoria" readonly><br>

                  <label for="descripcion">Descripción:</label><br>
                  <input type="text" id="descripcion" name="descripcion"><br>

                  <label for="activo">Activo:</label><br>
                  <input type="checkbox" id="activo" name="activo" value="1"><br>

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

        <!--/div-->

      </div>
    </div>
  </div>

  <script>
    function rellenar_formulario(event) {
      const columnas = event.target.parentElement.parentElement.children;
      document.getElementById("id_categoria").value = columnas[0].innerText;
      document.getElementById("descripcion").value = columnas[1].innerText;
      document.getElementById("activo").checked = columnas[2].innerText == "1" ? true : false;

    }

    function limpiar_formulario() {
      document.getElementById("formulario").reset();
    }
  </script>
</body>

</html>