<?php

// Abrimos conexión a BBDD
include_once('conexion.php');

// Iniciamos la sesión
session_start();

// Inicializamos el usuario activo a false por defecto
$usuarioActivo = false;

// Inicializamos el mensaje de apertura en blanco
$mensajeApertura;

// Verificamos si el usuario ha iniciado sesión
if (isset($_SESSION['user'])) {
  $userLog = $_SESSION['user'];
  $usuarioActivo = true;

  // Obtenemos las aperturas del usuario
  $sql = "SELECT idApertura, nombreApertura, PGN FROM Aperturas WHERE idUsuario = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("i", $userLog);
  $stmt->execute();
  $result = $stmt->get_result();
  // Guardamos los resultados en el array de aperturas
  $aperturas = $result->fetch_all(MYSQLI_ASSOC);

  // Preparamos la consulta para sacar la pieza favorita del usuario
  $sql = "SELECT piezaFavorita FROM Usuario WHERE idUsuario = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $userLog);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    // Asignamos la pieza fav
    $piezaFavorita = $row['piezaFavorita'];
  } else {
    // Valor por defecto en caso de error o que no haya iniciada sesión
    $piezaFavorita = "Moderno";
  }
} else {
  // Valores por defecto en caso de error o que no haya iniciada sesión de usuario
  $aperturas = [];
  $piezaFavorita = "Moderno";
}

// Si hay datos en el formulario para guardar una nueva apertura
if (isset($_POST['titApertura']) && isset($_POST['pgnText'])) {
  // Obtenemos los datos del formulario
  $nombreApertura = $_POST['titApertura'];
  $PGN = $_POST['pgnText'];
  $idUsuario = $_SESSION['user'];

  // Primero verificamos si ya existe la apertura para ese usuario
  $sqlCheck = "SELECT COUNT(*) FROM Aperturas WHERE idUsuario = ? AND nombreApertura = ?";
  $stmtCheck = $conn->prepare($sqlCheck);

  if ($stmtCheck) {
    $stmtCheck->bind_param("is", $idUsuario, $nombreApertura);
    $stmtCheck->execute();
    $stmtCheck->bind_result($count);
    $stmtCheck->fetch();
    $stmtCheck->close();

    if ($count > 0) {
      // Si ya existe una apertura con ese nombre para el usuario, lanzamos un mensaje de error
      $mensajeApertura = "¡Ya tienes una apertura con este nombre! Elige otro.";
    } else {
      // Si no hay ninguna apertura con esa nomenclatura, la insertamos normalmente
      $sql = "INSERT INTO Aperturas (idUsuario, nombreApertura, PGN) VALUES (?, ?, ?)";
      $stmt = $conn->prepare($sql);

      // Vinculamos los parámetros y ejecutamos la consulta
      if ($stmt) {
        $stmt->bind_param("iss", $idUsuario, $nombreApertura, $PGN);
        $stmt->execute();
        $stmt->close();
        // Mensaje para la ventana modal
        $mensajeApertura = "Apertura guardada con éxito.";
      } else {
        // En caso de error
        $mensajeApertura = "Error al guardar la apertura, inténtalo más tarde.";
      }
    }
  } else {
    // En caso de error al preparar la consulta de verificación
    $mensajeApertura = "Error al verificar la apertura, inténtalo más tarde.";
  }
  // Cerramos la conexión
  $conn->close();
}

// Si hay una solicitud para borrar una apertura
if (isset($_POST['nombreApertura']) && isset($_POST['idUsuario'])) {
  $idUsuario = $_SESSION['user'];
  // Obtener el nombre de la apertura y el ID de usuario
  $nombreApertura = $_POST['nombreApertura'];
  $idUsuario = $_SESSION['user'];

  // Preparamos la consulta SQL para borrar la apertura
  $sql = "DELETE FROM Aperturas WHERE nombreApertura = ? AND idUsuario = ?";
  $stmt = $conn->prepare($sql);

  // Vinculamos los parámetros y ejecutamos la consulta
  if ($stmt) {
    $stmt->bind_param("si", $nombreApertura, $idUsuario);
    $stmt->execute();
    $stmt->close();
    // Mensaje para la ventana modal
    $mensajeApertura = "Apertura borrada con éxito.";

    // Enviar una respuesta de éxito al cliente
    echo "success";
    exit();
  } else {
    // En caso de error
    $mensajeApertura = "Error al borrar la apertura, inténtalo más tarde";
  }
  // Cerramos la conexión
  $conn->close();
}

// Vaciamos los datos del formulario
$_POST = [];

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Aperturas</title>
  <link rel="icon" href="img/iconos/favIcon.ico?v=1" type="image/x-icon" />
  <!-- Fuentes de Google que utiliza la web, importo los APIs -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Besley:ital,wght@0,400..900;1,400..900&family=Kdam+Thmor+Pro&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Brygada+1918:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
  <link rel="stylesheet" href="css/chessboard-1.0.0.min.css" />
  <!-- Importo la bibloteca de chessboard.js para dibujar tablero y piezas, además de JQuery -->
  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
  <script src="js/chessboard-1.0.0.min.js"></script>
  <!-- La biblioteca chess para las reglas y movimientos-->
  <script src="js/chess.js"></script>
  <!-- Js para el inicio y cierre de sesión -->
  <script src="js/sesion.js"></script>
  <script>
    // Pasamos a js el estado del usuario (activo o no)
    var usuarioActivo = <?php echo json_encode($usuarioActivo); ?>;
    // Pasamos las aperturas del usuario
    var aperturas = <?php echo json_encode($aperturas); ?>;
    // Paso a js el valor del estilo de pieza
    var piezaFavorita = "<?php echo $piezaFavorita; ?>";
  </script>
</head>

<body>
  <!-- Cabecera -->
  <header>
    <div class="logoTexto">
      <a href="index.php">
        <img class="icono" id="logo" src="img/iconos/logo.png" alt="Logo de Jaque Mate" />

        <h1 id="titulo">JaqueMate</h1>
      </a>
    </div>
    <!-- Controles de tablero -->
    <div class="botones">
      <img id="principio" class="botonJuego reversa" src="img/iconos/avanceDoble.png" alt="Ir al principio" />
      <img id="atras" class="botonJuego reversa" src="img/iconos/avance.png" alt="Jugada anterior" />
      <img id="adelante" class="botonJuego" src="img/iconos/avance.png" alt="Jugada siguiente" />
      <img id="final" class="botonJuego" src="img/iconos/avanceDoble.png" alt="Ir al final" />
      <img id="exportar" class="botonJuego" src="img/iconos/exportar.png" alt="Exportar PGN" />
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
  <!-- Efectos de sonido para movimiento y acciones de piezas -->
  <audio id="reinicio" src="audio/reiniciar.mp3"></audio>
  <audio id="mover" src="audio/mover.mp3"></audio>
  <audio id="error" src="audio/error.mp3"></audio>
  <audio id="comer" src="audio/comer.mp3"></audio>
  <audio id="enrocar" src="audio/enrocar.mp3"></audio>
  <audio id="jaque" src="audio/jaque.mp3"></audio>
  <audio id="fin" src="audio/fin.mp3"></audio>
  <main>
    <!-- Cuerpo -->
    <div class="cuerpo">
      <div class="contenedorCol">
        <div class="columna" id="columnaIzqSup">
          <div class="filaSeleccionPeq">
            <a href="problemas.php" class="filaSeleccionPeq">
              <img src="img/iconos/problemas.png" alt="Problemas" class="iconoPeq" />
              <p>Problemas</p>
            </a>
            <a href="aperturas.php" class="filaSeleccionPeq">
              <img src="img/iconos/tablero2.png" alt="Aperturas" class="iconoPeq" />
              <p>Aperturas</p>
            </a>
            <a href="bot.php" class="filaSeleccionPeq">
              <img src="img/iconos/torre.png" alt="Bot" class="iconoPeq" />
              <p>VS Bot</p>
            </a>
          </div>
        </div>
        <div class="columna" id="columnaIzqInf">
          <div id="listaAperturas" style="display:none">
            <?php if (is_array($aperturas) && count($aperturas) > 0) : ?>
              <?php foreach ($aperturas as $apertura) : ?>
                <p class="aperturaPGN <?php echo $apertura['idApertura'] === $aperturaSeleccionada ? 'seleccionada' : ''; ?>" data-pgn="<?php echo htmlspecialchars($apertura['PGN']); ?>">
                  <?php echo htmlspecialchars($apertura['nombreApertura']); ?>
                </p>
              <?php endforeach; ?>
            <?php else : ?>
              Aún no tienes ninguna apertura guardada
            <?php endif; ?>
          </div>
        </div>
      </div>
      <div class="centro">
        <!-- Ventana modal para importar PGN -->
        <div id="modalApertura" class="ventanaModal">
          <div id="contenidoApertura">
            <span class="cerrar">&times;</span>
            <h2 id="tituloSesion">Nueva Apertura</h2>
            <form id="formularioApertura" class="formularioApertura" method="post" action="aperturas.php">
              <label for="tituloApertura">Nombre:</label><br>
              <input type="text" id="tituloApertura" name="titApertura" required><br>
              <label for="pgn">PGN:</label><br>
              <textarea id="pgn" name="pgnText" rows="6" required></textarea><br>
              <input id="botonApertura" class="botonPerfil" type="submit" value="Guardar">
            </form>
          </div>
        </div>
        <!-- Ventana modal para avisos -->
        <div id="ventanaModal" class="ventanaModal">
          <div id="contenidoModal">
            <span class="cerrar">&times;</span>
            <p id="textoModal"></p>
          </div>
        </div>
        <!-- Ventana modal para seleccionar color a practicar en la apertura -->
        <div id="modalColor" class="ventanaModal">
          <div id="colorContenido">
            <span class="cerrar">&times;</span>
            <h3>Elige el color para practicar esta apertura:</h5><br>
              <div class="elegirColor">
                <button class="botonApertura" data-color="white">Blancas</button>
                <button class="botonApertura" data-color="black">Negras</button>
              </div>
          </div>
        </div>
        <!-- Ventana modal para promocionar pieza -->
        <div id="modalPromocion" class="ventanaModal">
          <div id="promocionContenido">
            <h5>Elige la pieza de promoción</h3>
              <div class="opciones">
                <button class="opcion-promocion" pieza="q">Reina</button>
                <button class="opcion-promocion" pieza="r">Torre</button>
                <button class="opcion-promocion" pieza="n">Caballo</button>
                <button class="opcion-promocion" pieza="b">Alfil</button>
              </div>
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
        <!-- El espacio para el tablero -->
        <div id="contenedorTablero">
          <div id="tablero"></div>
        </div>
        <!-- Script para aperturas -->
        <script src="js/aperturas.js" idUsuario="<?php echo $userLog; ?>"></script>
        <!-- Script para toda la partida de ajedrez -->
        <script src="js/ajedrez.js"></script>
        <!-- Script de ventana modal -->
        <script src="js/modal.js"></script>
      </div>
      <div class="columna" id="columnaDec2">
        <div class="aperturas">
          <div id="<?php echo $usuarioActivo ? 'misAperturas' : 'misAperturasSesion'; ?>" class="filaCol"><img class="iconoApertura" src="img/iconos/favLista.png" alt="Mis aperturas" />
            <h3 class="aperturasTit">Mis aperturas</h3>
          </div>
          <div id="<?php echo $usuarioActivo ? 'nuevaApertura' : 'aperturaSesion'; ?>" class="filaCol"><img class="iconoApertura" src="img/iconos/agregarFav.png" alt="Nueva apertura" />
            <h3 class="aperturasTit">Nueva apertura</h3>
          </div>
          <div id="<?php echo $usuarioActivo ? 'aperturaBorrar' : 'borrarAperturaSesion'; ?>" class="filaCol"><img class="iconoApertura" src="img/iconos/borrarFav.png" alt="Nueva apertura" />
            <h3 class="aperturasTit">Borrar apertura</h3>
          </div>
        </div>
        <div class="dialogoDiv">
          <div id="conversacion">
            <p id="textoDialogo">¿Siguiente movimiento?</p>
          </div>
          <img id="torre" src="img/iconos/torre.png" alt="Torre entrenadora" />
        </div>
      </div>
    </div>
    </div>
  </main>
  <!-- Pie de página -->
  <footer>
    <p class="copy">Jaque Mate 2024 &#169; | Sergio García Jiménez</p>
  </footer>
</body>
</html>