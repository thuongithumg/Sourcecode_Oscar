<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Barcodesuccess Adminhtml Controller
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Adminhtml_Barcodesuccess_ImportController extends
    Mage_Adminhtml_Controller_Action
{

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/barcodesuccess/import');
    }


    public function importProductSampleAction()
    {
        $fileName = 'import_barcode.csv';
        $content  = $this->getLayout()
                         ->createBlock('barcodesuccess/adminhtml_barcode_sample')
                         ->setForProduct(true)
                         ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

}
