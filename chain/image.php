<?php
    # TODO Determine the image type

    $imageFile = rtrim($imageStore, "/") . "/" . $image;

    # If the instruction is to extract a file from the image, then do so instead of chainloading
    if ((isset($_GET["file"]) || isset($_GET["raw"]))) {
        if (file_exists($imageFile)) {
            if (isset($_GET["file"])) {
                $file = $_GET["file"];

                # Run 7zip to extract the file from the ISO and stream it to the browser
                passthru("7z e '-i!$file' -so $imageFile");
            } else if (isset($_GET["raw"])) {
                # Stream the entire image file to the client
                $fpointer = fopen($imageFile, "rb");
                fpassthru($fpointer);
            }

            # Prevent further execution
            exit;
        } else {
            http_response_code(404);
            die();
        }
    }
?>
#!ipxe

menu What kind of operating system is this?
item --key w windows (W)indows 7 or beyond
item --key d memory I (D)on't know

choose option && goto ${option}

<?
    # TODO Check to make sure with realpath() that directory traversal is not taking place  
    
    # TODO Prevent command injection (when using 7z)

    # TODO This can also read files that are not .iso

    # Boot by storing the file in memory
?>

:memory
kernel tftp://<?= $host ?>/memdisk
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&raw
boot

:windows
kernel tftp://<?= $host ?>/wimboot
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=boot/bcd BCD
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=boot/boot.sdi boot.sdi
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=sources/boot.wim boot.wim
boot
