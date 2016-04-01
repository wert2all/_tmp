<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Tests\Framework;

use Mage\Model\Resource\File;
use Mage\Model\Resource\FTP;

class ResourceProvider
{

    public function getGoodResources()
    {
        return array(
            new File(self::getConfigPath()),
            new FTP(
                "ftp://wert2all:_wert2all@localhost/",
                dirname(__FILE__) . DIRECTORY_SEPARATOR . "ftp.ini",
                "/home/wert2all/work/code/magento1/downloader/brute-force.ini"
            )
        );
    }

    private static function getConfigPath()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . "file.ini";
    }

    /**
     * @return array
     */
    public function getBadResources()
    {
        return array(
            (new File(self::getConfigPath() . "bad")),
            new FTP("ftp://wert2all:_wert2all@localhost/", dirname(__FILE__) . DIRECTORY_SEPARATOR . "ftp.ini", "/tmp/")
        );
    }
}
