<?php
// Abrimos conexión a BBDD
include_once('conexion.php');

// Iniciamos la sesión
session_start();

// Inicializamos el usuario activo a false por defecto
$usuarioActivo = false;

// Y el mensaje de baneo
$msg = '';

// Verificamos si el usuario es administrador
if (isset($_SESSION['admin'])) {
  $userLog = $_SESSION['user'];
  $usuarioActivo = true;

  // Obtenemos número de usuarios registrados

  $sql = "SELECT COUNT(*) AS numeroUsuarios FROM usuario";
  $result = $conn->query($sql);
  $numeroUsuarios = $result->fetch_assoc()['numeroUsuarios'];

  // Obtenemos el usuario más antiguo (que no sea el propio admin)
  $sql = "SELECT nombreUsuario FROM usuario where nombreUsuario != 'admin' ORDER BY fechaRegistro ASC LIMIT 1";
  $result = $conn->query($sql);
  $usuarioAntiguo = $result->fetch_assoc()['nombreUsuario'];

  // Obtenemos el usuario más nuevo
  $sql = "SELECT nombreUsuario FROM usuario ORDER BY fechaRegistro DESC LIMIT 1";
  $result = $conn->query($sql);
  $usuarioNuevo = $result->fetch_assoc()['nombreUsuario'];

  // Obtener apertura más jugada
  $sql = "SELECT nombreApertura, COUNT(*) AS cantidad FROM aperturas GROUP BY nombreApertura ORDER BY cantidad DESC LIMIT 1";
  $result = $conn->query($sql);
  $aperturaJugada = $result->fetch_assoc()['nombreApertura'];

  // Si se ha enviado el formulario para banear usuario
  if (isset($_POST['banear'])) {
    $userBan = $_POST['userBan'];

    // Verificamos si el usuario ya está baneado
    $sql = "SELECT nombreUsuario FROM baneos WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userBan);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row) {
      // Si ya hay resultado es que el usuario ya está baneado
      $msg = "El usuario ya está baneado.";
    } else {
      // Si no está baneado, lo insertamos en la tabla de baneos
      $sql = "INSERT INTO baneos (nombreUsuario) VALUES (?)";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $userBan);
      $stmt->execute();
      $stmt->close();

      $msg = "Usuario baneado";

    }
  }

  // Si se ha enviado el formulario para desbanear usuario
  if (isset($_POST['desbanear'])) {
    $userDesban = $_POST['userBan'];

     // Verificamos si el usuario estaba baneado antes de desbanearlo
     $sql = "SELECT nombreUsuario FROM baneos WHERE nombreUsuario = ?";
     $stmt = $conn->prepare($sql);
     $stmt->bind_param("s", $userDesban);
     $stmt->execute();
     $result = $stmt->get_result();
     $row = $result->fetch_assoc();

    if ($row){
    // Preparamos la consulta para desbanear usuario
    $sql = "DELETE FROM Baneos WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userDesban);
    $stmt->execute();
    $stmt->close();

    $msg = "Usuario desbaneado";
    }
    else {
      $msg = "El usuario no está en la lista de baneados";
    }
  }
} else {
  // Si no es el usuario administrador y de alguna forma se accede a esta página, redirigimos a la página de inicio de sesión
  header("Location: inicioSesion.php");
  exit();
}

// Vaciamos los datos del formulario
$_POST = [];
// Cerramos la conexión
$conn->close();

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Administración</title>
  <link rel="icon" href="img/iconos/favIcon.ico?v=1" type="image/x-icon">
  <!-- Fuentes de Google que utiliza la web, importo los APIs -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Besley:ital,wght@0,400..900;1,400..900&family=Kdam+Thmor+Pro&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Brygada+1918:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo time(); ?>" />
  <!-- Importo JQuery -->
  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
  <!-- Js para el inicio y cierre de sesión -->
  <script src="js/sesion.js"></script>
  <!-- Script de ventana modal -->
  <script src="js/modal.js"></script>
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
        <h1 id="tituloNegro">Administración</h1>
      </div>
      <p id="numUsuarios" class="textoPerfil">Usuarios registrados: <?php echo $numeroUsuarios ?></p>
      <p id="userAnt" class="textoPerfil">Usuario más antiguo: <?php echo $usuarioAntiguo ?></p>
      <p id="userNuevo" class="textoPerfil">Usuario más nuevo: <?php echo $usuarioNuevo ?></p>
      <p id="aperturaPop" class="textoPerfil">Apertura más jugada: <?php echo $aperturaJugada ?></p>
      <br>
      <h2 id="subtitulo">Gestión de usuarios:</h2>
      <form id="formBaneo" method="post" action="admin.php">
        Introduce el usuario a gestionar:<input type="text" name="userBan" class="input">
        <span id="userBan"></span>
        <div id="filaBoton">
          <input id="baneo" class="botonPerfil" type="submit" name="banear" value="Banear usuario">
          <input id="desbaneo" class="botonPerfil" type="submit" name="desbanear" value="Desbanear usuario">
        </div>
      </form>
      <br>
      <?php echo $msg ?>
    </div>
  </div>
</main>
<!-- Pie de página -->
<footer>
  <p class="copy">Jaque Mate 2024 &#169; | Sergio García Jiménez</p>
</footer>
</body>

</html>
