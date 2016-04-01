<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Tests\Model;

use Exception;
use Mage\Model\ConfigIni;
use Mage\Model\Resource\ResourceInterface;
use Mage\Validator\BruteForce;
use PHPUnit_Framework_TestCase;
use stdClass;
use Tests\Framework\ResourceProvider;

class ModelWithFileTest extends PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider  provideResources
     * @param ConfigIni $model
     */
    public function testDefaultValue(ConfigIni $model)
    {
        $this->assertEquals("bad", $model->get("bat_key", "bad"));
        $this->assertEquals(3, $model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, "bad"));
    }

    /**
     * @dataProvider  provideResources
     * @param ConfigIni $model
     */
    public function testGettingExistValue(ConfigIni $model)
    {
        $this->assertEquals(3, $model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));
    }

    /**
     * @dataProvider  provideResources
     * @param ConfigIni $model
     */
    public function testGettingNonExistValue(ConfigIni $model)
    {
        $this->assertEquals(null, $model->get("count_bad"));
    }

    /**
     * @dataProvider  provideResources
     * @param ConfigIni $model
     * @throws Exception
     */
    public function testSetValue(ConfigIni $model)
    {
        $model->set(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, 4);
        $this->assertEquals(4, $model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));

        $model->set("test", 4);
        $this->assertEquals(4, $model->get("test"));
    }

    /**
     * @dataProvider  provideResources
     * @param ConfigIni $model
     * @throws Exception
     */
    public function testReReadValues(ConfigIni $model)
    {
        $model->set(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, 4);
        $this->assertEquals(4, $model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));

        $model->readConfig();
        $this->assertEquals(3, $model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));
    }

    /**
     * @dataProvider  provideResources
     * @param ConfigIni $model
     * @throws Exception
     */
    public function testSaveConfig(ConfigIni $model)
    {
        $model->set(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, 4);
        $model->save();

        $model->readConfig();

        $this->assertEquals(4, $model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));

        $model->set(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, 3);
        $model->save();
    }

    /**
     * @expectedException Exception
     * @param ConfigIni $model
     * @dataProvider  provideResources
     * @throws Exception
     */
    public function testArraySetting(ConfigIni $model)
    {
        $model->set("bad", array());
    }

    /**
     * @dataProvider provideResources
     * @expectedException Exception
     * @param ConfigIni $model
     * @throws Exception
     */
    public function testObjectSetting(ConfigIni $model)
    {
        $model->set("bad", new stdClass());
    }

    /**
     * @dataProvider  provideResources
     * @param ConfigIni $model
     * @throws Exception
     */
    public function testUnsetValue(ConfigIni $model)
    {
        $model->set("test", "bad");
        $model->delete("test");

        $this->assertEquals(null, $model->get("test"));
    }

    /**
     * @dataProvider provideBadResources
     * @expectedException Exception
     * @param ResourceInterface $bad
     */
    public function testBadFilePath(ResourceInterface $bad)
    {
        new ConfigIni($bad);
    }

    public function provideBadResources()
    {
        $return = array();
        $resources = (new ResourceProvider())->getBadResources();
        foreach ($resources as $resource) {
            $return[] = array($resource);
        }
        return $return;
    }

    public function provideResources()
    {
        return $this->makeProvider((new ResourceProvider())->getGoodResources());
    }

    private function makeProvider(array $resources)
    {
        $return = array();
        foreach ($resources as $resource) {
            $return[] = array(new ConfigIni($resource));
        }
        return $return;
    }
}
