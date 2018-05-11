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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Purchaseorder_Pdf_Footer
    extends Magestore_Purchaseordersuccess_Block_Purchaseorder_Pdf_Abstract
{
    /**
     * @var Mage_Directory_Model_Currency
     */
    protected $currency;

    protected $_template = 'purchaseordersuccess/purchaseorder/pdf/footer.phtml';

    public function getPurchaseOrderComment(){
        return $this->purchaseOrder->getComment();
    }

    public function getPriceFormat($code)
    {
        if (!$this->currency)
            $this->currency = Mage::getModel('directory/currency')->load($this->purchaseOrder->getCurrencyCode());
        return $this->currency->formatTxt($this->getPrice($code));
    }

    public function getPrice($code){
        return $this->purchaseOrder->getData($code);
    }
}