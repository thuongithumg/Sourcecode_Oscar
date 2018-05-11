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
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Import
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Adjuststock_Import extends Mage_Adminhtml_Block_Template
{
    /**
     * Get adjust stock csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink() {
        $url = $this->getUrl('adminhtml/inventorysuccess_adjuststock_product/downloadsample',
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
        return 'Please choose a CSV file to import product adjust stock. You can download this sample CSV file';
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink() {
        return $this->getUrl('adminhtml/inventorysuccess_adjuststock_product/import',
            array(
                '_secure' => true,
                'id' => $this->getRequest()->getParam('id')
            ));
    }

    /**
     * Get import title
     *
     * @return string
     */
    public function getTitle() {
        return 'Import products';
    }
}