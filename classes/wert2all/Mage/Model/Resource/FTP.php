<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mage\Model\Resource;


class FTP implements ResourceInterface
{
    /** @var  string */
    protected $connectionString;
    /** @var \Mage_Connect_Ftp */
    protected $ftp;
    /**
     * @var string
     */
    private $localFilePath;
    /**
     * @var string
     */
    private $remoteFilePath;

    /**
     * FTP constructor.
     * @param $connectionString
     * @param $localFilePath
     * @param $remoteFilePath
     */
    public function __construct($connectionString, $localFilePath, $remoteFilePath)
    {
        $this->connectionString = $connectionString;
        $this->ftp = new \Mage_Connect_Ftp();
        $this->localFilePath = $localFilePath;
        $this->remoteFilePath = $remoteFilePath;

        $this->ftp->connect($this->connectionString);
    }

    /**
     * @return string
     */
    public function read()
    {
        if ($this->isReadable()) {
            return file_get_contents($this->localFilePath);
        }
        return false;
    }

    /**
     * @return boolean
     */
    public function isReadable()
    {
        return ($this->ftp->get($this->localFilePath, $this->remoteFilePath) === true);
    }

    /**
     * @return string
     */
    public function isWritable()
    {
        return $this->isReadable();
    }

    function __destruct()
    {
        $this->ftp->close();
    }

    /**
     * @param string $content
     * @return boolean
     */
    public function write($content)
    {
        return $this->ftp->upload(
            $this->remoteFilePath,
            $this->localFilePath
        );
    }
}
