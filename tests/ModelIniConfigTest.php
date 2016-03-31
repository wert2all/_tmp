<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Mage\Model\BruteForceConfig;
use Mage\Validator\BruteForce;

class IniModelTest extends PHPUnit_Framework_TestCase
{
    /** @var  BruteForceConfig */
    protected $model;

    public function testDefaultValue()
    {
        $this->assertEquals("bad", $this->model->get("bat_key", "bad"));
        $this->assertEquals(3, $this->model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, "bad"));
    }

    public function testGettingExistValue()
    {
        $this->assertEquals(3, $this->model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));
    }

    public function testGettingNonExistValue()
    {
        $this->assertEquals(null, $this->model->get("count_bad"));
    }

    public function testSetValue()
    {
        $this->model->set(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, 4);
        $this->assertEquals(4, $this->model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));

        $this->model->set("test", 4);
        $this->assertEquals(4, $this->model->get("test"));
    }

    public function testReReadValues()
    {
        $this->model->set(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, 4);
        $this->assertEquals(4, $this->model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));

        $this->model->readConfig();
        $this->assertEquals(3, $this->model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));
    }

    public function testSaveConfig()
    {
        $this->model->set(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, 4);
        $this->model->save();

        $this->model->readConfig();

        $this->assertEquals(4, $this->model->get(BruteForce::MODEL_KEY_ATTEMPTS_COUNT));

        $this->model->set(BruteForce::MODEL_KEY_ATTEMPTS_COUNT, 3);
        $this->model->save();
    }

    /**
     * @expectedException Exception
     */
    public function testArraySetting()
    {
        $this->model->set("bad", array());
    }

    /**
     * @expectedException Exception
     */
    public function testObjectSetting()
    {
        $this->model->set("bad", new stdClass());
    }

    public function testUnsetValue()
    {
        $this->model->set("test", "bad");
        $this->model->delete("test");

        $this->assertEquals(null, $this->model->get("test"));
    }

    /**
     * @expectedException Exception
     */
    public function testBadFilePath()
    {
        new BruteForceConfig(self::getConfigPath() . "bad");
    }

    /**
     * @return string
     */
    public static function getConfigPath()
    {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR . "good_test.ini";
    }

    protected function setUp()
    {
        parent::setUp();
        $this->model = new BruteForceConfig(self::getConfigPath());
    }
}
