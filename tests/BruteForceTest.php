<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

use Mage\Model\BruteForceConfig;
use Mage\Validator\BruteForce;

class BruteForceTest extends PHPUnit_Framework_TestCase
{
    /** @var  BruteForce */
    protected $validator;
    /** @var  BruteForceConfig */
    protected $model;

    public function testCanLogin()
    {
        $this->validator->reset();
        $this->assertTrue($this->validator->isCanLogin());
    }

    public function testMoreBadAttemptsAndTimeNotPass()
    {
        $this->setCantLogin();
        $this->assertFalse($this->validator->isCanLogin());
    }

    protected function setCantLogin()
    {
        for ($i = 0; $i < BruteForce::DEFAULT_ATTEMPTS_COUNT + 1; $i++) {
            $this->validator->doBadLogin();
        }
    }

    public function testTimeToAttempt()
    {
        $this->assertEquals(BruteForce::DEFAULT_DIFF_TIME_TO_ATTEMPT, $this->validator->getTimeToAttempt());
    }

    public function testGoodLogin()
    {
        $this->setCantLogin();
        $this->assertFalse($this->validator->isCanLogin());

        $this->validator->doGoodLogin();

        $this->checkReset();
    }

    private function checkReset()
    {
        foreach ($this->resetProvider() as $testValues) {
            $this->testReset($testValues[0], $testValues[1]);
        }
    }

    public function resetProvider()
    {
        return array(
            array(BruteForce::DEFAULT_BAD_ATTEMPTS_COUNT, BruteForce::MODEL_KEY_BAD_ATTEMPTS_COUNT),
            array(BruteForce::DEFAULT_DIFF_TIME_TO_ATTEMPT, BruteForce::MODEL_KEY_DIFF_TIME_TO_ATTEMPT),
            array(null, BruteForce::MODEL_KEY_LAST_BAD_TIME),
        );
    }

    /**
     * @dataProvider resetProvider
     * @param mixed $expect
     * @param string $key
     */
    public function testReset($expect, $key)
    {
        $this->assertEquals($expect, $this->model->get($key));
    }

    public function testBadLogin()
    {
        $this->validator->doBadLogin();
        $this->assertTrue(true, $this->validator->isCanLogin());
        $this->assertEquals(BruteForce::DEFAULT_BAD_ATTEMPTS_COUNT + 1, $this->model->get(BruteForce::MODEL_KEY_BAD_ATTEMPTS_COUNT));
        $this->assertEquals(BruteForce::DEFAULT_DIFF_TIME_TO_ATTEMPT, $this->model->get(BruteForce::MODEL_KEY_DIFF_TIME_TO_ATTEMPT));
        $this->assertNotNull($this->model->get(BruteForce::MODEL_KEY_LAST_BAD_TIME));

        $this->validator->doBadLogin();
        $this->assertEquals(BruteForce::DEFAULT_BAD_ATTEMPTS_COUNT + 2, $this->model->get(BruteForce::MODEL_KEY_BAD_ATTEMPTS_COUNT));
    }


    public function testBadLoginWithLock()
    {
        for ($i = 0; $i < BruteForce::DEFAULT_ATTEMPTS_COUNT + 1; $i++) {
            $this->validator->doBadLogin();
        }
        $this->assertFalse($this->validator->isCanLogin());
    }

    public function testBadLoginIncreaseTime()
    {
        $fakeTime = time() - 1000;
        $this->validator->doGoodLogin();
        $this->model
            ->set(BruteForce::MODEL_KEY_LAST_BAD_TIME, $fakeTime)
            ->save();

        $this->validator->doBadLogin();

        $this->assertTrue($this->model->get(BruteForce::MODEL_KEY_LAST_BAD_TIME) > $fakeTime);

    }

    public function testIsCanLoginAfterTime()
    {
        $this->setCantLogin();

        $this->model
            ->delete(BruteForce::MODEL_KEY_LAST_BAD_TIME)
            ->save();
        $this->assertTrue($this->validator->isCanLogin());

        $this->setCantLogin();

        $timeToAttempt = $this->model->get(BruteForce::MODEL_KEY_DIFF_TIME_TO_ATTEMPT) + 10;
        $this->model
            ->set(
                BruteForce::MODEL_KEY_LAST_BAD_TIME,
                $this->model->get(BruteForce::MODEL_KEY_LAST_BAD_TIME) - $timeToAttempt
            )
            ->save();

        $this->assertTrue($this->validator->isCanLogin());
    }

    protected function setUp()
    {
        parent::setUp();

        $this->model = new BruteForceConfig(IniModelTest::getConfigPath());
        $this->validator = new BruteForce($this->model);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->validator->reset();
    }
}
