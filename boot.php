<?php
    include "config.php";

    # Determine the server hostname
    $host = $_SERVER["SERVER_NAME"];

    # Determine the server protocol being used
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") {
        $proto = "https";
    } else $proto = "http";

    # Ensure that data is only sent as plaintext
    header("Content-Type: text/plain");

    if (isset($_GET["image"])) {
        $image = $_GET["image"];

        include "chain/image.php";
    } else {
        include "chain/menu.php";
    }
?>