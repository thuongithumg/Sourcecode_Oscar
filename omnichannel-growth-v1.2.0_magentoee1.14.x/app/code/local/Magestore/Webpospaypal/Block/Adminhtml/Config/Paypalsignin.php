<?php

class Magestore_Webpospaypal_Block_Adminhtml_Config_Paypalsignin extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('webpospaypal/config/paypalsignin.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for collect button
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return Mage::getUrl('webpospaypal/config/paypalsignin', array('_nosid'=>true, '_secure'=>true));
    }

    /**
     * Return client Id
     *
     * @return string
     */
    public function getClientId()
    {
        $paypalConfig = Mage::helper('webpospaypal');
        $clientId = $paypalConfig->getPaypalConfig('client_id') ? $paypalConfig->getPaypalConfig('client_id') : '';
        return $clientId;
    }

    /**
     * Get Paypal login url
     *
     * @return string
     */
    public function getPaypalLoginUrl()
    {
        $paypalConfig = Mage::helper('webpospaypal');
        $isSandBox = $paypalConfig->getPaypalConfig('is_sandbox');
        if($isSandBox) {
            $url = 'https://www.sandbox.paypal.com/signin/authorize';
        } else {
            $url = 'https://www.paypal.com/signin/authorize';
        }
        return $url;
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'paypal_sign',
                'label' => $this->helper('adminhtml')->__('Sign in'),
                'onclick' => 'javascript:paypalLogin(); return false;'
            ));

        return $button->toHtml();
    }
}
