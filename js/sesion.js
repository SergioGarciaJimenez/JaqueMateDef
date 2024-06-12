// Función que controla el uso del botón de usuario

$(document).ready(function () {
  // Si no hay iniciada sesión, abre la ventana modal
  $("#inicioSesion").on("click", function () {
    funcionAbrirModal();
  });

  // Y si hay iniciada sesión, abre el perfil de usuario
  $("#usuarioPerfil").on("click", function () {
    window.location.href = "usuario.php";
  });

  // Si es admin, tiene acceso a administracion
  $("#admin").on("click", function(){
    window.location.href = "admin.php";
  })

  // Script para cerrar sesión
  $("#cerrarSesion").on("click", function () {
    window.location.href = "cerrarSesion.php";
  });
});
