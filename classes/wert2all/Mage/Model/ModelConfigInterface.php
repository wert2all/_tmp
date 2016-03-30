<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mage\Model;


interface ModelConfigInterface
{

    /**
     * @param string $key
     * @param mixed|null $defaultValue
     * @return mixed
     */
    public function get($key, $defaultValue = null);

    /**
     * @param string $key
     * @param mixed $value
     * @return ModelConfigInterface
     * @throws \Exception
     */
    public function set($key, $value);

    /**
     * @return void
     */
    public function save();

    /**
     * @param string $key
     * @return ModelConfigInterface
     */
    public function delete($key);
}
