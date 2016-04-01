<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mage\Model\Resource;

interface ResourceInterface
{

    /**
     * @return boolean
     */
    public function isReadable();

    /**
     * @return string
     */
    public function read();

    /**
     * @return string
     */
    public function isWritable();

    /**
     * @param string $content
     * @return boolean
     */
    public function write($content);
}
