<?php


try {
   $des_categorias = consulta_pdo_corrector("SELECT descripcion FROM sqlrank_categorias WHERE activo=1 ORDER BY descripcion");
} catch (Exception $e) {
   echo "Se ha producido una excepción mysql";
}

?>

<div class="list-group list-group-flush">


   <?php if (isset($_SESSION['loguedUser'])): ?>

      <?php foreach ($des_categorias as $des_categoria) : ?>

         <a href="cuestionario.php?categoria=<?= $des_categoria['descripcion'] ?>"
            class="list-group-item list-group-item-action w-100 <?= strpos($_SERVER['REQUEST_URI'], $des_categoria['descripcion']) && strpos($_SERVER['REQUEST_URI'], 'cuestionario') ? 'active' : '' ?>">
            <?= $des_categoria['descripcion'] ?>
         </a>

      <?php endforeach; ?>

  


      <?php if ($_SESSION['rol'] == 'admin'): ?>


              <?php foreach ($des_categorias as $des_categoria) : ?>

         <a href="preguntas.php?categoria=<?= $des_categoria['descripcion'] ?>"
            class="list-group-item list-group-item-action w-100 <?= strpos($_SERVER['REQUEST_URI'], $des_categoria['descripcion']) && strpos($_SERVER['REQUEST_URI'], 'preguntas') ? 'active' : '' ?>">
            <?= $des_categoria['descripcion'] ?>
         </a>

      <?php endforeach; ?>

         <a href="categorias.php"
            class="list-group-item list-group-item-action w-100 <?= strpos($_SERVER['REQUEST_URI'], 'categorias') ? 'active' : '' ?>">
            Categorías
         </a>

         <a href="usuarios.php"
            class="list-group-item list-group-item-action w-100 <?= strpos($_SERVER['REQUEST_URI'], 'usuarios') ? 'active' : '' ?>">
            Usuarios
         </a>

      <?php endif; ?>

           <a href="notas.php"
         class="list-group-item list-group-item-action w-100 <?= strpos($_SERVER['REQUEST_URI'], 'notas') ? 'active' : '' ?>">
         Notas
      </a>

      <a href="logout.php"
         class="list-group-item list-group-item-action w-100 <?= strpos($_SERVER['REQUEST_URI'], 'logout') ? 'active' : '' ?>">
         <i class="fas fa-sign-out-alt"></i> Logout <?= $_SESSION['loguedUser'] ?>
      </a>


   <?php endif; ?>


</div>