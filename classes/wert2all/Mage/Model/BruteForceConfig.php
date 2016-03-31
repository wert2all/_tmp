<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mage\Model;

class BruteForceConfig implements ModelConfigInterface
{
    /**
     * @var array
     */
    protected $data = array();
    /** @var string */
    protected $configFile;

    /**
     * BruteForceConfig constructor.
     * @param string $filePath
     * @throws \Exception
     */
    public function __construct($filePath)
    {
        if (is_file($filePath) and is_readable($filePath)) {
            $this->configFile = $filePath;
            $this->readConfig();
        } else {
            throw new \Exception("Can't read config file " . $filePath . ".");
        }
    }

    /**
     * @throws \Exception
     */
    public function readConfig()
    {
        if (false === $data = parse_ini_file($this->configFile)) {
            throw new \Exception("Bad ini file.");
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
        $res = array();
        foreach ($this->data as $key => $value) {
            $res[] = "$key = " . (is_numeric($value) ? $value : '"' . $value . '"');
        }
        $content = implode("\n", $res);
        if (is_writable($this->configFile)) {
            file_put_contents($this->configFile, $content);
        } else {
            throw new \Exception("Can't write to file " . $this->configFile . ".");
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
