<?php

use Mage\Model\Resource\FTP;

include "vendor/autoload.php";
$ftp = new FTP(
    "ftp://wert2all:_wert2all@localhost/",
    dirname(__FILE__) . DIRECTORY_SEPARATOR . "ftp.ini",
    "/home/wert2all/work/tmp.txt"
);

var_dump($ftp->read());

$ftp->write("xxx");
var_dump($ftp->read());
