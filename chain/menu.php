#!ipxe

:menustart
menu Boot Menu
item memtest

choose os && goto \${os}

:shell