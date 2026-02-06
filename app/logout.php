<?php 
    setcookie("jwtAccess", "", 1);
    setcookie("jwtRefresh", "", 1);
    header("location: form.php");
    exit();
?>
