<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mage\Model;

use Mage\Model\Resource\ResourceInterface;

class ConfigIni implements ModelConfigInterface
{
    /**
     * @var array
     */
    protected $data = array();
    /**
     * @var ResourceInterface
     */
    private $resource;

    /**
     * BruteForceConfig constructor.
     * @param ResourceInterface $resource
     * @throws \Exception
     */
    public function __construct(ResourceInterface $resource)
    {
        if ($resource->isReadable()) {
            $this->resource = $resource;
            $this->readConfig();
        } else {
            throw new \Exception("Can't read config file.");
        }
    }

    /**
     * @throws \Exception
     */
    public function readConfig()
    {
        if (false === $data = parse_ini_string($this->resource->read())) {
            throw new \Exception("Bad config file.");
        }
        $this->data = $data;
    }

    /**
     * @param string $name
     * @param null $defaultValue
     * @return mixed|null
     */
    public function get($name, $defaultValue = null)
    {
        return (isset($this->data[$name]) ? $this->data[$name] : $defaultValue);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     * @throws \Exception
     */
    public function set($name, $value)
    {
        if (is_array($value) or is_object($value)) {
            throw new \Exception ("Bad value type.");
        }
        $this->data[$name] = $value;
        return $this;
    }

    public function save()
    {
        if ($this->resource->isWritable()) {
            $res = array();
            foreach ($this->data as $key => $value) {
                $res[] = "$key = " . (is_numeric($value) ? $value : '"' . $value . '"');
            }
            $content = implode("\n", $res);
            $this->resource->write($content);
        } else {
            throw new \Exception("Can't write to config.");
        }
    }

    /**
     * @param string $name
     * @return $this
     */
    public function delete($name)
    {
        if (isset($this->data[$name])) {
            unset($this->data[$name]);
        }
        return $this;
    }

}
