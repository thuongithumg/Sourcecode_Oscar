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
 * Stocktaking Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Stocktaking_ProductController extends Mage_Adminhtml_Controller_Action
{
    /**
     * download sample products to adjust stock
     *
     * @return $this
     */
    public function downloadsampleAction()
    {
        $fileName   = 'stocktaking_import_product_sample.csv';
        $content    = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_stocktaking_sample')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * import products to adjust stock
     *
     * @return $this
     */
    public function importAction()
    {
        if ($data = $this->getRequest()->isPost()) {
            $id = $this->getRequest()->getParam('id');
            $status = $this->getRequest()->getParam('status');
            if (isset($_FILES['import_product']['name']) && $_FILES['import_product']['name'] != '') {
                try {
                    $importHandler = Magestore_Coresuccess_Model_Service::stocktakingImportService();
                    $importHandler->importFromCsvFile($_FILES['import_product'], $id, $status);
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        Mage::helper('inventorysuccess')->__('The product adjustment has been imported.')
                    );

                } catch (\Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                }
            }
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('inventorysuccess')->__('Invalid file upload attempt')
            );
        }
        return $this->_redirect('*/inventorysuccess_stocktaking/edit', array('id' => $id));
    }

    /**
     * export grid stocktakings to CSV type
     */
    public function exportDifferentAction()
    {
        $fileName   = 'stocktaking_different_qty_products.csv';
        $content    = $this->getLayout()
            ->createBlock('inventorysuccess/adminhtml_stocktaking_edit_tab_export')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * export invalid products after importing to CSV file
     */
    public function downloadInvalidCsvAction()
    {
        $fileName = Magestore_Inventorysuccess_Model_ImportType::INVALID_STOCKTAKING;
        $fileDir = Mage::getBaseDir('media'). DS . 'inventorysuccess'. DS. $fileName;
        $content = array(
            'type'  => 'filename',
            'value' => $fileDir,
            'rm'    => false
        );
        $this->_prepareDownloadResponse($fileName, $content);
    }
    
    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        $resource = 'inventorysuccess/stockcontrol/create_stocktaking';
        return Mage::getSingleton('admin/session')->isAllowed($resource);
    }    
}