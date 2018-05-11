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
 * @package     Magestore_Webpospaypal
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

class Magestore_Webpospaypal_ConfigController extends Mage_Core_Controller_Front_Action
{
    /**
     * Store authorization code
     *
     * @return string
     */
    public function paypalsigninAction()
    {
        $authCode = $this->getRequest()->getParam('code');
        $tokenInfo = '';
        if($authCode) {
            try {
                $tokenInfo = Mage::getModel('webpospaypal/webpospaypal')->getTokenInfo($authCode);
            } catch (Exception $ex) {
                $this->getResponse()->setBody($ex->getMessage());
            }
            if($tokenInfo) {
                $accessToken = $tokenInfo->access_token;
                $refreshToken = $tokenInfo->refresh_token;
                if ($accessToken) {
                    Mage::getConfig()->saveConfig(
                        'webpos/payment/paypal_access_token',
                        $accessToken
                    );
                }
                if ($refreshToken) {
                    Mage::getConfig()->saveConfig(
                        'webpos/payment/paypal_refresh_token',
                        $refreshToken
                    );
                }
                Mage::app()->getCacheInstance()->cleanType('config');
                $html = $this->getClosePopup();
                $this->getResponse()->setBody($html);
            } else {
                $this->getResponse()->setBody(Mage::helper('webpos')->__('Please try again'));
            }
        } else {
            $this->getResponse()->setBody(Mage::helper('webpos')->__('Please try again'));
        }
    }

    /**
     * get closed popup html
     */
    public function getClosePopup()
    {
        $html = "<div id='my-timer'>".
            Mage::helper('webpos')->__('Successfully, please save your magento config. Window will close in %s seconds', '<b id=\'show-time\'>5</b>')
            ."</div>
                <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js'></script>
                <script type='text/javascript'> 
                    jQuery(function(){
                            window.setInterval(function() {
                                var timeCounter = jQuery('b[id=show-time]').html();
                                var updateTime = eval(timeCounter)- eval(1);
                                $('b[id=show-time]').html(updateTime);
                                if(updateTime == 0){
                                   window.close(); 
                                }
                            }, 1000);

                    });
            	</script>";
        return $html;
    }

}