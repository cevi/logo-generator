<?php
    session_start();

    if (isset($_SESSION['login'])) {
        unset($_SESSION['login']);
    }

    header('LOCATION:login.php');
    die();
