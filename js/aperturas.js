$(document).ready(function () {
  // Inicializo las variables que se van a utilizar
  var colorJugador;
  var indiceMovimiento;
  var movimientos;
  var pgn;

  // La variable que guarda el id de usuario
  var idUsuario = $('script[src="js/aperturas.js"]').attr('idUsuario');

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

    // Verificamos si board está definido y tiene el método resize
    if (board && typeof board.resize === 'function') {
      // Redimensionamos el tablero acorde a los nuevos tamaños
      board.resize();
    } else {
      // Linea de debug
      console.error('El tablero no está definido o no tiene un método resize.');
    }
  }

  // Hacemos el tablero dinámicamente responsive ancho y alto según se actualiza la ventana
  $(window).resize(function () {
    adjustBoardSize();
  });

  // Función para mostrar los mensajes en la ventana modal para aperturas
  function mostrarMensajeModal(mensaje) {
    $("#textoModal").text(mensaje);
    $("#ventanaModal").show();
  }

  $(".cerrar").on("click", function () {
    $("#ventanaModal").hide();
  });

  // Función para cargar la partida de la apertura seleccionada
  function importarPGN(pgn) {
    if (juegoAjedrez.load_pgn(pgn)) {
      movimientos = juegoAjedrez.history({ verbose: true });
      // Empezamos por la posición 0 de la lista de movimientos importada
      indiceMovimiento = 0;
      // Colocamos el tablero en la posición inicial de la partida
      juegoAjedrez.reset();
      // Restauramos la posición inicial del tablero
      board.position("start");
      // Muestro la ventana modal para seleccionar el color
      $("#modalColor").show();
      // Mostramos el diálogo de la torre entrenadora al cargar la apertura
      document.getElementById("conversacion").style.display = "block";
    } else {
      mostrarMensajeModal("El archivo PGN no es válido.");
    }
  }

  // Función para confirmar el borrado
  function confirmarBorrar(mensaje, callback) {
    mostrarMensajeModal(mensaje);
    $("#contenidoModal").append('<div class="botonesFila"><button id="confirmar" class="botonPerfil">Confirmar</button><button id="cancelar" class="botonPerfil">Cancelar</button><div>');

    // Si confirmamos el callback es true, borrarmos la apertura y cerramos la modal
    $("#confirmar").on("click", function () {
      callback(true);
      $("#ventanaModal").hide();
      $("#confirmar, #cancelar").remove();
    });
    // Si cancelamos se cierra la ventana
    $("#cancelar").on("click", function () {
      callback(false);
      $("#ventanaModal").hide();
      $("#confirmar, #cancelar").remove();
    });
  }

  // Manejar el borrado de una apertura
  $('#aperturaBorrar').on('click', function () {
    var aperturaSeleccionada = $('.aperturaPGN.seleccionada');
    // Si no hay apertura seleccionada, mostramos el mensaje de error
    if (aperturaSeleccionada.length === 0) {
      mostrarMensajeModal('Por favor selecciona una apertura para borrar.');
      return;
    }
    // Variable que guarda el nombre de la apertura seleccionada
    var nombreApertura = aperturaSeleccionada.text();

    confirmarBorrar('¿Estás seguro de que deseas borrar esta apertura?', function (confirmado) {
      if (confirmado) {
        // Mostramos el mensaje correspondiente y actualizamos tras dos segundos
        $.post('aperturas.php', { nombreApertura: nombreApertura, idUsuario: idUsuario }, function (response) {
          mostrarMensajeModal('Apertura borrada con éxito.');
          setTimeout(function () { location.reload() }, 1500);
        }).fail(function () {
          // Mensaje genérico de eror
          mostrarMensajeModal('Error al borrar la apertura, inténtalo más tarde.');
        });
      }
    });
  });

  // Mostrar la lista de aperturas
  $("#misAperturas").click(function () {
    // Mostrar la lista de aperturas
    $("#listaAperturas").show();
    // Limpiar contenido previo
    $("#listaAperturas").empty();

    if (aperturas.length > 0) {
      // Agregamos cada apertura a la lista para mostrarla
      aperturas.forEach(function (apertura) {
        $("#listaAperturas").append(
          '<p class="aperturaPGN" data-pgn="' +
          apertura.PGN +
          '">' +
          apertura.nombreApertura +
          "</p>"
        );
      });
    } else {
      // Mensaje en caso de que el usuario no tenga aperturas
      $("#listaAperturas").append("Aún no tienes ninguna apertura guardada");
    }
  });

  // Función para cargar el PGN de la apertura seleccionada
  $(document).on("click", ".aperturaPGN", function () {
    pgn = $(this).data("pgn");
    importarPGN(pgn);
    // Remarcamos la apertura seleccionada
    $(".aperturaPGN").removeClass("seleccionada");
    $(this).addClass("seleccionada");
  });

  // Función para avanzar un movimiento del ordenador cuando sea su turno
  function avanzarMovimiento() {
    if (indiceMovimiento < movimientos.length) {
      var movimiento = movimientos[indiceMovimiento];
      // Compruebo que es el movimiento del ordenador
      if (
        (colorJugador === "white" && movimiento.color === "b") ||
        (colorJugador === "black" && movimiento.color === "w")
      ) {
        juegoAjedrez.move(movimiento);
        // Avanzo un movimiento en el indice
        indiceMovimiento++;
        setTimeout(function () {
          board.position(juegoAjedrez.fen());
          // Reproduzco el sonido correspondiente a la acción que ha sucedido
          if (juegoAjedrez.in_check()) {
            document.getElementById("jaque").play();
          } // Sonido de captura
          else if (movimiento.captured) {
            document.getElementById("comer").play();
          } // Sonido de movimiento normal
          else {
            document.getElementById("mover").play();
          }
          // Si es el último movimiento de la lista, felicitamos al jugador
          if (indiceMovimiento >= movimientos.length) {
            document.getElementById("textoDialogo").innerHTML =
              "¡Muy bien! Este era el último movimiento";
          } else {
            avanzarMovimiento();
          }
        }, 500); // Esperamos 0.5 segundos para hacer el movimiento del ordenador
      }
    }
  }

  // Funciones para avanzar o retroceder movimientos

  // Función para cargar el movimiento anterior
  $("#atras").on("click", function () {
    if (indiceMovimiento > 0) {
      indiceMovimiento--;
      juegoAjedrez.undo();
      document.getElementById("mover").play();
      board.position(juegoAjedrez.fen());
      // Actualizamos el diálogo de la torre entrenadora
      document.getElementById("textoDialogo").innerHTML =
        "¿Siguiente movimiento?";
    } else {
      // Si no hay más movimientos que atrasar suena error y no hacemos nada
      document.getElementById("error").play();
    }
  });

  // Función para rehacer el último movimiento deshecho/avanzar un movimiento
  $("#adelante").on("click", function () {
    if (indiceMovimiento < movimientos.length) {
      juegoAjedrez.move(movimientos[indiceMovimiento]);
      indiceMovimiento++;
      document.getElementById("mover").play();
      board.position(juegoAjedrez.fen());
      // Actualizamos el diálogo de la torre entrenadora
      document.getElementById("textoDialogo").innerHTML =
        "¿Siguiente movimiento?";
    } else {
      // Si no hay más movimientos que avanzar suena error y no hacemos nada
      document.getElementById("error").play();
    }
  });

  // Ir al primer movimiento
  $("#principio").on("click", function () {
    juegoAjedrez.reset();
    indiceMovimiento = 0;
    board.position(juegoAjedrez.fen());
    // Actualizamos el diálogo de la torre entrenadora
    document.getElementById("textoDialogo").innerHTML =
      "¿Siguiente movimiento?";
    // Si es el turno del ordenador, avanzamos su movimiento
    if (
      (colorJugador === "white" && juegoAjedrez.turn() === "b") ||
      (colorJugador === "black" && juegoAjedrez.turn() === "w")
    ) {
      setTimeout(avanzarMovimiento, 500);
    }
  });

  // Ir al último movimiento
  $("#final").on("click", function () {
    while (indiceMovimiento < movimientos.length) {
      juegoAjedrez.move(movimientos[indiceMovimiento]);
      indiceMovimiento++;
    }
    board.position(juegoAjedrez.fen());
  });

  // Función para verificar si el movimiento del jugador es el esperado
  function validarMovimientoJugador(source, target) {
    // No permitir más movimientos si hemos llegado al final de la apertura
    if (indiceMovimiento >= movimientos.length) {
      document.getElementById("error").play();
      document.getElementById("textoDialogo").innerHTML =
        "¡Ya no hay más movimientos! Empieza de nuevo o añade alguna apertura nueva.";
      return;
    }
    var movimiento = movimientos[indiceMovimiento];
    var movimientoValido = juegoAjedrez.move({
      from: source,
      to: target,
      promotion: "q", // Suponemos promoción a reina
    });

    if (movimientoValido) {
      // Verificamos si el movimiento realizado es el esperado
      if (movimiento.from === source && movimiento.to === target) {
        indiceMovimiento++;
        // Reproduzco el sonido correspondiente a la acción que ha sucedido
        if (juegoAjedrez.in_check()) {
          document.getElementById("jaque").play();
        } // Sonido de captura
        else if (movimiento.captured) {
          document.getElementById("comer").play();
        } // Sonido de movimiento normal
        else {
          document.getElementById("mover").play();
        }
        document.getElementById("textoDialogo").innerHTML = "¡Correcto!";
        setTimeout(function () {
          // Si es el último movimiento felicitamos al jugador
          if (indiceMovimiento >= movimientos.length - 1) {
            document.getElementById("textoDialogo").innerHTML =
              "¡Muy bien! Este era el último movimiento";
          } else {
            // Si no, preguntamos por el siguiente
            document.getElementById("textoDialogo").innerHTML =
              "¿Siguiente movimiento?";
          }
          board.position(juegoAjedrez.fen());
          setTimeout(avanzarMovimiento, 250); // Comenzar el movimiento del ordenador después de 0.25 segundos
        }, 1000); // Mostrar el mensaje "¡Correcto!" durante 1 segundo
      } else {
        // Deshacemos el movimiento si no es el esperado
        juegoAjedrez.undo();
        document.getElementById("error").play();
        document.getElementById("textoDialogo").innerHTML =
          "¡Movimiento incorrecto! Inténtalo de nuevo";
      }
    } else {
      // Si el movimiento no es legal, no es necesario deshacer nada, solo reproducir el sonido de error
      document.getElementById("error").play();
    }
  }

  // Configuración del tablero
  var config = {
    pieceTheme: "img/chesspieces/" + piezaFavorita + "/{piece}.png", // Aplicamos el estilo de piezas correspondiente
    position: "start", // Posición
    draggable: true, // Hace que las piezas puedan moverse

    // Función para comprobar el movimiento en caso de que sea el turno del jugador
    onDrop: function (source, target) {
      if (
        (colorJugador === "white" && juegoAjedrez.turn() === "w") ||
        (colorJugador === "black" && juegoAjedrez.turn() === "b")
      ) {
        validarMovimientoJugador(source, target);
      } else {
        return "snapback";
      }
    },
    // Actualizamos el tablero tras el movimiento
    onSnapEnd: function () {
      board.position(juegoAjedrez.fen());
    },
  };

  var board = ChessBoard("tablero", config);

  // Ajustar el tamaño del tablero al cargar la página
  adjustBoardSize();

  // Función para seleccionar el color
  $(".botonApertura").click(function () {
    colorJugador = $(this).data("color");
    $("#modalColor").hide();
    // Ajustamos la orientación del tablero según el color del jugador
    config.orientation = colorJugador;
    board = ChessBoard("tablero", config);
    // Comenzamos la práctica de la partida
    avanzarMovimiento();
  });
});
