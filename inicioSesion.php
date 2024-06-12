<?php
// Abrimos conexión a BBDD
include_once('conexion.php');

// Iniciamos la sesión
session_start();

// Comprobamos que hemos recibido usuario y contraseña
if (isset($_POST['userLog']) && isset($_POST['pwdLog'])) {
  $userLog = $_POST['userLog'];
  $pwdLog = $_POST['pwdLog'];

  // Preparamos la consulta primero para verificar si el usuario está baneado
  $sql = "SELECT * FROM baneos WHERE nombreUsuario = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $userLog);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    // Si es así, no iniciamos sesión y mostramos el mensaje
    $msg = "Este usuario está baneado.";
  } else {
    // Preparamos la consulta para verificar credenciales si el usuario no está baneado
    $sql = "SELECT idUsuario, password, admin FROM Usuario WHERE nombreUsuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $userLog);
    $stmt->execute();
    $result = $stmt->get_result();

    // Comprobamos si el usuario existe
    if ($result->num_rows > 0) {
      $row = $result->fetch_assoc();
      $hashedPwd = $row['password'];
      // Verificamos si la contraseña proporcionada coincide con la contraseña encriptada almacenada
      if (password_verify($pwdLog, $hashedPwd)) {
        if($row['admin'] == 1){
        // Activa el modo administrador si el usuario es admin
        $_SESSION['admin'] = true;
        }
        // Y asigna el id de usuario a la sesión
        $_SESSION['user'] = $row['idUsuario'];
        $msg = "Inicio de sesión correcto.";

      } else {
        $msg = "La contraseña introducida es incorrecta.";
      }
    } else {
      $msg = "El nombre de usuario indicado no exite.";
    }
  }
  // Cerramos la conexión y limpiamos los datos del formulario
  $_POST = [];
  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar sesión</title>
  <link rel="icon" href="img/iconos/favIcon.ico?v=1" type="image/x-icon">
  <!-- Fuentes de Google que utiliza la web, importo los APIs -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Besley:ital,wght@0,400..900;1,400..900&family=Kdam+Thmor+Pro&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Brygada+1918:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="css/style.css?<?php echo time(); ?>" />
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
        <h1 id="tituloNegro">Login</h1>
      </div>
      <br>
      <!-- Mostramos el mensaje correspondiente (éxito, fallo contraseña o no existe usuario) -->
      <p id="mensajeRegistro"><?php echo $msg ?></p>
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
