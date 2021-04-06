<?php
    # Loop through the operating systems, and create an array for them
    $imageFiles = array();

    # Recursively walk the image store
    $dir = new RecursiveDirectoryIterator($imageStore);
    $iter = new RecursiveIteratorIterator($dir);
    $files = new RegexIterator($iter, "/.*\.iso/", RegexIterator::GET_MATCH);

    foreach($files as $file) {
        $path = $file[0];

        # Select entries matching the user-provided query, or all if no query was provided
        if ((isset($_GET["query"]) && stristr(basename($path), $_GET["query"]))
            || !isset($_GET["query"])) {
            array_push($imageFiles, $file[0]);
        }
    }
?>
#!ipxe

menu Install an Operating System

item --key s search (S)earch...

item --gap
item --gap Operating Systems
<?
    # Loop through the detected operating systems and create menu items for every image
    for ($i = 0; $i < count($imageFiles); $i++) {
        echo "item $i " . basename($imageFiles[$i]) . "\n";
    }
?>

item --gap
item --gap Other
item --key l local Boot from (L)ocal hard disk
item --key p shell i(P)XE Shell

choose option && goto ${option}

:local
sanboot --no-describe --drive 0x80

:search
echo -n Enter a search term: ${} && read query
chain <?= $proto ?>://<?= $host ?>/boot.php?query=${query:uristring}

<?
    # Loop through the operating systems and create a chainloader configuration
    for ($i = 0; $i < count($imageFiles); $i++) {
        # Determine how much of the path is outside the image store path
        $baseLength = strlen(rtrim($imageStore, "/")) + 1;

        # Create a trimmed-down file path
        $imagePath = substr($imageFiles[$i], $baseLength);

        # Embed the image path into a chainloader item
        echo ":$i\n";
        echo "chain $proto://$host/boot.php?image=" . urlencode($imagePath) . "\n";
    }
?>

:shell
shell