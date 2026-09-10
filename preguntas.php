<?php

require 'cliente-pdo.php';

// Iniciar sesión
session_start();

if (!(isset($_SESSION['loguedUser']) && $_SESSION['rol'] == 'admin')) {
  header("location: /index.php");
}

try {

$categoria = $_GET["categoria"] ?? "";

  if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['Aceptar'])) {
      /*** almacenar en variables los datos recibidos del formulario ***/
      $id_pregunta = $_POST['id_pregunta'] ?? null;
      $numero = $_POST['numero'] ?? null;
      $enunciado = $_POST['enunciado'] ?? null;
      $solucion = $_POST['solucion'] ?? null;
      $id_categoria = $_POST['id_categoria'] ?? null;

      /*** operación UPDATE EN LA BASE DE DATOS ***/
      if ($id_pregunta) {

        comando_pdo_corrector("UPDATE sqlrank_preguntas SET numero=?,enunciado=?,solucion=?,id_categoria=? WHERE id_pregunta=?", array($numero, $enunciado, $solucion, $id_categoria, $id_pregunta));
      }
      /*** operación INSERT EN LA BASE DE DATOS ***/
      else {

        comando_pdo_corrector("INSERT INTO sqlrank_preguntas (numero,enunciado,solucion,id_categoria) VALUES (?, ?, ?, ?)", array($numero, $enunciado, $solucion, $id_categoria));
      }
    } elseif (isset($_POST['Borrar'])) {
      $id_pregunta = $_POST['id_pregunta'] ?? null;
      comando_pdo_corrector("DELETE FROM sqlrank_preguntas WHERE id_pregunta=?", array($id_pregunta));
    }
  }

  /*** guardar en el array $preguntas todas las preguntas de la base de datos ***/
  $preguntas = consulta_pdo_corrector("SELECT p.*,c.descripcion as categoria FROM sqlrank_preguntas p left join sqlrank_categorias c on c.id_categoria=p.id_categoria where c.descripcion=? order by c.descripcion, p.numero",array($categoria));
  /*** guardar en el array $categorias todas las categorias de la base de datos ***/
  $categorias = consulta_pdo_corrector("SELECT * FROM sqlrank_categorias");
} catch (Exception $e) {
  echo "Se ha producido una excepción mysql";
}


?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Gestión de preguntas</title>
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




        <h1>Gestión de preguntas</h1>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#myModal" onclick="limpiar_formulario()">Nueva pregunta</button>

        <table class="table">
          <tr>
            <th>id_pregunta</th>
            <th>numero</th>
            <th>enunciado</th>
            <th>solucion</th>
            <th>id_categoria</th>
            <th>categoria</th>
            <th colspan='2'>acciones</th>
          </tr>
          <?php foreach ($preguntas as $pregunta) : ?>
            <tr>
              <td> <?= $pregunta['id_pregunta'] ?> </td>
              <td> <?= $pregunta['numero'] ?> </td>
              <td> <?= $pregunta['enunciado'] ?> </td>
              <td> <?= $pregunta['solucion'] ?> </td>
              <td> <?= $pregunta['id_categoria'] ?> </td>
              <td> <?= $pregunta['categoria'] ?> </td>
              <td> <input type="submit" class="btn btn-primary" name="Editar" value="Editar" data-bs-toggle="modal" data-bs-target="#myModal" onclick="rellenar_formulario(event)"> </td>
              <td>
                <form action="<?= $_SERVER['PHP_SELF'] ?>" method='post'>
                  <input type='hidden' name='id_pregunta' value="<?= $pregunta['id_pregunta'] ?>">
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
                <h4 class="modal-title">Preguntas</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <!-- Modal body -->
              <div class="modal-body">


        <form id="formulario" method="post" action="<?= $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] ?>" enctype="multipart/form-data">



                  <label for="id_pregunta">id_pregunta:</label><br>
                  <input type="text" id="id_pregunta" name="id_pregunta" readonly><br>
                  <label for="numero">Número:</label><br>
                  <input type="text" id="numero" name="numero"><br>

                  <label for="enunciado">Enunciado:</label><br>
                  <!--input type="text" id="enunciado" name="enunciado"><br-->
                  <textarea class="form-control" id="enunciado" name="enunciado" rows="5" cols="100"></textarea><br>

                  <label for="solucion">Solución:</label><br>
                  <!--input type="text" id="solucion" name="solucion"><br-->
                  <textarea class="form-control" id="solucion" name="solucion" rows="5" cols="100"></textarea><br>


                  <label for="id_categoria">Categoría:</label><br>
                  <select name="id_categoria" id="id_categoria">
                    <?php foreach ($categorias as $categoria): ?>
                      <option value="<?= $categoria['id_categoria'] ?>"><?= $categoria['descripcion'] ?></option>
                    <?php endforeach; ?>
                  </select>

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
      document.getElementById("id_pregunta").value = columnas[0].innerText;
      document.getElementById("numero").value = columnas[1].innerText;
      document.getElementById("enunciado").value = columnas[2].innerText;
      document.getElementById("solucion").value = columnas[3].innerText;
      document.getElementById("id_categoria").value = columnas[4].innerText;
    }

    function limpiar_formulario() {
      document.getElementById("formulario").reset();
    }
  </script>
</body>

</html>