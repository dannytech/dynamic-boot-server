<?php
    # Loop through the operating systems, and create an array for them
    $imageFiles = array();

    # Recursively walk the image store
    $dir = new RecursiveDirectoryIterator($imageStore);
    $iter = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($iter, "/.*\.iso/", RegexIterator::GET_MATCH);

    foreach($files as $file) {
        $path = $file[0];
        $query = $_GET["query"];

        # Select entries matching the user-provided query, or all if no query was provided
        if ((isset($query) && stristr(basename($path), $query))
            || !isset($query)) {
            array_push($imageFiles, $file[0]);
        }
    }
?>
#!ipxe

menu Install an Operating System
item search Search...
<?
    # Loop through the detected operating systems and create menu items for every image
    for ($i = 0; $i < count($imageFiles); $i++) {
        echo "item $i " . basename($imageFiles[$i]) . "\n";
    }
?>
item shell iPXE Shell

choose option && goto \${option}

:search
echo -n "Enter a search term" && read query
chain "<?= $proto ?>://<?= $host ?>/boot.php?query=\${query}"
<?
    # Loop through the operating systems and create a chainloader configuration
    for ($i = 0; $i < count($imageFiles); $i++) {
        # Determine how much of the path is outside the image store path
        $baseLength = strlen(trim($imageStore, "/")) + 1;

        # Create a trimmed-down file path
        $imagePath = substr($imageFiles[$i], $baseLength);

        # Embed the image path into a chainloader item
        echo ":$i\n";
        echo "chain $proto://$host/boot.php?image=" . urlencode($imagePath) . "\n";
    }
?>
:shell
shell