$("#uc_mostrar").on("click", function(e) {
    var tipo = document.getElementById("clave");
    if (tipo.type == "password") {
        tipo.type = "text";
        $("#uc_mostrar").html('<i class="bi bi-eye-fill text-secondary"></i>')
    } else {
        tipo.type = "password";
        $("#uc_mostrar").html('<i class="bi bi-eye-slash-fill text-secondary"></i>')
    }
});
let selfthome  = ($('#selfHome').val());
sessionStorage.removeItem(selfthome+'/proy/proy_info');