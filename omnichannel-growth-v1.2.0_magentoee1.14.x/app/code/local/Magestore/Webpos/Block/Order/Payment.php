<?php
class Magestore_Webpos_Block_Order_Payment extends Mage_Adminhtml_Block_Template
{

    protected $_order = null;

    public function getOrder()
    {
        return $this->_order;
    }

    public function setOrder($order)
    {
        $this->_order = $order;
        return $this;
    }

    protected function _beforeToHtml()
    {
        $this->setPayment($this->getOrder()->getPayment());
        parent::_beforeToHtml();
    }

    public function setPayment($payment)
    {
        $paymentInfoBlock = Mage::helper('payment')->getInfoBlock($payment);
        $this->setChild('info', $paymentInfoBlock);
        $this->setData('payment', $payment);
        return $this;
    }

    protected function _toHtml()
    {
        return $this->getChildHtml('info');
    }

}
