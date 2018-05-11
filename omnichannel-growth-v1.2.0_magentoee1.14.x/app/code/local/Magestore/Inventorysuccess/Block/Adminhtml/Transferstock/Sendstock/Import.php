<?php

/**
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Import
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Sendstock_Import extends Mage_Adminhtml_Block_Template
{
    /**
     * Get adjust stock csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink()
    {
        $url = $this->getUrl('adminhtml/inventorysuccess_transferstock_sendstock/downloadsample',
            array(
                '_secure' => true,
                'id' => $this->getRequest()->getParam('id')
            ));
        return $url;
    }

    /**
     * Get adjust stock csv sample link
     *
     * @return mixed
     */
    public function getCsvBarcodeSampleLink()
    {
        $url = $this->getUrl('adminhtml/barcodesuccess_import/importProductSample',
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
    public function getContent()
    {
        return $this->__("Please choose a CSV file to import products sent. You can download this sample CSV file");
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink()
    {
        return $this->getUrl('adminhtml/inventorysuccess_transferstock_sendstock/import',
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
    public function getTitle()
    {
        return $this->__("Import products");
    }
}