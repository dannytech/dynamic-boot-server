<?php
    # Determine the image type

    # If the instruction is to stream an ISO to the client, then do so instead of chainloading
    if (isset($_GET["file"])) {
        $imageFile = trim($imageStore, "/") . "/" . $_GET["image"];
        
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
    }
?>
#!ipxe

<?
    # TODO Check to make sure with realpath() that directory traversal is not taking place  
    
    # TODO Prevent command injection

    # Boot by storing the file in memory
    echo "initrd $proto://$host/boot.php?image=" . $_GET["image"] . "&file";
?>