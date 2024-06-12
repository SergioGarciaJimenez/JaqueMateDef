<?php

// Abrimos conexión a BBDD
include_once('conexion.php');

// Iniciamos la sesión
session_start();

// Inicializamos el usuario activo a false por defecto
$usuarioActivo = false;

// Verificamos si el usuario ha iniciado sesión
if (isset($_SESSION['user'])) {
  $userLog = $_SESSION['user'];
  $usuarioActivo = true;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Regístrate</title>
  <link rel="icon" href="img/iconos/favIcon.ico?v=1" type="image/x-icon">
  <!-- Fuentes de Google que utiliza la web, importo los APIs -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Besley:ital,wght@0,400..900;1,400..900&family=Kdam+Thmor+Pro&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Brygada+1918:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
  <!-- Importo Jquery -->
  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>

</head>
<!-- Cabecera -->
<header>
  <div class="logoTexto">
    <a href="index.php">
      <img class="icono" id="logo" src="img/iconos/logo.png" alt="Logo de Jaque Mate" />

      <h1 id="titulo">JaqueMate</h1>
    </a>
  </div>
  <div class="headerLat">
    <!-- Si el admin está activo mostramos su ventana de administración -->
    <?php if (isset($_SESSION['admin'])) : ?>
      <img id="admin" src="img/iconos/admin.png" alt="Administración" />
    <?php endif; ?>
    <img id="<?php echo $usuarioActivo ? 'usuarioPerfil' : 'inicioSesion'; ?>" src="img/iconos/usuario.png" alt="Iniciar sesión" />
    <!-- Si el usuario está activo se muestra el botón para cerrar sesión -->
    <?php if ($usuarioActivo) : ?>
      <img id="cerrarSesion" src="img/iconos/logout.png" alt="Cerrar sesión" onclick="cerrarSesion()" />
    <?php endif; ?>
  </div>
</header>
<main>
  <div class="cuerpoRegistro">
    <div class="cajaRegistro">
      <div class="registroLogo">
        <img id="jaqueCabecera" src="img/iconos/logo.png" alt="Logo de Jaque Mate" />
        <h1 id="tituloNegro">Registro</h1>
      </div>
      <!-- Formulario que recoge los datos para el registro -->
      <form id="formRegistro" method="post" action="registroRespuesta.php">
        Correo electrónico:<input type="email" name="userMail" class="input">
        <span id="email-status"></span>
        Usuario:<input type="text" name="user" class="input">
        <span id="user-status"></span>
        Contraseña:<input type="password" name="pwd" class="input">
        Repite la contraseña:<input type="password" name="pwd2" class="input">
        <input id="registrarse" class="botonPerfil" type="submit" value="Registrarse">
      </form>
    </div>
  </div>
         <!-- Ventana modal para inicio sesión -->
         <div id="modalSesion" class="ventanaModal">
          <div id="contenidoSesion">
            <span class="cerrar">&times;</span>
            <div class="registroLogo">
              <img id="jaqueCabecera" src="img/iconos/logo.png" alt="Logo de Jaque Mate" />
              <h2 id="tituloSesion">Iniciar Sesión</h2>
            </div>
            <form id="formInicioSesion" method="post" action="inicioSesion.php">
              <label for="usuario">Usuario:</label>
              <input type="text" id="usuario" name="userLog" required>
              <label for="password">Contraseña:</label>
              <input type="password" id="password" name="pwdLog" required>
              <input id="botonSesion" class="botonPerfil" type="submit" value="Iniciar Sesión">
            </form>
            <p>¿No tienes cuenta? <a href="registro.php" id="registrate">Regístrate</a></p>
          </div>
        </div>
</main>
<!-- Pie de página -->
<footer>
  <p class="copy">Jaque Mate 2024 &#169; | Sergio García Jiménez</p>
</footer>
  <!-- Script de ventana modal -->
  <script src="js/modal.js"></script>
</body>

</html>