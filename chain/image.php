<?php
    # TODO Determine the image type

    $imageFile = rtrim($imageStore, "/") . "/" . $image;

    # If the instruction is to stream an ISO to the client, then do so instead of chainloading
    if (isset($_GET["file"])) {
        if (file_exists($imageFile)) {
            # Get the image MIME type
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $imageFile);

            # Send the content type (overrides top-level type)
            header("Content-Type: $mimeType");

            # Stream the file contents
            $fpointer = fopen($imageFile, "rb");
            fpassthru($fpointer);

            # Prevent further execution
            exit;
        } else {
            http_response_code(404);
            die();
        }
    }
?>
#!ipxe

<?
    # TODO Check to make sure with realpath() that directory traversal is not taking place  
    
    # TODO Prevent command injection

    # Boot by storing the file in memory
    echo "initrd $proto://$host/boot.php?image=" . $_GET["image"] . "&file";
?>