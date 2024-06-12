// Divide la solución en dos partes, casilla partida y casilla destino
const partesSolucion = solucion.split(/-/); 

// El movimiento esperado es el que coincide con la solución
const movimientoEsperado = {
  from: partesSolucion[0], 
  to: partesSolucion[1],   
  promotion: partesSolucion[1].length > 4 ? partesSolucion[1][4] : undefined  // Detecta promoción si existe
};

// Comentarios de consola para depuración/comprobación de solución
console.log(movimientoEsperado);
console.log(fen);

// Iniciamos la posición en el FEN recibido
var juegoAjedrez = new Chess(fen);

// Configuración del tablero
var config = {
  pieceTheme: "img/chesspieces/" + piezaFavorita + "/{piece}.png", // Aquí añado el estilo de las piezas correspondiente
  position: fen, // Posición inicial del problema según el FEN
  draggable: true, // Hace que las piezas se puedan mover

  // Función para mover piezas
  onDragStart: function (source, piece, position, orientation) {
    // Solo permite arrastrar las piezas si es el turno del jugador y el juego no ha terminado
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

    // Resaltar las casillas posibles para una pieza al empezar a arrastrarla
    for (var i = 0; i < moves.length; i++) {
      // Determinar si el movimiento es un enroque
      var enroque =
        moves[i].flags.includes("k") || moves[i].flags.includes("q");
      resaltar(moves[i].to, moves[i].flags.includes("c"), enroque); // Resalta en verde el movimiento de enroque si es legal
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

    if (piezaOrigen.type === "p" && (target[1] === "1" || target[1] === "8")) {
      // Si el peón llega al extremo del tablero, muestra la ventana modal para la promoción de pieza
      modal.style.display = "block";

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

          // Actualizamos el tablero
          board.position(juegoAjedrez.fen());

          // Me aseguro de parar todos los sonidos para que no se solapen
          document.querySelectorAll("audio").forEach(function (audio) {
            audio.pause();
            audio.currentTime = 0;
          });
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

    // Si el movimiento no es legal, vuelve a colocar la pieza en su posición original y suena un error
        if (movimiento === null) {
          document.getElementById("error").play();
          return "snapback";
        } else {
          // Verifica si el movimiento es el esperado
          if (source === movimientoEsperado.from && target === movimientoEsperado.to && (!movimientoEsperado.promotion || movimiento.promotion === movimientoEsperado.promotion)) {
            document.getElementById("textoModal").innerHTML = "<span class = 'finJuego'>¡Movimiento correcto!</span>";
            funcionAbrirModal();
          } else {
            document.getElementById("textoModal").innerHTML = "<span class = 'finJuego'>¡Movimiento incorrecto! Inténtalo de nuevo</span>";
            funcionAbrirModal();
            // Ponemos sonido de error
            document.getElementById("error").play();
          }
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
    }
  },
  // Asigno cada acción a su función
  onMouseoutSquare: onMouseoutSquare,
  onSnapEnd: onSnapEnd,
};

// Iniciamos el tablero con la configuración
var board = Chessboard("tablero", config);

// Función para ajustar el tamaño del contenedor del tablero y del tablero según se reajusta la ventana al contenedor

function adjustBoardSize() {
  // Dejo un 90% del contenedor de ancho para que deje algo de margen
  var containerWidth = $('#contenedorTablero').width()*0.9;
  // El alto es un 80% de la ventana
  var containerHeight = $(window).height() * 0.8;

  // Determinar el tamaño del tablero basado en el tamaño del contenedor
  var size = Math.min(containerWidth, containerHeight);

  // Ajustamos el tamaño del tablero
  $('#tablero').css({
    width: size + 'px',
    height: size + 'px'
  });

  // Redimensionamos el tablero acorde a los nuevos tamaños
  board.resize();
}

// Hacer el tablero dinámicamente responsive ancho y alto según se actualiza la ventana
$(window).resize(function() {
  adjustBoardSize();
});

// Ajustar el tamaño del tablero al cargar la página
adjustBoardSize();


// Funciones para resaltar casillas al mover

// Función para quitar los estilos de las casillas resaltadas cuando sea necesario
function borrarResaltado() {
  $("#tablero .square-55d63").css("box-shadow", "");
}

// Función para cambiar el color de las casillas en ciertas situaciones
function resaltar(square, capturar, enroque) {
  var $square = $("#tablero .square-" + square);
  // Fondo resaltado de amarillo, rojo o verde según el movimiento (mover, capturar, enrocar)
  var colorResaltar = capturar ? "red" : enroque ? "green" : "yellow";
  $square.css("box-shadow", "inset 0 0 25px " + colorResaltar);
}

// Cuando quitamos el ratón deja de resaltar las casillas de esa pieza
function onMouseoutSquare(square, piece) {
  borrarResaltado();
}

// Actualiza con la nueva posición tras mover la pieza
function onSnapEnd() {
  board.position(juegoAjedrez.fen());
}

// Función para pasar al siguiente problema (realmente solo recarga la página)

$(document).ready(function() {
  $("#siguienteProblema").on("click", function() {
    // Ocultamos la ventana modal 
    $("#ventanaModal").hide();
    location.reload();
  });
});

// Función para reintentar el problema

$(document).ready(function() {
  $("#reintentar").on("click", function() {
    // Ocultamos la ventana modal 
    $("#ventanaModal").hide();

    // Deshacemos el movimiento incorrecto
    juegoAjedrez.undo();

    // Actualizamos el tablero con la posición del problema nuevamente
    board.position(juegoAjedrez.fen());
  });
});
