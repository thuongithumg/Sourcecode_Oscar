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
 * @package     Magestore_Giftvoucher
 * @module     Giftvoucher
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Giftvoucher Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */
class Magestore_Giftvoucher_Model_Giftvoucher extends Mage_Rule_Model_Rule
{

    const STATUS_YES = 1;
    const STATUS_NO = 2;
    const TYPE_NORMAL = 'normal';
    const TYPE_FOLD_PAPER = 'fold';
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('giftvoucher/giftvoucher');
        $this->setIdFieldName('giftvoucher_id');
    }

    /**
     * Load Gift Card by gift code
     *
     * @param string $code
     * @param null $storeId
     * @return Magestore_Giftvoucher_Model_Giftvoucher
     */
    public function loadByCode($code, $storeId = null)
    {
        $storeId = ($storeId == null ? Mage::app()->getStore()->getId() : $storeId);
        return $this->getCollection()->addFieldToFilter('gift_code', $code)
            ->addFieldToFilter('store_id',
                array(
                    array('finset'=> array(0)),
                    array('finset'=> array($storeId)),
                )
            )
            ->getFirstItem();
    }

    /**
     * @param int $id
     * @param null $field
     * @return $this|false|Mage_Core_Model_Abstract
     */
    public function load($id, $field = null)
    {
        parent::load($id, $field);
        $timeSite = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
        if ($this->getIsDeleted()) {
            return Mage::getModel('giftvoucher/giftvoucher');
        }

        if ($this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE 
            && $this->getExpiredAt() && $this->getExpiredAt() < $timeSite) {
            $this->setStatus(Magestore_Giftvoucher_Model_Status::STATUS_EXPIRED);
        }
        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsDeleted()
    {
        if (!$this->hasData('is_deleted')) {
            $this->setData('is_deleted', $this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_DELETED);
        }
        return $this->getData('is_deleted');
    }

    /**
     * @return mixed
     */
    public function getCollection()
    {
        return parent::getCollection()->getAvailable();
    }

    /**
     * Get the base balance of gift code
     *
     * @param string $storeId
     * @return float
     */
    public function getBaseBalance($storeId = null)
    {
        if (!$this->hasData('base_balance')) {
            $baseBalance = 0;
            if ($this->getData('currency')) {
                $rate = Mage::app()->getStore($storeId)->getBaseCurrency()
                        ->getRate($this->getData('currency'));
            }
            if (isset($rate)) {
                $baseBalance = $this->getBalance() / $rate;
            }
            $this->setData('base_balance', $baseBalance);
        }
        return $this->getData('base_balance');
    }

    /**
     * @return Mage_Rule_Model_Abstract
     * @throws Mage_Core_Exception
     */
    protected function _beforeSave()
    {
        $timeSite = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
        if (strtolower(Mage::app()->getRequest()->getActionName()) == 'processimport') {
            $expiredDateTime = date('Y-m-d H:i:s', strtotime($this->getExpiredAt()));
        } else {
            $expiredDateTime = $this->getExpiredAt();
        }

        if (!$this->getId()) {
            $this->setAction(Magestore_Giftvoucher_Model_Actions::ACTIONS_CREATE);
        }

        if ($this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_USED 
            && Mage::app()->getStore()->roundPrice($this->getBalance()) > 0) {
            $this->setStatus(Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE);
        }

        if ($this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE 
            && Mage::app()->getStore()->roundPrice($this->getBalance()) == 0) {
            $this->setStatus(Magestore_Giftvoucher_Model_Status::STATUS_USED);
        }
        

        if ($this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE
            && $this->getExpiredAt() && $expiredDateTime < $timeSite) {
            $this->setStatus(Magestore_Giftvoucher_Model_Status::STATUS_EXPIRED);
        }


        if ($this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_EXPIRED 
            && $this->getExpiredAt() && $expiredDateTime > now()) {
            $this->setExpiredAt(now());
        }

        if (!$this->getGiftCode()) {
            $this->setGiftCode(Mage::helper('giftvoucher')->getGeneralConfig('pattern'));
        }
        if ($this->_codeIsExpression()) {
            $this->setGiftCode($this->_getGiftCode());
        }

        $session = Mage::getSingleton('adminhtml/session_quote');

        if (!Mage::registry('giftvoucher_conditions')) {
            Mage::register('giftvoucher_conditions', true);
        } else {
            $data = $this->getData();
            $order = $session->getOrder();
            if ($order && $order->getId() == null) {
                if (isset($data['conditions_serialized'])) {
                    unset($data['conditions_serialized']);
                }
                if (isset($data['actions_serialized'])) {
                    unset($data['actions_serialized']);
                }
            }
            $this->setData($data);            
        }
        
        Mage::helper('giftvoucher')->createBarcode($this->getGiftCode());

        return parent::_beforeSave();
    }

    /**
     * @return mixed
     */
    protected function _codeIsExpression()
    {
        return Mage::helper('giftvoucher')->isExpression($this->getGiftCode());
    }

    /**
     * @return mixed
     * @throws Mage_Core_Exception
     */
    protected function _getGiftCode()
    {
        $code = Mage::helper('giftvoucher')->calcCode($this->getGiftCode());
        $times = 10;
        while (Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code)->getId() && $times) {
            $code = Mage::helper('giftvoucher')->calcCode($this->getGiftCode());
            $times--;
            if ($times == 0) {
                throw new Mage_Core_Exception('Exceeded maximum retries to find available random gift card code!');
            }
        }

        return $code;
    }

    /**
     * @return Mage_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        if ($this->getIncludeHistory() && $this->getAction()) {
            $history = Mage::getModel('giftvoucher/history')
                ->setData($this->getData())
                ->setData('created_at', now());
            if ($this->getAction() == Magestore_Giftvoucher_Model_Actions::ACTIONS_UPDATE 
                || $this->getAction() == Magestore_Giftvoucher_Model_Actions::ACTIONS_MASS_UPDATE
            ) {
                $history->setData('customer_id', null)
                    ->setData('customer_email', null)
                    ->setData('amount', $this->getBalance());
            }

            try {
                $history->save();
            } catch (Exception $e) {
                
            }
        }

//        if (!Mage::registry('draw_gift_card' . $this->getGiftCode()) && !$this->getMassEmail()) {
//            Mage::helper('giftvoucher/drawgiftcard')->draw($this);
//            Mage::register('draw_gift_card' . $this->getGiftCode(), 1);
//        }

        return parent::_afterSave();
    }

//    public function delete()
//    {
//        $this->setStatus(Magestore_Giftvoucher_Model_Status::STATUS_DELETED)
//            ->save();
//        return $this;
//    }

    /**
     * @return mixed
     */
    public function getFormatedMessage()
    {
        return str_replace("\n", "<br/>", $this->getMessage());
    }

    /**
     * @param null $session
     * @return $this
     */
    public function addToSession($session = null)
    {
        if (is_null($session)) {
            $session = Mage::getSingleton('checkout/session');
        }
        if ($codes = $session->getGiftCodes()) {
            $codesArray = explode(',', $codes);
            $codesArray[] = $this->getGiftCode();
            $codes = implode(',', array_unique($codesArray));
        } else {
            $codes = $this->getGiftCode();
        }
        $session->setGiftCodes($codes);
        return $this;
    }

    /**
     * @return $this
     */
    public function sendEmail()
    {
        $store = Mage::app()->getStore($this->getStoreId());
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $mailSent = 0;
        if ($this->getCustomerEmail()) {
            $mailTemplate = Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $store->getStoreId()
            ));

            $gifttemplate = Mage::getModel('giftvoucher/gifttemplate')->load($this->getData('giftcard_template_id'));

            $giftcart_template_email = Mage::helper('giftvoucher')->getEmailConfig('self', $store->getStoreId());
//            if($gifttemplate->getDesignPattern() == Magestore_Giftvoucher_Model_Designpattern::PATTERN_AMAZON){
//                $giftcart_template_email = 'giftvoucher_email_self_amazon';
//            }

            $text_color = '#DC8C71';
            $style_color = '#949392';

            $filename = $this->getData('giftcard_template_image');
            $urlImage = '/giftvoucher/template/images/amazon/' . $filename;
            $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$urlImage;

            $mailTemplate->sendTransactional(
                $giftcart_template_email,
                Mage::helper('giftvoucher')->getEmailConfig('sender', $store->getStoreId()),
                $this->getCustomerEmail(), $this->getCustomerName(), array(
                    'store' => $store,
                    'sendername' => $this->getCustomerName(),
                    'receivename' => $this->getRecipientName(),
                    'code' => $this->getGiftCode(),
                    'balance' => $this->getBalanceFormated(),
                    'status' => $this->getStatusLabel(),
                    'noactive' => ($this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE) ? 0 : 1,
                    'expiredat' => $this->getExpiredAt() ? Mage::getModel('core/date')->date('M d, Y', $this->getExpiredAt()) : '',
                    'message' => $this->getFormatedMessage(),
                    'note' => $this->getEmailNotes(),
                    'description' => $this->getDescription(),
                    'logo' => $this->getPrintLogo(),
                    'url' => $this->getPrintTemplate(),
                    'secure_key' => base64_encode($this->getGiftCode() . '$' . $this->getId()),
                    'text_color' => ($gifttemplate->getTextColor() != '' ? $gifttemplate->getTextColor() : $text_color),
                    'style_color' => ($gifttemplate->getStyleColor() != '' ? $gifttemplate->getStyleColor() : $style_color),
                    'barcode_url' => $this->getPrintBarcode(),
                    'image_url' => $imageUrl,
                    'addurl' => Mage::getUrl('giftvoucher/index/addlist', array('giftvouchercode' => $this->getGiftCode())),
                )
            );

            $mailSent++;
        }

        if ($this->getRecipientEmail()) {
            $mailSent += $this->sendEmailToRecipient();
        }

        if ($this->getRecipientEmail() || $this->getCustomerEmail()) {
            try {
                // changed by Adam
                $giftvoucher = Mage::getModel('giftvoucher/giftvoucher')->load($this->getId());
                if ($this->getData('recipient_address')) {
                    $giftvoucher->setIsSent(2);
                } else {
                    $giftvoucher->setIsSent(true);
                }
                if (!$this->getNotResave()) {
                    $giftvoucher->save();
                }
            } catch (Exception $ex) {

            }
        }

        $this->setEmailSent($mailSent);
        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * Send email to friend
     * 
     * @return Magestore_Giftvoucher_Model_Giftvoucher
     */
    public function sendEmailToFriend()
    {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        $this->sendEmailToRecipient();
        $translate->setTranslateInline(true);
        return $this;
    }

    /**
     * Send email to Gift Voucher Receipient
     * 
     * @return int The number of email sent
     */
    public function sendEmailToRecipient()
    {
        $allowStatus = explode(',', Mage::helper('giftvoucher')->getEmailConfig('only_complete', $this->getStoreId()));
        if (!is_array($allowStatus)) {
            $allowStatus = array();
        }
        if ($this->getSetId() > 0 && $this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_PENDING || $this->getIsSent()) {  // changed by Adam
            return $this;
        }
        if ($this->getRecipientEmail() && !$this->getData('dont_send_email_to_recipient') 
            && in_array($this->getStatus(), $allowStatus)
        ) {
            $text_color = '#DC8C71';
            $style_color = '#949392';

            $store = Mage::app()->getStore($this->getStoreId());
            $mailTemplate = Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $store->getStoreId()
            ));

            $filename = $this->getData('giftcard_template_image');
            $urlImage = '/giftvoucher/template/images/amazon/' . $filename;
            $imageUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).$urlImage;

            $giftcart_template_email = Mage::helper('giftvoucher')->getEmailConfig('template', $store->getStoreId());
            $gifttemplate = Mage::getModel('giftvoucher/gifttemplate')->load($this->getData('giftcard_template_id'));
            // Comment by adam to solve the problem of email template.
            /*if($gifttemplate->getDesignPattern() == Magestore_Giftvoucher_Model_Designpattern::PATTERN_AMAZON){
                $giftcart_template_email = 'giftvoucher_email_template_amazon';
            }*/

            $mailTemplate->sendTransactional(
                $giftcart_template_email,
                Mage::helper('giftvoucher')->getEmailConfig('sender', $store->getStoreId()),
                $this->getRecipientEmail(),
                $this->getRecipientName(),
                array(
                    'text_color' => ($gifttemplate->getTextColor() != '' ? $gifttemplate->getTextColor() : $text_color),
                    'style_color' => ($gifttemplate->getStyleColor() != '' ? $gifttemplate->getStyleColor() : $style_color),
                    'store' => $store,
                    'sendername' => $this->getCustomerName(),
                    'receivename' => $this->getRecipientName(),
                    'code' => $this->getGiftCode(),
                    'balance' => $this->getBalanceFormated(),
                    'status' => $this->getStatusLabel(),
                    'noactive' => ($this->getStatus() == Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE) ? 0 : 1,
                    'expiredat' => $this->getExpiredAt() ? Mage::getModel('core/date')->date('M d, Y', $this->getExpiredAt()) : '',
                    'message' => $this->getFormatedMessage(),
                    'note' => $this->getEmailNotes(),
                    'description' => $this->getDescription(),
                    'logo' => $this->getPrintLogo(),
                    'url' => $this->getPrintTemplate(),
                    'barcode_url' => $this->getPrintBarcode(),
                    'image_url' => $imageUrl,
                    'addurl' => Mage::getUrl('giftvoucher/index/addlist', array('giftvouchercode' => $this->getGiftCode())),
                    'secure_key' => base64_encode($this->getGiftCode() . '$' . $this->getId())
                )
            );

            try {
                // changed by Adam
                $giftvoucher = Mage::getModel('giftvoucher/giftvoucher')->load($this->getId());
                if (!$this->getData('recipient_address')) {
                    $giftvoucher->setIsSent(true);
                } else {
                    $giftvoucher->setIsSent(2);
                }                
                if (!$this->getNotResave()) {
                    $giftvoucher->save();
                }
            } catch (Exception $ex) {
                
            }
            return 1;
        }
        return 0;
    }

    /**
     * Send the refund notification email
     * 
     * @return Magestore_Giftvoucher_Model_Giftvoucher
     */
    public function sendEmailRefundToRecipient()
    {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        if ($this->getRecipientEmail() && !$this->getData('dont_send_email_to_recipient')) {
            $store = Mage::app()->getStore($this->getStoreId());
            $mailTemplate = Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $store->getStoreId()
            ));
            $mailTemplate->sendTransactional(
                Mage::helper('giftvoucher')->getEmailConfig('template_refund', $store->getStoreId()), 
                    Mage::helper('giftvoucher')->getEmailConfig('sender', $store->getStoreId()), 
                        $this->getRecipientEmail(), $this->getRecipientName(), array(
                            'store' => $store,
                            'sendername' => $this->getCustomerName(),
                            'receivename' => $this->getRecipientName(),
                            'code' => $this->getGiftCode(),
                            'balance' => $this->getBalanceFormated(),
                            'status' => $this->getStatusLabel(),
                            'message' => $this->getFormatedMessage(),
                            'description' => $this->getDescription(),
                            'addurl' => Mage::getUrl('giftvoucher/index/addlist', array(

                                'giftvouchercode' => $this->getGiftCode()
                            )),
                )
            );
        }
        $translate->setTranslateInline(false);
        return $this;
    }

    /**
     * Send the success notification email
     * 
     * @return Magestore_Giftvoucher_Model_Giftvoucher
     */
    public function sendEmailSuccess()
    {
        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        if ($this->getCustomerEmail()) {
            $store = Mage::app()->getStore($this->getStoreId());
            $mailTemplate = Mage::getModel('core/email_template')
                ->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $store->getStoreId()
            ));
            $mailTemplate->sendTransactional(
                Mage::helper('giftvoucher')->getEmailConfig('template_success', $store->getStoreId()), 
                    Mage::helper('giftvoucher')->getEmailConfig('sender', $store->getStoreId()), 
                        $this->getCustomerEmail(), $this->getCustomerName(), array(
                            'receivename' => $this->getRecipientName(),

                )
            );
        }
        $translate->setTranslateInline(false);
        return $this;
    }

    /**
     * Get the print notes
     * 
     * @return string
     */
    public function getPrintNotes()
    {
        if (!$this->hasData('print_notes')) {
            $notes = Mage::getStoreConfig('giftvoucher/print_voucher/note', $this->getStoreId());
            $notes = str_replace(array(
                '{store_url}',
                '{store_name}',
                '{store_address}'

                ), array(
                '<span class="print-notes">' . Mage::app()->getStore($this->getStoreId())->getBaseUrl() . '</span>',



                '<span class="print-notes">' . Mage::app()->getStore($this->getStoreId())->getFrontendName() . 
                    '</span>',
                '<span class="print-notes">' . Mage::getStoreConfig('general/store_information/address', 
                    $this->getStoreId()) . '</span>'
                ), $notes);
            $this->setData('print_notes', $notes);
        }
        return $this->getData('print_notes');
    }

    /**
     * Get the email notes
     * 
     * @return string
     */
    public function getEmailNotes()
    {
        if (!$this->hasData('email_notes')) {
            $notes = Mage::getStoreConfig('giftvoucher/email/note', $this->getStoreId());
            $notes = str_replace(array(
                '{store_url}',
                '{store_name}',
                '{store_address}'

                ), array(
                Mage::app()->getStore($this->getStoreId())->getBaseUrl(),
                Mage::app()->getStore($this->getStoreId())->getFrontendName(),
                Mage::getStoreConfig('general/store_information/address', $this->getStoreId())

                ), $notes);
            $this->setData('email_notes', $notes);
        }
        return $this->getData('email_notes');
    }

    /**
     * Get the print logo
     * 
     * @return string|boolean
     */
    public function getPrintLogo()
    {
        $image = Mage::getStoreConfig('giftvoucher/print_voucher/logo', $this->getStoreId());
        if ($image) {
            $image = Mage::app()->getStore($this->getStoreId())->getBaseUrl('media') . 'giftvoucher/pdf/logo/' . $image;
            return $image;
        }
        return false;
    }

    /**
     * Get the print template image
     * 
     * @return string
     */
    public function getPrintTemplate()
    {
        $images = Mage::helper('giftvoucher/drawgiftcard')->getImagesInFolder($this->getGiftCode());

        if (isset($images[0]) && file_exists($images[0])) {
            $search = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . $this->getGiftCode() . DS;
            $replace = Mage::getBaseUrl('media') . 'giftvoucher/draw/' . $this->getGiftCode() . '/';
            $result = str_replace($search, $replace, $images[0]);

            return $result;
        }
        return '';
    }

    /**
     * Get the print template image
     *
     * @return string
     */
    public function getPrintBarcode()
    {
        $images = Mage::helper('giftvoucher/drawgiftcard')->getBarcodeInFolder($this->getGiftCode());

        if (isset($images[0]) && file_exists($images[0])) {
            $search = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . $this->getGiftCode() . DS;
            $replace = Mage::getBaseUrl('media') . 'giftvoucher/draw/' . $this->getGiftCode() . '/';
            $result = str_replace($search, $replace, $images[0]);
            return $result;
        }
        return '';
    }

    /**
     * Returns the formatted balance
     * 
     * @return string
     */
    public function getBalanceFormated()
    {
        $currency = Mage::getModel('directory/currency')->load($this->getCurrency());
        return $currency->format($this->getBalance());
    }

    /**
     * @return mixed
     */
    public function getStatusLabel()
    {
        $statusArray = Mage::getSingleton('giftvoucher/status')->getOptionArray();
        return $statusArray[$this->getStatus()];
    }

    /**
     * Get the list customer that used this code
     * 
     * @return array
     */
    public function getCustomerIdsUsed()
    {
        $collection = Mage::getResourceModel('giftvoucher/history_collection')
            ->addFieldToFilter('main_table.giftvoucher_id', $this->getId())
            ->addFieldToFilter('main_table.action', Magestore_Giftvoucher_Model_Actions::ACTIONS_SPEND_ORDER);
        $collection->getSelect()
            ->joinLeft(array('o' => $collection->getTable('sales/order')), 
                'main_table.order_increment_id = o.increment_id', array('order_customer_id' => 'customer_id')
            )->group('o.customer_id');
        $customerIds = array();
        foreach ($collection as $item) {
            $customerIds[] = $item->getData('order_customer_id');
        }
        return $customerIds;
    }



    /**
     * Add Magento Sales Rule for Gift Card Model
     * 
     */
    public function getConditionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_combine');
    }

    /**
     * @return false|Mage_Core_Model_Abstract
     */
    public function getActionsInstance()
    {
        return Mage::getModel('salesrule/rule_condition_product_combine');
    }

    /**
     * @param array $rule
     * @return $this
     */
    public function loadPost(array $rule)
    {
        $arr = $this->_convertFlatToRecursive($rule);
        if (isset($arr['conditions'])) {
            $this->getConditions()->setConditions(array())->loadArray($arr['conditions'][1]);
        }
        if (isset($arr['actions'])) {
            $this->getActions()->setActions(array())->loadArray($arr['actions'][1], 'actions');
        }
        return $this;
    }

    /**
     * Fix error when load and save with multiple Gift Card for Core Magento
     * 
     * @return Magestore_Giftvoucher_Model_Giftvoucher
     */
    protected function _afterLoad()
    {
        $this->setConditions(null);
        $this->setActions(null);
        return parent::_afterLoad();
    }

    /**
     * @return array
     */
    static public function getPrintType()
    {
        return array(
            self::TYPE_NORMAL => Mage::helper('giftvoucher')->__('Normal Print'),
            self::TYPE_FOLD_PAPER => Mage::helper('giftvoucher')->__('Fold Print')
        );
    }

}
