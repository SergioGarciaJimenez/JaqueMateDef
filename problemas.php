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
  // Preparamos la consulta para sacar datos del usuario (pieza y admin)
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
  // Valor por defecto en caso de error o que no haya iniciada sesión
  $piezaFavorita = "Moderno";
}

// Consulta para seleccionar un problema de ajedrez al azar, con su posición inicial y solución

$sql = "SELECT fen, solucion FROM problemas ORDER BY RAND() LIMIT 1";
$resultado = $conn->query($sql);

if ($resultado->num_rows > 0) {
  $problema = $resultado->fetch_assoc();
  $fen = $problema['fen'];
  $solucion = $problema['solucion'];
} else {
  // Valor por defecto en caso de que no se encuentren problemas
  $fen = '8/8/8/8/2R5/3k3B/8/2B4K w - - 0 1';
  $solucion = '1.Bf1# *';
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Problemas</title>
  <link rel="icon" href="img/iconos/favIcon.ico?v=1" type="image/x-icon">
  <!-- Fuentes de Google que utiliza la web, importo los APIs -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Besley:ital,wght@0,400..900;1,400..900&family=Kdam+Thmor+Pro&display=swap" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Brygada+1918:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="css/style.css" />
  <!-- Importo la bibloteca de chessboard.js para dibujar tablero y piezas, además de JQuery -->
  <link rel="stylesheet" href="css/chessboard-1.0.0.min.css" />
  <script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
  <script src="js/chessboard-1.0.0.min.js"></script>
  <!-- La biblioteca chess para las reglas y movimientos-->
  <script src="js/chess.js"></script>
  <!-- Js para el inicio y cierre de sesión -->
  <script src="js/sesion.js"></script>
  <script>
    // Pasamos a js el estado del usuario (activo o no)
    var usuarioActivo = <?php echo json_encode($usuarioActivo); ?>;
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
      <div class="columna" id="columnaIzq">
        <div class="filaSeleccion">
          <a href="problemas.php" class="filaSeleccion">
            <img src="img/iconos/problemas.png" alt="Problemas" class="icono" />
            <p>Problemas</p>
          </a>

          <a href="aperturas.php" class="filaSeleccion">
            <img src="img/iconos/tablero2.png" alt="Aperturas" class="icono" />
            <p>Aperturas</p>
          </a>

          <a href="bot.php" class="filaSeleccion">
            <img src="img/iconos/torre.png" alt="Bot" class="icono" />
            <p>VS Bot</p>
          </a>
        </div>
      </div>
      <div class="centro">
        <!-- Ventana modal para avisos -->
        <div id="ventanaModal" class="ventanaModal">
          <div id="contenidoProblemas">
            <p id="textoModal"></p>
            <div class="botonesFila">
              <input id="siguienteProblema" class="botonPerfil" type="submit" value="Siguiente">
              <input id="reintentar" class="botonPerfil" type="submit" value="Reintentar">
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
        <!-- Paso el FEN y la solución del problema a JavaScript -->
        <script>
          var fen = '<?php echo $fen; ?>';
          var solucion = '<?php echo $solucion; ?>';
        </script>
        <!-- Incluyo la parte del código de JS-->
        <script src="js/problemas.js"></script>
        <!-- Script de ventana modal -->
        <script src="js/modal.js"></script>
      </div>
      <div class="columna" id="columnaDec">
        <h3>Dato curioso</h3>
        <p class="dato">
          <?php
          include_once('conexion.php');
          // Consulta para seleccionar un dato curioso al azar 
          $sql = "SELECT descripcion FROM Datos ORDER BY RAND() LIMIT 1";
          $resultado = $conn->query($sql); // Verificar si hay resultados 
          if ($resultado->num_rows > 0) { // Mostrar el dato curioso 
            while ($fila = $resultado->fetch_assoc()) {
              echo ($fila["descripcion"]);
            }
          } else {
            echo "No se encontraron datos curiosos.";
          } // Cerrar la conexión
          $conn->close(); ?>
        </p>
      </div>
    </div>
  </main>
  <!-- Pie de página -->
  <footer>
    <p class="copy">Jaque Mate 2024 &#169; | Sergio García Jiménez</p>
  </footer>
</body>

</html>
