<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mage\Validator;


use Mage\Model\ModelConfigInterface;

class BruteForce
{
    const MODEL_KEY_ATTEMPTS_COUNT = "brute-force-attempts-count";
    const MODEL_KEY_BAD_ATTEMPTS_COUNT = "brute-force-bad-attempts-count";
    const MODEL_KEY_LAST_BAD_TIME = "brute-force-last-bad-time";
    const MODEL_KEY_DIFF_TIME_TO_ATTEMPT = "brute-force-diff-time-to-attempt";

    const DEFAULT_ATTEMPTS_COUNT = 3;
    const DEFAULT_BAD_ATTEMPTS_COUNT = 0;
    const DEFAULT_DIFF_TIME_TO_ATTEMPT = 3 * 60 * 60; // 3 minutes


    /** @var ModelConfigInterface */
    protected $model;

    /**
     * BruteForce constructor.
     * @param ModelConfigInterface $model
     */
    public function __construct(ModelConfigInterface $model)
    {
        $this->model = $model;
    }

    public function isCanLogin()
    {
        return $this->getBadAttempts() <= $this->getConfigAttemptsCount();
    }

    /**
     * @return int
     */
    protected function getBadAttempts()
    {
        return $this->model->get(self::MODEL_KEY_BAD_ATTEMPTS_COUNT, self::DEFAULT_BAD_ATTEMPTS_COUNT);
    }

    /**
     * @return int
     */
    protected function getConfigAttemptsCount()
    {
        return $this->model->get(self::MODEL_KEY_ATTEMPTS_COUNT, self::DEFAULT_ATTEMPTS_COUNT);
    }

    public function getTimeToAttempt()
    {
        return $this->model->get(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, self::DEFAULT_DIFF_TIME_TO_ATTEMPT);
    }

    /**
     * @return $this
     */
    public function doGoodLogin()
    {
        $this->reset();
        return $this;
    }

    public function reset()
    {
        $this->model
            ->set(self::MODEL_KEY_BAD_ATTEMPTS_COUNT, self::DEFAULT_BAD_ATTEMPTS_COUNT)
            ->set(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, self::DEFAULT_DIFF_TIME_TO_ATTEMPT)
            ->delete(self::MODEL_KEY_LAST_BAD_TIME)
            ->save();
    }

    public function doBadLogin()
    {
        $badAttempts = $this->getBadAttempts();

        $this->model
            ->set(self::MODEL_KEY_BAD_ATTEMPTS_COUNT, ++$badAttempts)
            ->set(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, $this->getDiffTimeToNextAttempt())
            ->set(self::MODEL_KEY_LAST_BAD_TIME, time())
            ->save();
    }

    private function getDiffTimeToNextAttempt()
    {
        return $this->model->get(self::MODEL_KEY_DIFF_TIME_TO_ATTEMPT, self::DEFAULT_DIFF_TIME_TO_ATTEMPT);
    }
}
