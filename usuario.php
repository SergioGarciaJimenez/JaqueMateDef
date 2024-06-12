<?php
// Iniciamos la sesión
session_start();

// Abrimos conexión a BBDD
include_once('conexion.php');

// Inicializamos el usuario activo a false por defecto
$usuarioActivo = false;

// Verificamos si el usuario ha iniciado sesión
if (isset($_SESSION['user'])) {
  $userLog = $_SESSION['user'];
  $usuarioActivo = true;

  // Si se ha enviado el formulario para cambio de estilo de pieza
  if (isset($_POST['estiloPieza'])) {
    $nuevoEstilo = $_POST['estiloPieza'];
    // Preparamos la consulta para actualizar la pieza favorita
    $sql = "UPDATE Usuario SET piezaFavorita = ? WHERE idUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nuevoEstilo, $userLog);
    $stmt->execute();
    $stmt->close();
    // Redirigimos de vuelta a la página
    header("Location: usuario.php");
    exit(); // Detenemos el script después de redirigir
  }

  // Preparamos la consulta para la fecha de registro
  $sql = "SELECT fechaRegistro, piezaFavorita FROM Usuario WHERE idUsuario = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $userLog);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $fechaRegistro = $row['fechaRegistro'];
    $piezaFavorita = $row['piezaFavorita'];
  } else {
    $fechaRegistro = "No se encontró la fecha de registro.";
    $piezaFavorita = "Moderno"; // Valor por defecto en caso de no cargar correctamente
  }
} else {
  // Si no hay usuario con sesión iniciada y de alguna forma se accede a esta página, redirigimos a la página de inicio de sesión
  header("Location: inicioSesion.php");
  exit(); // Asegúrate de detener el script después de redirigir
}

// Cerramos la conexión
$conn->close();

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mi perfil</title>
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
        <h1 id="tituloNegro">Perfil</h1>
      </div>
      <form class="formUsuario" method="post" action="usuario.php">
        Estilo de piezas: <select name="estiloPieza" class="editInput">
          <option value="Clásico" <?php if ($piezaFavorita == 'Clásico') echo 'selected'; ?>>Clásico</option>
          <option value="Moderno" <?php if ($piezaFavorita == 'Moderno') echo 'selected'; ?>>Moderno</option>
          <option value="Retro" <?php if ($piezaFavorita == 'Retro') echo 'selected'; ?>>Retro</option>
          <option value="Kawai" <?php if ($piezaFavorita == 'Kawai') echo 'selected'; ?>>Kawai</option>
        </select>
        <div id="filaBoton">
          <input id="cambios" class="botonPerfil" type="submit" value="Guardar cambios">
        </div>
        <p class="textoPerfil">Fecha de registro: <?php echo date('d-m-Y', strtotime($fechaRegistro)); ?></p>
      </form>
      <br>
    </div>
  </div>
</main>
<!-- Pie de página -->
<footer>
  <p class="copy">Jaque Mate 2024 &#169; | Sergio García Jiménez</p>
</footer>
</body>

</html>
