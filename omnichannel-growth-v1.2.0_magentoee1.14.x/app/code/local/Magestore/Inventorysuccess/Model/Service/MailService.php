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
 * Inventorysuccess Model
 * 
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Model_Service_MailService
{
    const XML_PATH_GENERAL_NAME = 'trans_email/ident_general/name';
    const XML_PATH_GENERAL_EMAIL = 'trans_email/ident_general/email';
    const XML_PATH_LOWSTOCK_NOTIFICATION_EMAIL_TEMPLATE = 'lowstocknotification/notification/send_email_notification';

    /**
     * @param $productSystem
     * @param $productWarehouse
     * @param $notifierEmails
     */
    public function sendEmailNotification($productSystem, $productWarehouse, $notifierEmails)
    {
        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $notifierEmails = explode(',', $notifierEmails);
        $sender = array(
            'name' => Mage::getStoreConfig(self::XML_PATH_GENERAL_NAME),
            'email' => Mage::getStoreConfig(self::XML_PATH_GENERAL_EMAIL)
        );
        foreach ($notifierEmails as $email) {
            try {
                $emailInfo = Mage::getModel('core/email_info');
                $emailInfo->addTo($email, Mage::helper('inventorysuccess')->__('Store Owner'));
                $mailer->addEmailInfo($emailInfo);
                // Set all required params and send emails
                $mailer->setSender($sender);
                $mailer->setTemplateId(Mage::getStoreConfig(self::XML_PATH_LOWSTOCK_NOTIFICATION_EMAIL_TEMPLATE));
                $mailer->setTemplateParams(
                    array(
                        'productSystem' => $productSystem,
                        'productWarehouse' => $productWarehouse,
                        'createdAt' => date('Y-m-d')
                    )
                );
                $mailer->send();
            } catch (\Exception $e) {
                return;
            }
        }
    }
}