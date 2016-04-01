<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mage\Model\Resource;

class File implements ResourceInterface
{
    /** @var string */
    protected $filePath;

    /**
     * File constructor.
     * @param string $filePath
     */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @return string
     */
    public function read()
    {
        if ($this->isReadable()) {
            return file_get_contents($this->filePath);
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function isReadable()
    {
        return (is_file($this->filePath) and is_readable($this->filePath));
    }

    /**
     * @param string $content
     * @return boolean
     */
    public function write($content)
    {
        if ($this->isWritable()) {
            return (boolean)file_put_contents($this->filePath, $content);
        }
        return false;
    }

    /**
     * @return string
     */
    public function isWritable()
    {
        return (is_file($this->filePath) and is_writable($this->filePath));
    }
}
