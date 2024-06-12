<?php
include_once ('conexion.php');

// Inicializamos el usuario activo a false por defecto
$usuarioActivo = false;

// Inicializo una variable para guardar el mensaje de registro resultante
$msg = "";

// Compruebo que he recibido todos los datos del formulario de registro
if (isset($_POST['userMail']) && isset($_POST['user']) && isset($_POST['pwd']) && isset($_POST['pwd2'])) {
  // Recibo y filtro los datos del formulario
  $email = htmlspecialchars($_POST['userMail']);
  $user = htmlspecialchars($_POST['user']);
  $pwd = htmlspecialchars($_POST['pwd']);
  $pwd2 = htmlspecialchars($_POST['pwd2']);
  // Obtengo el día de hoy
  $fechaActual = date('Y-m-d H:i:s');

  // Verifico que las contraseñas coincidan
  if ($pwd !== $pwd2) {
    $msg = "Las contraseñas no coinciden.";
  } else {
    // Encripto la contraseña
    $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

    // Verificar si el nombre de usuario ya está en uso
    $sql = "SELECT * FROM Usuario WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($sql);
    // Error genérico en caso de fallo de la conexión o la BBDD
    if (!$stmt) {
      die("Ha habido un error durante el registro, inténtalo más tarde.");
    }
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    // Si está registrado, lo muestro
    if ($result->num_rows > 0) {
      $msg = "El nombre de usuario ya está en uso, por favor elige otro.";
    } else {
      // Verificar si el correo electrónico ya está registrado
      $sql = "SELECT * FROM Usuario WHERE correoElectronico = ?";
      $stmt = $conn->prepare($sql);
      // Error genérico en caso de fallo de la conexión o la BBDD
      if (!$stmt) {
        die("Ha habido un error durante el registro, inténtalo más tarde.");
      }
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();
      if ($result->num_rows > 0) {
        $msg = "El correo electrónico indicado ya está registrado.";
      } else {
        // Inserto en la base de datos si todo es correcto
        $sql = "INSERT INTO Usuario (nombreUsuario, correoElectronico, password, fechaRegistro) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // Error genérico en caso de fallo de la conexión o la BBDD
        if (!$stmt) {
          die("Ha habido un error durante el registro, inténtalo más tarde.");
        }
        $stmt->bind_param("ssss", $user, $email, $hashedPwd, $fechaActual);
        if ($stmt->execute()) {
          $msg = "Usuario registrado correctamente. Ahora puedes iniciar sesión.";
          $redirect = "index.php";
        } else {
          // Error genérico en caso de fallo de la conexión o la BBDD
          $msg = "Ha habido un error durante el registro, inténtalo más tarde.";
          $redirect = "registro.php";
        }
      }
    }
    // Cierro la conexión
    $stmt->close();
    $conn->close();
  }
} else {
  // En caso de acceder a esta página directamente, o con algún campo vacío
  $msg = "Rellena el formulario de registro correctamente.";
}
// Vacío todos los datos
$_POST = [];

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
  <link
    href="https://fonts.googleapis.com/css2?family=Besley:ital,wght@0,400..900;1,400..900&family=Kdam+Thmor+Pro&display=swap"
    rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Brygada+1918:ital,wght@0,400..700;1,400..700&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
  <!-- Script de ventana modal -->
  <script src="js/modal.js"></script>
  <!-- Script para validar registro -->
  <script src="js/registroValido.js"></script>
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
      <br>
      <p id="mensajeRegistro"><?php echo $msg ?></p>
    </div>
  </div>
  <script>
    setTimeout(function () {
      window.location.href = "<?php echo isset($redirect) ? $redirect : 'registro.php'; ?>";
    }, 2500); // Redirecciono despues de 2,5 segs a index o registro según proceda
  </script>
</main>
<!-- Pie de página -->
<footer>
  <p class="copy">Jaque Mate 2024 &#169; | Sergio García Jiménez</p>
</footer>
</body>

</html>