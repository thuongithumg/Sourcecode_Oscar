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
 * Adjuststock Index Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_PurchaseorderController extends Mage_Core_Controller_Front_Action
{
    /**
     * index action
     */
    public function downloadcsvAction()
    {
        $purchaseKey = $this->getRequest()->getParam('key');
        $purchaseOrder = Mage::getModel('purchaseordersuccess/purchaseorder')->load($purchaseKey, 'purchase_key');
        $content = $this->csvData($purchaseOrder);
        $fileName = 'purchase_order_items.csv';
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * index action
     */
    public function downloadpdfAction()
    {
        $purchaseKey = $this->getRequest()->getParam('key');
        $url = Mage::getUrl('purchaseordersuccess/purchaseorder/data', ['key' => $purchaseKey]);
        $return = "
                <script>
                    window.open('".$url."','PrintWindow', 'width=500,height=500,top=200,left=200').print();
                    
                    setTimeout(function(){ 
                        close(); 
                        }, 100
                    );
                </script>
            ";
        return $this->getResponse()->setBody($return);
    }

    public function dataAction()
    {
        $layout = Mage::app()->getLayout();
        $html = $layout->createBlock('purchaseordersuccess/purchaseorder_pdf_header')->toHtml();
        $html .= $layout->createBlock('purchaseordersuccess/purchaseorder_pdf_items')->toHtml();
        $html .= $layout->createBlock('purchaseordersuccess/purchaseorder_pdf_footer')->toHtml();
        return $this->getResponse()->setBody($html);
    }

    /**
     * @param Magestore_Purchaseordersuccess_Model_Purchaseorder $purchaseOrder
     * @return string
     */
    protected function csvData($purchaseOrder) {

        $csv = '';
        $data = array();
        $columns = array('PRODUCT_NAME', 'PRODUCT_SKU', 'SUPPLIER_SKU',
            'QTY', "QTY_RECEIVED", "COST",
            "TAX", 'DISCOUNT');
        /* prepare data */
        $products = $purchaseOrder->getItems();
        if($products->getSize()) {
            /** @var Magestore_Purchaseordersuccess_Model_Purchaseorder_Item $product */
            foreach($products as $product) {
                $data[] = array(
                    $product->getProductName(),
                    $product->getProductSku(),
                    $product->getProductSupplierSku(),
                    $product->getQtyOrderred(),
                    $product->getQtyReceived(),
                    $product->getCost() * $purchaseOrder->getCurrencyRate(),
                    $product->getTax(),
                    $product->getDiscount()
                );
            }
        }

        /* bind data to $csv */
        $csv.= implode(',', $columns)."\n";
        foreach($data as $row) {
            $csv.= implode(',', $row)."\n";
        }
        return $csv;
    }
}