<?php
    $title = $_GET["title"];
    $text = $_GET["text"];

    include '../connection.php';

    $connection->query("INSERT INTO notes (user_id, title, content) VALUES ('0', '$title', '$text')");

    header("Location: ../../../index.php?site=notes");
    exit();
?>