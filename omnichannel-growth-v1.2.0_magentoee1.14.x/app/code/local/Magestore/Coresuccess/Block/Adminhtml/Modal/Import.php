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
 * @package     Magestore_Coresuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */
abstract class Magestore_Coresuccess_Block_Adminhtml_Modal_Import extends Magestore_Coresuccess_Block_Adminhtml_Modal_Abstract
{
    /**
     *
     * @var string
     */
    protected $modalId = 'import_product';

    /**
     * allowed file types
     *
     * @var array
     */
    protected $allowFileTypes = array(
        'text/csv',
        'application/vnd.ms-excel'
    );

    /*
     * 
     */
    protected function _construct()
    {
        $this->setTemplate('coresuccess/modal/import.phtml');
        parent::_construct();
    }

    /**
     * Get import title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->__('Import Products');
    }


    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->__('Please choose a CSV file to import products. You can download the sample CSV file below.');
    }

    /**
     * Get csv sample dowload link
     *
     * @return string
     */
    abstract public function getCsvSampleLink();

    /**
     * Get import url
     *
     * @return string
     */
    abstract public function getImportLink();

    /**
     *
     * @return string
     */
    public function getAllowedFileTypes()
    {
        return Zend_Json::encode($this->allowFileTypes);
    }

}