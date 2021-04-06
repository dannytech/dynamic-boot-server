<?php
    # Loop through the operating systems, and create an array for them
    $imagePaths = glob("$imageStore/*.iso");

    # Convert from full-length paths to just filenames
    $imageFiles = array_map(function($value) {
        return basename($value);
    }, $imagePaths);
?>
#!ipxe

menu Install an Operating System
<?
    # Loop through the detected operating systems and create menu items for every image
    for ($i = 0; $i < count($imageFiles); $i++) {
        echo "item $i " . $imageFiles[$i] . "\n";
    }
?>
item shell iPXE Shell

choose option && goto \${option}

<?
    # Loop through the operating systems and create a chainloader configuration
    for ($i = 0; $i < count($imageFiles); $i++) {
        echo ":$i\n";
        echo "chain $proto://$host/boot.php?image=" . urlencode($imagePaths[$i]) . "\n";
    }
?>

:shell
shell