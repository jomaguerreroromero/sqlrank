<?php

require 'cliente-pdo.php';

// Iniciar sesión
session_start();

if (!isset($_SESSION['loguedUser'])) {
  header("location: /index.php");
}

$categoria = $_GET["categoria"] ?? "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $errores = [];
  $i = 0;



  //leer las respuestas anteriores del usuario en esta categoria
  $respuestas_anteriores = consulta_pdo_sql("SELECT r.id_pregunta,r.respuesta,r.calificacion FROM sqlrank_respuestas r JOIN sqlrank_preguntas p on p.id_pregunta=r.id_pregunta JOIN sqlrank_categorias c on c.id_categoria=p.id_categoria where r.id_usuario=? and c.descripcion=?", array($_SESSION['IDloguedUser'], $categoria));



  foreach ($_POST as $key => $value) {

    $errores[$i] = false;

    //buscar respuesta anterior del usuario
    $respuesta_anterior = null;
    foreach ($respuestas_anteriores as $resp) {
      if ($resp['id_pregunta'] == $key) {
        $respuesta_anterior = $resp['respuesta'];
        $calificacion_anterior = $resp['calificacion'];
        break;
      }
    }

    if (trim($value) !== "" && !($respuesta_anterior !== null && $respuesta_anterior === $value && $calificacion_anterior === 1)) {

      //sustituir dobles comillas por comillas simples
      $respuesta_usuario = trim($value);
      $respuesta_usuario = str_replace('"', "'", $respuesta_usuario);
      $respuesta_usuario = (substr($respuesta_usuario, -1) === ";") ? substr($respuesta_usuario, 0, -1) : $respuesta_usuario;

      //comprobar cada respuesta
      try {

        //realizar la consulta recibida 
        $filas = consulta_pdo_sql($respuesta_usuario);

        //obtener la solución
        $sql_solucion = consulta_pdo_sql("select solucion from sqlrank_preguntas where id_pregunta=?", array($key));

        //realizar la consulta solucion
        $filas_sql_solucion = consulta_pdo_sql($sql_solucion[0]['solucion']);;

        //realizar la unión de los resultados de las consultas anteriores  
        $filas_sql_union = consulta_pdo_sql("(" . $sql_solucion[0]['solucion'] . ") union (" . $respuesta_usuario . ")");

        //guardar en la variable mensaje el resultado de comprobar si la consulta recibida es correcta
        if (count($filas) == count($filas_sql_solucion) && count($filas) == count($filas_sql_union)) {
          $calificacion = 1;
        } else {
          $calificacion = 0;
        }
      } catch (Exception $e) {

        $calificacion = null;
      }

      // grabar cada respuesta en la base de datos con su calificación: 1(bien), 0(mal) y null(desconocido)
      try {
        $filas = consulta_pdo_sql("SELECT count(*) as cantidad FROM sqlrank_respuestas where id_pregunta=? and id_usuario=?",  array($key, $_SESSION['IDloguedUser']));

        if ($filas[0]['cantidad'] == 0) {
          comando_pdo_sql("INSERT INTO sqlrank_respuestas (id_pregunta,id_usuario,respuesta,calificacion) VALUES (?, ?, ?, ?)",  array($key, $_SESSION['IDloguedUser'], $value, $calificacion));
        } else {
          comando_pdo_sql("UPDATE sqlrank_respuestas SET respuesta=?, calificacion=? WHERE id_pregunta=? and id_usuario=?",  array($value, $calificacion, $key, $_SESSION['IDloguedUser']));
        }
      } catch (Exception $e) {
        echo "Se ha producido una excepción mysql";
        $errores[$i] = true;
      }
    }
    ++$i;
  }
}

try {

  /*** guardar en el array $preguntas todas las preguntas de la base de datos ***/
  $preguntas = consulta_pdo_sql("SELECT p.*,r.respuesta,r.calificacion,c.descripcion FROM sqlrank_preguntas p left join sqlrank_categorias c on c.id_categoria=p.id_categoria left join sqlrank_respuestas r on r.id_pregunta=p.id_pregunta and r.id_usuario=? where c.descripcion=? ", array($_SESSION['IDloguedUser'], $categoria));
} catch (Exception $e) {
  echo "Se ha producido una excepción mysql";
}

?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <title><?= $categoria ?></title>
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

        <form id="formulario" method="post" action="<?= $_SERVER['PHP_SELF'] . '?' . $_SERVER['QUERY_STRING'] ?>">

          <ol>
            <?php $i = 0;
            foreach ($preguntas as $pregunta):       ?>

              <li><?= $pregunta['enunciado']; ?>

                <textarea class="form-control" name='<?= $pregunta['id_pregunta'] ?? "" ?>' rows="10" cols="100"><?= $pregunta['respuesta'] ?? "" ?></textarea>

                <?php if (isset($errores[$i]) && $errores[$i] === true): ?>
                  <div class="alert alert-danger">Error en la operación INSERT/UPDATE</div>
                <?php elseif ($pregunta['calificacion'] === 1): ?>
                  <div class="alert alert-success">OK</div>
                <?php elseif ($pregunta['calificacion'] === 0): ?>
                  <div class="alert alert-danger">KO</div>
                <?php else: ?>
                  <div class="alert alert-warning">NO EVALUADO</div>
                <?php endif; ?>

              </li>

            <?php ++$i;
            endforeach; ?>
          </ol>

          <div class="col text-center">
            <input type="submit" class="btn btn-primary" value="Enviar" />
          </div>

        </form>


      </div>
    </div>
  </div>

</body>

</html>