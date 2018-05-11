<?php

/**
 * Class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_ImportDelivery
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Transferstock_Requeststock_ImportDelivery extends
    Mage_Adminhtml_Block_Template
{
    /**
     * Get adjust stock csv sample link
     *
     * @return mixed
     */
    public function getCsvSampleLink()
    {
        $url = $this->getUrl('adminhtml/inventorysuccess_transferstock_requeststock/downloadsampleDelivery',
                             array(
                                 '_secure' => true,
                                 'id'      => $this->getRequest()->getParam('id'),
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
                                 'id'      => $this->getRequest()->getParam('id'),
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
        return $this->__("Please choose a CSV file to import product delivered. You can download this sample CSV file");
    }

    /**
     * Get import urk
     *
     * @return mixed
     */
    public function getImportLink()
    {
        return $this->getUrl('adminhtml/inventorysuccess_transferstock_requeststock/importDelivery',
                             array(
                                 '_secure' => true,
                                 'id'      => $this->getRequest()->getParam('id'),
                             ));
    }

    /**
     * Get import title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->__("Import products to create delivery ");
    }
}