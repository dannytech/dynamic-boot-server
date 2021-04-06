#!ipxe

<? if (isset($error)) echo "echo " . $error; ?>

login || goto quit
chain <?= $proto ?>://\${username:uristring}:\${password:uristring}@<?= $host ?>/boot.php?mac=\${netX/max}

:quit