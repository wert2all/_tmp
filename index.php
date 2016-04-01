<?php

use Mage\Model\Resource\FTP;

include "vendor/autoload.php";

$res = new FTP("ftp://wert2all:_wert2all@localhost/", dirname(__FILE__) . DIRECTORY_SEPARATOR . "test.ini", "/home/wert2all/work/code/magento1/downloader/index.php");
$ma = new Mage_Connect_Ftp();

$ma->connect( "ftp://wert2all:_wert2all@localhost/");

var_dump( $res->isReadable());
