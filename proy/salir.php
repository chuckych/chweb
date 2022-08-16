<?php
$_SESSION["secure_auth_ch"] = false;
?>
<script>
    // redirect to index.php
    sessionStorage.setItem(
        location.pathname.substring(1) + "proy_page",
        "inicio"
    );
    window.location.href = "../logout.php";
</script>