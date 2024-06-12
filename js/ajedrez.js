// Iniciamos un nuevo juego
var juegoAjedrez = new Chess();

// Configuración del tablero
var config = {
  pieceTheme: "img/chesspieces/" + piezaFavorita + "/{piece}.png", // Aquí añado el estilo de las piezas correspondiente
  position: "start", // Posición de inicio
  draggable: true, // Hace que las piezas se puedan mover

  // Función para mover piezas
  onDragStart: function (source, piece, position, orientation) {
    // Solo permite arrastrar las piezas si es el turno del jugador correspondiente y el juego no ha terminado
    if (
      juegoAjedrez.game_over() === true ||
      (juegoAjedrez.turn() === "w" && piece.search(/^b/) !== -1) ||
      (juegoAjedrez.turn() === "b" && piece.search(/^w/) !== -1)
    ) {
      return false;
    }

    // Obtener lista de movimientos posibles
    var moves = juegoAjedrez.moves({
      square: source,
      verbose: true,
    });

    // Resaltar las casillas de destino posibles para una pieza al empezar a arrastrarla
    for (var i = 0; i < moves.length; i++) {
      // Determinar si el movimiento es un enroque
      var enroque =
        moves[i].flags.includes("k") || moves[i].flags.includes("q");
      resaltar(moves[i].to, moves[i].flags.includes("c"), enroque); // Resalta la casilla en verde para enrocar
    }
  },

  onDrop: function (source, target) {
    // Cuando se acaba el movimiento dejamos de resaltar casillas
    borrarResaltado();

    // Inicializamos las variables necesarias
    
    // Variable para guardar el movimiento
    var movimiento = null;

    // Obtener la pieza en la casilla de origen
    var piezaOrigen = juegoAjedrez.get(source);

    // Ventana modal para la promoción de pieza
    var modal = document.getElementById("modalPromocion");

    // Marcador para saber si estamos esperando la promoción
    var esperandoPromocion = false;

    // Compruebo todas las condiciones para que un peón esté en posición de promocionar, según el color, el origen y el destino
    if (
      piezaOrigen.type === "p" &&
      ((piezaOrigen.color === "b" && source[1] === "2" && target[1] === "1") ||
        (piezaOrigen.color === "w" && source[1] === "7" && target[1] === "8"))
    ) {
      esperandoPromocion = true;
      // Si el peón llega al extremo del tablero, muestra la ventana modal para la promoción de pieza
      modal.style.display = "block";
      // Reproduzco el sonido de movimiento
      document.getElementById("mover").play();
      // Aplica la selección del jugador cuando elige que pieza promocionar
      var btnsPromocion = document.querySelectorAll(".opcion-promocion");
      btnsPromocion.forEach(function (btn) {
        btn.addEventListener("click", function () {
          var piezaPromocion = this.getAttribute("pieza");
          movimiento = juegoAjedrez.move({
            from: source,
            to: target,
            promotion: piezaPromocion,
          });

          // Oculta la ventana modal
          modal.style.display = "none";

          // Actualizamos el tablero y colocamos la piezaPromocion elegida por el jugador
          board.position(juegoAjedrez.fen());

          // Compruebo si se acaba el juego tras el movimiento
          comprobarFinJuego();
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
    }

    // Si el movimiento realizado no es legal, vuelvo a colocar la pieza en su posición original y suena un error
    if (movimiento === null) {
      // Si estamos esperando la promoción, lo indicamos para que no se reproduzca el sonido de error sin motivo
      if (!esperandoPromocion) {
        document.getElementById("error").play();
        return "snapback";
      }
    } else {
      // Compruebo si se acaba el juego tras el movimiento
      comprobarFinJuego();
      // Reproduzco el sonido según lo que provoque el movimiento
      // Sonido por jaque
      if (juegoAjedrez.in_check()) {
        document.getElementById("jaque").play();
      } // Sonido de captura
      else if (movimiento.captured) {
        document.getElementById("comer").play();
      } // Sonido de movimiento normal
      else {
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

// Iniciamos el tablero con la configuración completa
var board = Chessboard("tablero", config);

// Funciones adicionales

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
  // Dejo un 90% del contenedor de ancho para que quede algo de margen
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


// Función para quitar los estilos de las casillas resaltadas cuando sea necesario (al soltar la pieza o acabar el movimiento)
function borrarResaltado() {
  $("#tablero .square-55d63").css("box-shadow", "");
}

// Función para cambiar el color de las casillas para resaltarlas antes de mover
function resaltar(square, capturar, enroque) {
  var $square = $("#tablero .square-" + square);
  // Fondo resaltado de amarillo, rojo o verde según el movimiento (mover, capturar, enrocar)
  var colorResaltar = capturar ? "red" : enroque ? "green" : "yellow";
  $square.css("box-shadow", "inset 0 0 25px " + colorResaltar);
}

// Cuando quitamos el ratón deja de resaltar las casillas
function onMouseoutSquare(square, piece) {
  borrarResaltado();
}

// Función para actualizar con la nueva posición tras mover la pieza
function onSnapEnd() {
  board.position(juegoAjedrez.fen());
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
  funcionAbrirModal();
});

