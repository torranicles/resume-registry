<?php
    session_start();
    unset($_SESSION['name']);
    unset($_SESSION['use_id']);
    header('Location: index.php')
?>