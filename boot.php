<?php
    # Determine the server hostname
    $host = $_SERVER["SERVER_NAME"];

    # Determine the server protocol being used
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] != "off") {
        $proto = "https";
    } else $proto = "http";

    # Ensure that data is only sent as plaintext
    header("Content-Type: text/plain");

    # Load either the preboot script or the menu
    if (isset($_GET["mac"]) ) {
        if (isset($_SERVER["PHP_AUTH_USER"]) && isset($_SERVER["PHP_AUTH_PW"])) {
            # Extract the username and password
            $username = $_SERVER["PHP_AUTH_USER"];
            $password = $_SERVER["PHP_AUTH_PW"];

            # TODO Check if the credentials are valid
            if (true) {
                # Present the boot menu
                include "chain/menu.php";
            } else {
                $error = "Invalid username or password";
                include "chain/preboot.php";
            }
        } else {
            $error = "No credentials supplied";
            include "chain/preboot.php";
        }
    } else {
        # First redirect to a preboot script which collects and sends client metadata
        include "chain/preboot.php";
    }
?>