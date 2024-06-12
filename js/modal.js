// Funcionamiento de la ventana modal de promociones y avisos de partida

// Inicializamos las variables necesarias
var modal = document.getElementById("ventanaModal");
var cerrarVentana = document.querySelector(".cerrar");
var modalApertura = document.getElementById("modalApertura");

// Función para mostrar la ventana modal
function funcionAbrirModal() {
  modal.style.display = "block";
}

// Función para cerrar cualquier ventana modal
$(".cerrar").on("click", function () {
  $(".ventanaModal").css("display", "none");
});

// Función para abrir la ventana modal de iniciar sesion
$("#inicioSesion").on("click", function () {
  modalSesion.style.display = "block";
});

// Función para abrir la ventana modal de apertura
$(document).ready(function () {
  $("#nuevaApertura").on("click", function () {
    modalApertura.style.display = "block";
  });

  // Si no está iniciada la sesión del usuario, abrimos la ventana de sesión al pulsar mis aperturas, nueva apertura o borrar apertura
  $("#misAperturasSesion").on("click", function () {
    modalSesion.style.display = "block";
  });

  $("#aperturaSesion").on("click", function () {
    modalSesion.style.display = "block";
  });

  $("#borrarAperturaSesion").on("click", function () {
    modalSesion.style.display = "block";
  });
});
