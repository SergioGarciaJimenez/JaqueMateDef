<?php
// Iniciamos la sesión
session_start();

// La desasignamos y destruimos para cerrar sesión
session_unset();
session_destroy();

// Eliminamos también datos de formulario
$_POST = []

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cerrar sesión</title>
  <link rel="icon" href="img/iconos/favIcon.ico?v=1" type="image/x-icon">
  <!-- Fuentes de Google que utiliza la web, importo los APIs -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Besley:ital,wght@0,400..900;1,400..900&family=Kdam+Thmor+Pro&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Brygada+1918:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
</head>
<!-- Cabecera -->
<header>
  <div class="logoTexto">
    <a href="index.php">
      <img class="icono" id="logo" src="img/iconos/logo.png" alt="Logo de Jaque Mate" />
      <h1 id="titulo">JaqueMate</h1>
    </a>
  </div>
</header>
<main>
  <div class="cuerpoRegistro">
    <div class="cajaRegistro">
      <div class="registroLogo">
        <img id="jaqueCabecera" src="img/iconos/logo.png" alt="Logo de Jaque Mate" />
        <h1 id="tituloNegro">Log out</h1>
      </div>
      <br>
      <p id="mensajeRegistro">Sesión cerrada correctamente.</p>
    </div>
  </div>
  <script>
    setTimeout(function() {
      window.location.href = "<?php echo isset($redirect) ? $redirect : 'index.php'; ?>";
    }, 2500); // Redireccionamos a la página principal después de 2,5 segundos
  </script>
</main>
<!-- Pie de página -->
<footer>
  <p class="copy">Jaque Mate 2024 &#169; | Sergio García Jiménez</p>
</footer>
</body>

</html>
