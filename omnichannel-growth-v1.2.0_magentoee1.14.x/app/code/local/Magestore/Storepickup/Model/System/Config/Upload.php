<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_System_Config_Upload
 */
class Magestore_Storepickup_Model_System_Config_Upload
{
    /**
     * @return string
     */
    public function getBaseMediaPathAddition()
    {
        return 'storepickup' . DS . 'images';
    }

    /**
     * Web-based directory path of product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaUrlAddition()
    {
        return 'storepickup/images';
    }

    /**
     * Filesystem directory path of temporary product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaPathAddition()
    {
        return 'tmp' . DS . $this->getBaseMediaPathAddition();
    }

    /**
     * Web-based directory path of temporary product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseTmpMediaUrlAddition()
    {
        return 'tmp/' . $this->getBaseMediaUrlAddition();
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return Mage::getBaseDir('media') . DS . 'storepickup' . DS . 'images';
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return Mage::getBaseUrl('media') . 'storepickup/images';
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaPath()
    {
        return Mage::getBaseDir('media') . DS . $this->getBaseTmpMediaPathAddition();
    }

    /**
     * @return string
     */
    public function getBaseTmpMediaUrl()
    {
        return Mage::getBaseUrl('media') . $this->getBaseTmpMediaUrlAddition();
    }

    /**
     * @param $file
     * @return string
     */
    public function getMediaUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if(substr($file, 0, 1) == '/') {
            return $this->getBaseMediaUrl() . $file;
        }

        return $this->getBaseMediaUrl() . '/' . $file;
    }

    /**
     * @param $file
     * @return string
     */
    public function getMediaPath($file)
    {
        $file = $this->_prepareFileForPath($file);

        if(substr($file, 0, 1) == DS) {
            return $this->getBaseMediaPath() . DS . substr($file, 1);
        }

        return $this->getBaseMediaPath() . DS . $file;
    }

    /**
     * @param $file
     * @return string
     */
    public function getTmpMediaUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if(substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseTmpMediaUrl() . '/' . $file;
    }

    /**
     * Part of URL of temporary product images
     * relatively to media folder
     *
     * @param $file
     * @return string
     */
    public function getTmpMediaShortUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if(substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseTmpMediaUrlAddition() . '/' . $file;
    }

    /**
     * Part of URL of product images relatively to media folder
     *
     * @param $file
     * @return string
     */
    public function getMediaShortUrl($file)
    {
        $file = $this->_prepareFileForUrl($file);

        if(substr($file, 0, 1) == '/') {
            $file = substr($file, 1);
        }

        return $this->getBaseMediaUrlAddition() . '/' . $file;
    }

    /**
     * @param $file
     * @return string
     */
    public function getTmpMediaPath($file)
    {
        $file = $this->_prepareFileForPath($file);

        if(substr($file, 0, 1) == DS) {
            return $this->getBaseTmpMediaPath() . DS . substr($file, 1);
        }

        return $this->getBaseTmpMediaPath() . DS . $file;
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function _prepareFileForUrl($file)
    {
        return str_replace(DS, '/', $file);
    }

    /**
     * @param $file
     * @return mixed
     */
    protected function _prepareFileForPath($file)
    {
        return str_replace('/', DS, $file);
    }
}

