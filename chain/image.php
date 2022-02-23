<?php
    $image = $_GET["image"];

    # Get absolute path to image file
    $imageFile = realpath($imageStore . "/" . $image);

    # (Hopefully) prevent LFI
    if (strncmp($imageFile, $imageStore, strlen($imageStore)) != 0) {
        http_response_code(403);
        die();
    }

    # Ensure the target ISO actually exists
    if (!file_exists($imageFile)) {
        http_response_code(404);
        die();
    }

    # Stream files instead of chainloading
    if (isset($_GET["file"])) {
        $file = $_GET["file"];

        if (file_exists("iso9660://$imageFile#$file")) {
            # Stream a file directly off the CDFS
            $fpointer = fopen("iso9660://$imageFile#$file", "rb");
            fpassthru($fpointer);

            exit();
        } else {
            http_response_code(404);
            die();
        }
    } else if (isset($_GET["raw"])) {
        # Stream the entire image file to the client
        $fpointer = fopen($imageFile, "rb");
        fpassthru($fpointer);

        exit();
    }
?>
#!ipxe

menu Which operating system is this?
item --gap <?= $image ?>

item --gap
item --gap Windows
item --key w windows (W)indows 7 or beyond - Boot with Windows PE

item --gap
item --gap Linux
item --key r rhel (R)ed Hat Enterprise Linux or similar
item --key d debian (D)ebian or similar
item --key a memory (A)rch Linux live

item --gap
item --gap Other
item memory Unsure - Try to boot with a memory disk

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
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=bootmgr bootmgr
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=boot/bcd BCD
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=boot/boot.sdi boot.sdi
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=sources/boot.wim boot.wim
boot

:rhel
kernel <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=images/pxeboot/vmlinuz initrd=initrd.img
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=images/pxeboot/initrd.img
boot

:debian
kernel <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=casper/vmlinuz initrd=initrd
initrd <?= $proto ?>://<?= $host ?>/boot.php?image=<?= $image ?>&file=casper/initrd
boot
