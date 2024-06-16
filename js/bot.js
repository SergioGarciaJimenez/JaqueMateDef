// Iniciamos nueva partida
var juegoAjedrez = new Chess();

// Función de la lógica de movimientos del bot
function realizarMovimientoBot() {
  // Comprueba que es el turno del ordenador (negras)
  if (juegoAjedrez.turn() === "b" && !juegoAjedrez.game_over()) {
    var movimientos = juegoAjedrez.moves(); // Obtenemos todos los movimientos legales
    var movimientoCaptura = movimientos.filter(function (move) {
      return move.includes("x"); // Comprobamos si el movimiento es una captura
    });

    if (movimientoCaptura.length > 0) {
      // Si hay uno o varios movimientos de captura, elige uno de ellos al azar
      var movimientoCapturaElegido =
        movimientoCaptura[Math.floor(Math.random() * movimientoCaptura.length)];
      juegoAjedrez.move(movimientoCapturaElegido);
      document.getElementById("comer").play(); // Sonido de captura
    } else {
      // Si no hay movimientos de captura disponibles, busca movimientos que pongan en jaque al rey oponente
      var movimientosJaque = movimientos.filter(function (move) {
        juegoAjedrez.move(move); // Intenta hacer el movimiento
        var jaque = juegoAjedrez.in_check(); // Comprueba si el movimiento pone en jaque al rey
        juegoAjedrez.undo(); // Deshace el movimiento
        return jaque;
      });

      if (movimientosJaque.length > 0) {
        var movimientosJaqueSeguros = movimientosJaque.filter(function (move) {
          juegoAjedrez.move(move); // Hace el movimiento
          var seguro = true;
          var movimientosOponente = juegoAjedrez.moves({ verbose: true });
          for (var i = 0; i < movimientosOponente.length; i++) {
            if (movimientosOponente[i].captured) {
              seguro = false;
              break;
            }
          }
          juegoAjedrez.undo();
          return seguro;
        });

        if (movimientosJaqueSeguros.length > 0) {
          // Si hay movimientos de jaque seguros, elige uno de ellos al azar
          var movimientoJaque =
            movimientosJaqueSeguros[
              Math.floor(Math.random() * movimientosJaqueSeguros.length)
            ];
          juegoAjedrez.move(movimientoJaque);
          document.getElementById("jaque").play(); // Reproduce sonido de jaque
        } else {
          realizarMovimientoAleatorio(movimientos);
        }
      } else {
        realizarMovimientoAleatorio(movimientos);
      }
    }
    board.position(juegoAjedrez.fen()); // Actualizamos el tablero
    comprobarFinJuego(); // Lógica para cada causística según lo que provoque el movimiento del bot
  }
}

// Función para realizar un movimiento aleatorio
function realizarMovimientoAleatorio(movimientos) {
  var movimientoAleatorio = movimientos[Math.floor(Math.random() * movimientos.length)];
  juegoAjedrez.move(movimientoAleatorio);
  document.getElementById("mover").play();
}

// Función para que el bot realice un movimiento después de unos momentos
function realizarMovimientoConRetraso() {
  setTimeout(realizarMovimientoBot, 1500); // El bot espera 1.5 segs para simular pensar
}

// Actualiza con la nueva posición tras mover la pieza
function onSnapEnd() {
  board.position(juegoAjedrez.fen());
}

// Configuración del tablero
var config = {
  pieceTheme: "img/chesspieces/" + piezaFavorita + "/{piece}.png", // Aquí añado el estilo de las piezas correspondiente
  position: "start", // Posición de inicio
  draggable: true, // Hace que las piezas se puedan mover

  // Función para mover piezas
  onDragStart: function (source, piece, position, orientation) {
    // Solo permite arrastrar las piezas si es el turno del jugador y el juego no ha terminado
    if (
      juegoAjedrez.game_over() === true ||
      (juegoAjedrez.turn() === "w" && piece.search(/^b/) !== -1) ||
      (juegoAjedrez.turn() === "b" && piece.search(/^w/) !== -1) ||
      piece.search(/^w/) === -1
    ) {
      // Si no es el turno de blancas, no permite mover
      return false;
    }

    // Obtener lista de movimientos posibles
    var moves = juegoAjedrez.moves({
      square: source,
      verbose: true,
    });
    // Resaltar las casillas posibles para una pieza al empezar a arrastrarla
    for (var i = 0; i < moves.length; i++) {
      // Determinar si el movimiento es un enroque
      var enroque =
        moves[i].flags.includes("k") || moves[i].flags.includes("q");
      resaltar(moves[i].to, moves[i].flags.includes("c"), enroque); // Marca el movimiento de enroque si es legal
    }
  },

  onDrop: function (source, target) {
    // Cuando se acaba el movimiento dejamos de resaltar casillas
    borrarResaltado();

    // Variable para guardar el movimiento
    var movimiento = null;

    // Obtener la pieza en la casilla de origen
    var piezaOrigen = juegoAjedrez.get(source);

    // Ventana modal para la promoción de pieza
    var modal = document.getElementById("modalPromocion");

    // Lógica en caso de que el movimiento sea de promoción

    // Si el peón llega al extremo del tablero, muestra la ventana modal para la promoción de pieza
    if (piezaOrigen.type === "p" && ((piezaOrigen.color === "b" && source[1] === "2" && target[1] === "1") || 
      (piezaOrigen.color === "w" && source[1] === "7" && target[1] === "8")
    )) {
      esperandoPromocion = true;
      // Si el peón llega al extremo del tablero, muestra la ventana modal para la promoción de pieza
      modal.style.display = "block";
      document.getElementById("mover").play();
      // Captura la selección del jugador cuando hace clic en un botón de selección de pieza
      var btnsPromocion = document.querySelectorAll(".opcion-promocion");
      btnsPromocion.forEach(function (btn) {
        btn.addEventListener("click", function () {
          var piezaPromocion = this.getAttribute("pieza");
          movimiento = juegoAjedrez.move({
            from: source,
            to: target,
            promotion: piezaPromocion,
          });

          /* En el caso de que el bot llegue al final del tablero, promociona una pieza al azar, ya que sus movimientos legales una vez llega al final
          tras mover un peón son elegir una pieza cualquiera, y por tanto lo hace al azar */

          // Ocultar la ventana modal
          modal.style.display = "none";

          // Actualizamos el tablero
          board.position(juegoAjedrez.fen());

          // Me aseguro de parar todos los sonidos para que no se solapen
          document.querySelectorAll("audio").forEach(function (audio) {
            audio.pause();
            audio.currentTime = 0;
          });

          // Después de que el jugador realice su movimiento, el bot realiza el suyo con algo de demora
          realizarMovimientoConRetraso();
        });
      });
    } else {
      // Realizar el movimiento normal si no hay promoción de pieza
      movimiento = juegoAjedrez.move({
        from: source,
        to: target,
      });

      // Me aseguro de parar todos los sonidos para que no se solapen
      document.querySelectorAll("audio").forEach(function (audio) {
        audio.pause();
        audio.currentTime = 0;
      });

      // Después de que el jugador realice su movimiento, el bot realiza el suyo con algo de demora
      realizarMovimientoConRetraso();
    }

    // Si el movimiento no es legal, vuelve a colocar la pieza en su posición original y suena un error
    if (movimiento === null) {
      // Si estamos esperando la promoción, lo indicamos para que no se reproduzca el sonido de error sin motivo
      if (!esperandoPromocion) {
        document.getElementById("error").play();
        return "snapback";
      }
    } else {
      // Lógica para cada causística según lo que provoque el movimiento del jugador
      comprobarFinJuego();
      
      // Reproduzco el sonido correspondiente al movimiento
      if (juegoAjedrez.in_check()) {
        // Sonido por jaque
        document.getElementById("jaque").play();
      } else if (movimiento.captured) {
        // Sonido de captura
        document.getElementById("comer").play();
      } else {
        // Movimiento normal
        document.getElementById("mover").play();
      }
      // Restablecemos el marcador de promoción
      esperandoPromocion = false;
    }
  },
  // Asigno cada acción a su función
  onMouseoutSquare: onMouseoutSquare,
  onSnapEnd: onSnapEnd,
};

// Iniciamos el tablero con la configuración
var board = Chessboard("tablero", config);

// Función para comprobar si el juego ha finalizado
function comprobarFinJuego(){
  if (juegoAjedrez.in_checkmate()) {
    // Si hay jaque mate, muestra un aviso de jaque mate y sonido fin de partida
    document.getElementById("fin").play();
    // Añado el texto correspondiente
    document.getElementById("textoModal").innerHTML =
      "<span class = 'finJuego'>¡Jaque Mate!</span>";
    // Y muestro la ventana
    funcionAbrirModal();
  } else if (juegoAjedrez.in_stalemate()) {
    // Si hay rey ahogado, muestro aviso de tablas y sonido fin de partida
    document.getElementById("fin").play();
    // Añado el texto correspondiente
    document.getElementById("textoModal").innerHTML =
      "<span class = 'finJuego'>¡Tablas por rey ahogado!</span>";
    // Y muestro la ventana
    funcionAbrirModal();
  } // Compruebo las situaciones de posibles empates por otras causas, como falta de material, repetición, etc
  else if (
    juegoAjedrez.in_draw() ||
    juegoAjedrez.in_threefold_repetition() ||
    juegoAjedrez.insufficient_material()
  ) {
    // Si el juego está empatado por alguna de las razones mencionadas, reproduzco el sonido de fin del juego
    document.getElementById("fin").play();
    // Añade el texto correspondiente para el tipo de tablas
    if (juegoAjedrez.in_threefold_repetition()) {
      document.getElementById("textoModal").innerHTML =
        "<span class = 'finJuego'>¡Tablas por repetición!</span>";
    } else if (juegoAjedrez.insufficient_material()) {
      document.getElementById("textoModal").innerHTML =
        "<span class = 'finJuego'>¡Tablas por insuficiencia de material!</span>";
    } else if (juegoAjedrez.in_draw()) {
      document.getElementById("textoModal").innerHTML =
        "<span class = 'finJuego'>¡Tablas por 50 movimientos sin captura ni avance de peón!</span>";
    }
    funcionAbrirModal();
  } 
}

// Función para ajustar el tamaño del contenedor del tablero y del tablero según se reajusta la ventana al contenedor
function adjustBoardSize() {
  // Dejo un 90% del contenedor de ancho para que deje algo de margen
  var containerWidth = $("#contenedorTablero").width() * 0.9;
  // El alto es un 80% de la ventana
  var containerHeight = $(window).height() * 0.8;

  // Determinar el tamaño del tablero basado en el tamaño del contenedor
  var size = Math.min(containerWidth, containerHeight);

  // Ajustamos el tamaño del tablero
  $("#tablero").css({
    width: size + "px",
    height: size + "px",
  });

  // Redimensionamos el tablero acorde a los nuevos tamaños
  board.resize();
}

// Hacer el tablero dinámicamente responsive ancho y alto según se actualiza la ventana
$(window).resize(function () {
  adjustBoardSize();
});

// Ajustar el tamaño del tablero al cargar la página
adjustBoardSize();

// Función para quitar los estilos de las casillas resaltadas cuando soltamos la pieza o terminamos de mover
function borrarResaltado() {
  $("#tablero .square-55d63").css("box-shadow", "");
}

// Función para cambiar el color de las casillas destino cuando vayamos a mover
function resaltar(square, capturar, enroque) {
  var $square = $("#tablero .square-" + square);
  // Fondo resaltado de amarillo, rojo o verde según el movimiento (mover, capturar, enrocar)
  var colorResaltar = capturar ? "red" : enroque ? "green" : "yellow";
  $square.css("box-shadow", "inset 0 0 25px " + colorResaltar);
}

// Cuando quitamos el ratón deja de resaltar las casillas disponibles para esa pieza
function onMouseoutSquare(square, piece) {
  borrarResaltado();
}

// Función para reiniciar el tablero
$("#reiniciar").on("click", function () {
  juegoAjedrez.reset();
  board.position("start");
  document.getElementById("reinicio").play();
});

// Función para exportar la partida en formato PGN
$("#exportar").on("click", function () {
  var pgn = juegoAjedrez.pgn();
  // Si no se han hecho movimientos indico que está vacío
  pgn != ""
    ? (document.getElementById("textoModal").innerHTML = pgn)
    : (document.getElementById("textoModal").innerHTML = "PGN vacío");
  document.getElementById("contenidoModal").style.fontSize = "1vw";
  funcionAbrirModal();
});
