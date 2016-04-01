<?php

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Tests;

use Mage\Model\ConfigIni;
use Mage\Validator\BruteForce;
use PHPUnit_Framework_TestCase;
use Tests\Framework\ResourceProvider;

class BruteForceTest extends PHPUnit_Framework_TestCase
{
    /** @var  BruteForce */
    protected $validator;
    /** @var  ConfigIni */
    protected $model;

    public function testCanLogin()
    {
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

    public function testEnlargeTimeToLogin()
    {
        $this->setCantLogin();
        $this->assertEquals(BruteForce::DEFAULT_DIFF_TIME_TO_ATTEMPT, $this->validator->getTimeToAttempt());

        $this->setCantLogin();
        $this->assertEquals(BruteForce::DEFAULT_DIFF_TIME_TO_ATTEMPT * 2, $this->validator->getTimeToAttempt());

    }

    public function testGoodLoginAfterBad()
    {
        $this->setCantLogin();
        $this->setCantLogin();
        $this->assertEquals(BruteForce::DEFAULT_DIFF_TIME_TO_ATTEMPT * 2, $this->validator->getTimeToAttempt());
        $this->validator->doGoodLogin();
        $this->checkReset();
        $this->setCantLogin();
        $this->assertEquals(BruteForce::DEFAULT_DIFF_TIME_TO_ATTEMPT, $this->validator->getTimeToAttempt());
    }

    protected function setUp()
    {
        parent::setUp();

        $provider = new ResourceProvider();
        $resources = $provider->getGoodResources();
        $resource = current($resources);

        $this->model = new ConfigIni($resource);
        $this->validator = new BruteForce($this->model);
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->validator->reset();
    }
}
