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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Import
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Stocktaking_Import extends Mage_Adminhtml_Block_Template
{
    /**
     * Get adjust stock csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink() {
        $url = $this->getUrl('adminhtml/inventorysuccess_stocktaking_product/downloadsample',
            array(
                '_secure' => true,
                'id' => $this->getRequest()->getParam('id')
            ));
        return $url;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent() {
        $content = 'Please choose a CSV file to import product to stocktake. You can download this sample CSV file';
        if($this->getStocktaking() &&
            $this->getStocktaking()->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING){
            $content = 'Please choose a CSV file to import product to count. You can download this sample CSV file';
        }
        return $content;
    }

    /**
     * get current stocktaking
     *
     * @return Magestore_Inventorysuccess_Model_Stocktaking
     */
    public function getStocktaking()
    {
        return Mage::getModel('inventorysuccess/stocktaking')->load($this->getRequest()->getParam('id'));
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink() {
        return $this->getUrl('adminhtml/inventorysuccess_stocktaking_product/import',
            array(
                '_secure' => true,
                'id' => $this->getRequest()->getParam('id'),
                'status' => $this->getStocktaking()->getStatus()
            ));
    }

    /**
     * Get import title
     *
     * @return string
     */
    public function getTitle() {
        $title = 'Import products to stocktake';
        if($this->getStocktaking() &&
            $this->getStocktaking()->getStatus() == Magestore_Inventorysuccess_Model_Stocktaking::STATUS_PROCESSING){
            $title = 'Import products to count';
        }
        return $title;
    }
}