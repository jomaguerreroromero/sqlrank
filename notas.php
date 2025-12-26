<?php

require 'cliente-pdo.php';

// Iniciar sesión
session_start();

if (!isset($_SESSION['loguedUser'])) {
    header("location: /index.php");
}


$sql = <<<SQL
select c.descripcion,count(*) as total, ifnull(consulta.bien,0) as bien, ifnull(consulta.bien,0)*10/count(*) as nota
from sqlrank_categorias c  
join sqlrank_preguntas p on c.id_categoria=p.id_categoria
left join (
select p.id_categoria,count(*) as bien
from sqlrank_respuestas r
join sqlrank_preguntas p on p.id_pregunta=r.id_pregunta
where r.id_usuario=? and r.calificacion=1
group by p.id_categoria) consulta on consulta.id_categoria=p.id_categoria
where c.activo=1
group by c.id_categoria
order by c.descripcion
SQL;


try {
    $notas = consulta_pdo_sql($sql, array($_SESSION['IDloguedUser']));
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
    <title>SQLRank 2.0 - Notas</title>
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

                <table class="table">
                    <tr>
                        <th>Descripción</th>
                        <th>Total</th>
                        <th>Bien</th>
                        <th>Nota</th>
                    </tr>
                    <?php foreach ($notas as $nota): ?>
                        <tr>
                            <td><?= $nota['descripcion'] ?></td>
                            <td><?= $nota['total'] ?></td>
                            <td><?= $nota['bien'] ?></td>
                            <td><?= $nota['nota'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>


            </div>
        </div>
    </div>

</body>

</html>