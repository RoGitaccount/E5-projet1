<?php

session_start();
if(!isset($_SESSION["user"])){
    header("Location: index.php");
    exit;
}

//supprime une variable
unset($_SESSION["user"]);

header("location: index.php");

?>