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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Helper_Email
 */
class Magestore_Storepickup_Helper_Email extends Mage_Core_Helper_Abstract {
    const XML_PATH_ADMIN_EMAIL_IDENTITY = "trans_email/ident_general";
    const XML_PATH_SALES_EMAIL_IDENTITY = "trans_email/ident_sales";
    const XML_PATH_NEW_ORDER_TO_ADMIN_EMAIL = 'carriers/storepickup/shopadmin_email_template';
    const XML_PATH_NEW_ORDER_TO_STORE_OWNER_EMAIL = 'carriers/storepickup/storeowner_email_template';
    const XML_PATH_SEND_EMAIL_CUSTOMER_TO_ADMIN = 'carriers/storepickup/storeowner_email_customer';
    const XML_PATH_STATUS_ORDER_TO_STORE_OWNER_EMAIL = 'carriers/storepickup/storeowner_email_change_status';
    const TEMPLATE_ID_NONE_EMAIL = 'none_email';

    /**
     * @param $order
     * @return $this|void
     */
    public function sendNoticeEmailToAdmin($order) {
        $store = $order->getStore();

        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                                               ->setIsSecureMode(true);

        $paymentBlock->getMethod()->setStore($store->getId());

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_TO_ADMIN_EMAIL, $store->getId());
        if ($template === self::TEMPLATE_ID_NONE_EMAIL) {
            return $this;
        }
        $sendTo = array(
            Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL_IDENTITY, $store->getId()),
        );
        $mailTemplate = Mage::getModel('core/email_template');

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()))
                         ->sendTransactional(
                             $template,
                             Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $store->getId()),
                             $recipient['email'],
                             $recipient['name'],
                             array(
                                 'order' => $order->setAdminName($recipient['name']),
                                 'billing' => $order->getBillingAddress(),
                                 'payment_html' => $paymentBlock->toHtml(),
                                 //'pickup_time'   => Mage::helper('core')->formatDate($order->getPickupTime(),'medium',false),
                             )
                         );
        }

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * @param $id_message
     */
    public function sendEmailtoAdmin($id_message) {
        $store_id = Mage::app()->getStore()->getId();
        $template_id = Mage::getStoreConfig(self::XML_PATH_SEND_EMAIL_CUSTOMER_TO_ADMIN);
        if ($template_id === self::TEMPLATE_ID_NONE_EMAIL) {
            return;
        }
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $information_sender = Mage::getModel('storepickup/message')->load($id_message);
        $email_sender = $information_sender->getEmail();
        $name_sender = $information_sender->getName();
        $mailSubject = "Email from Customer";
        $sender = array(
            'name' => $name_sender,
            'email' => $email_sender,
        );
        $message = $information_sender->getMessage();
        $sendTo = array(
            Mage::getStoreConfig(self::XML_PATH_ADMIN_EMAIL_IDENTITY, $store_id),
        );
        foreach ($sendTo as $item) {
            $email_contact = $item['email'];
            $name_contact = $item['name'];

            $vars = Array(
                'message' => $message,
                'email_sender' => $email_sender,
                'name_sender' => $name_sender,
                'name_contact' => $name_contact,
            );
            Mage::getModel('core/email_template')
                ->setTemplateSubject($mailSubject)
                ->sendTransactional($template_id, $sender, $email_contact, $name_contact, $vars, $store_id);
            $translate->setTranslateInline(true);
        }
    }

    /**
     * @param $order
     * @return $this|void
     */
    public function sendNoticeEmailToStoreOwner($order) {
        $order_id = $order->getId();
        $storeLocation = Mage::helper('storepickup')->getStorepickupByOrderId($order_id);
        if (!$storeLocation) {
            return $this;
        }

        $store = $order->getStore();

        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                                               ->setIsSecureMode(true);

        $paymentBlock->getMethod()->setStore($store->getId());

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getStoreConfig(self::XML_PATH_NEW_ORDER_TO_STORE_OWNER_EMAIL, $store->getId());
        if ($template === self::TEMPLATE_ID_NONE_EMAIL) {
            return $this;
        }
        $sendTo = array(
            array(
                'name' => $storeLocation->getStoreManager(),
                'email' => $storeLocation->getStoreEmail(),
            ),
        );
        $mailTemplate = Mage::getModel('core/email_template');

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()))
                         ->sendTransactional(
                             $template,
                             Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $store->getId()),
                             $recipient['email'],
                             $recipient['name'],
                             array(
                                 'order' => $order->setStoreOwnerName($recipient['name']),
                                 'billing' => $order->getBillingAddress(),
                                 'payment_html' => $paymentBlock->toHtml(),
                             )
                         );
        }

        $translate->setTranslateInline(true);

        return $this;
    }

    /**
     * @param $id_message
     * @param $id_store
     */
    public function sendEmailtoStoreOwner($id_message, $id_store) {
        $store_id = Mage::app()->getStore()->getId();
        $template_id = Mage::getStoreConfig(self::XML_PATH_SEND_EMAIL_CUSTOMER_TO_ADMIN);
        if ($template_id === self::TEMPLATE_ID_NONE_EMAIL) {
            return;
        }
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $information_sender = Mage::getModel('storepickup/message')->load($id_message);
        $email_sender = $information_sender->getEmail();
        $name_sender = $information_sender->getName();
        $mailSubject = "Email from Customer";
        $sender = array(
            'name' => $name_sender,
            'email' => $email_sender,
        );
        $message = $information_sender->getMessage();
        $imforSoter = Mage::getModel('storepickup/store')->load($id_store);
        $email_contact = $imforSoter->getStoreEmail();
        $name_contact = $imforSoter->getStoreName();
        $vars = Array(
            'message' => $message,
            'email_sender' => $email_sender,
            'name_sender' => $name_sender,
            'name_contact' => $name_contact,
        );
        Mage::getModel('core/email_template')
            ->setTemplateSubject($mailSubject)
            ->sendTransactional($template_id, $sender, $email_contact, $name_contact, $vars, $store_id);
        $translate->setTranslateInline(true);
    }

    /**
     * @param $order
     * @return $this|void
     */
    public function sendStautsEmailToStoreOwner($order) {
        $order_id = $order->getId();
        $storeLocation = Mage::helper('storepickup')->getStorepickupByOrderId($order_id);
        if (!$storeLocation) {
            return $this;
        }

        $store = $order->getStore();

        $paymentBlock = Mage::helper('payment')->getInfoBlock($order->getPayment())
                                               ->setIsSecureMode(true);

        $paymentBlock->getMethod()->setStore($store->getId());

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);

        $template = Mage::getStoreConfig(self::XML_PATH_STATUS_ORDER_TO_STORE_OWNER_EMAIL, $store->getId());
        if ($template === self::TEMPLATE_ID_NONE_EMAIL) {
            return $this;
        }
        $sendTo = array(
            array(
                'name' => $storeLocation->getStoreManager(),
                'email' => $storeLocation->getStoreEmail(),
            ),
        );

        $mailTemplate = Mage::getModel('core/email_template');

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()))
                         ->sendTransactional(
                             $template, Mage::getStoreConfig(self::XML_PATH_SALES_EMAIL_IDENTITY, $store->getId()), $recipient['email'], $recipient['name'], array(
                                 'order' => $order->setStoreOwnerName($recipient['name']),
                                 'billing' => $order->getBillingAddress(),
                                 'payment_html' => $paymentBlock->toHtml(),
                                 // 'status'        => $order->getStatus(),
                             )
                         );
        }

        $translate->setTranslateInline(true);

        return $this;
    }
}
