<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Tests\Resource;


use Mage\Model\Resource\ResourceInterface;
use Tests\Framework\ResourceProvider;

class ResourceInterfaceTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @return string
     */
    public static function getConfigPath()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . "good_test.ini";
    }

    public function providerObjects()
    {
        $provider = new ResourceProvider();
        $goodResourceObjects = $provider->getGoodResources();
        $badResourceObjects = $provider->getBadResources();
        $return = array();
        /** @var ResourceInterface $resourceObject */
        foreach ($goodResourceObjects as $index => $resourceObject) {
            $return[] = array(
                $resourceObject,
                (isset($badResourceObjects[$index]) ? $badResourceObjects[$index] : null)
            );
        }
        return $return;
    }

    /**
     *
     * @dataProvider providerObjects
     * @param ResourceInterface $good
     * @param ResourceInterface $bad
     */
    public function testIsReadable(ResourceInterface $good, ResourceInterface $bad)
    {
        $this->assertTrue($good->isReadable());
        $this->assertFalse($bad->isReadable());
    }

    /**
     *
     * @dataProvider providerObjects
     * @param ResourceInterface $good
     * @param ResourceInterface $bad
     */
    public function testIsWritable(ResourceInterface $good, ResourceInterface $bad)
    {
        $this->assertTrue($good->isWritable());
        $this->assertFalse($bad->isWritable());
    }

    /**
     *
     * @dataProvider providerObjects
     * @param ResourceInterface $good
     * @param ResourceInterface $bad
     */
    public function testRead(ResourceInterface $good, ResourceInterface $bad)
    {
        $this->assertFalse($bad->read());

        $this->assertNotEquals(
            0,
            strlen($good->read())
        );
    }

    /**
     *
     * @dataProvider providerObjects
     * @param ResourceInterface $good
     * @param ResourceInterface $bad
     */
    public function testWrite(ResourceInterface $good, ResourceInterface $bad)
    {
        $this->assertFalse($bad->write("sss"));
        $this->assertTrue($good->write($good->read()));
    }
}
