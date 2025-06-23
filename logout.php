<?php
    require_once 'Configuration/session.php';
    session_destroy();
    header('Location: index.php');
    exit;
?>