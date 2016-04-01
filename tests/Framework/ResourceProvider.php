<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Tests\Framework;

use Mage\Model\Resource\File;

class ResourceProvider
{

    public function getGoodResources()
    {
        return array(
            new File(self::getConfigPath()),
        );
    }

    private static function getConfigPath()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . "good_test.ini";
    }

    /**
     * @return array
     */
    public function getBadResources()
    {
        return array(
            (new File(self::getConfigPath() . "bad")),
        );
    }
}
