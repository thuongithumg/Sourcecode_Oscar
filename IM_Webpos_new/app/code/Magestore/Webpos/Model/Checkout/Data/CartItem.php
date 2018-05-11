<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Checkout\Data;

use Magestore\Webpos\Api\Data\Checkout\DataObjectInterface;

/**
 * 
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartItem extends \Magento\Framework\Model\AbstractExtensibleModel implements \Magestore\Webpos\Api\Data\Checkout\CartItemInterface
{
    /**
     *
     * @var \Magestore\Webpos\Helper\Data 
     */
    protected $_helper;
    
    /**
     *
     * @var \Magento\Framework\App\ObjectManager 
     */
    protected $_objectManager;
    
    /**#@-*/
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param AttributeValueFactory $customAttributeFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magestore\Webpos\Helper\Data $helperData,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $extensionFactory, $customAttributeFactory, $resource, $resourceCollection, $data);
        $this->_helper = $helperData;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getId(){
        if($this->getData(self::KEY_ID) == self::KEY_CUSTOM_SALE_ID){
            return $this->_helper->getCustomSaleProductId();
        }
        return $this->getData(self::KEY_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setId($id){
        if($id == self::KEY_CUSTOM_SALE_ID){
            $id = $this->_helper->getCustomSaleProductId();
        }
        return $this->setData(self::KEY_ID,$id);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getQty(){
        return $this->getData(self::KEY_QTY);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setQty($qty){
        return $this->setData(self::KEY_QTY,$qty);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCustomPrice(){
        return $this->getData(self::KEY_CUSTOM_PRICE);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCustomPrice($customPrice){
        return $this->setData(self::KEY_CUSTOM_PRICE,$customPrice);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setSuperAttribute($super_attribute){
        return $this->setData(self::KEY_SUPER_ATTRIBUTE,$super_attribute);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSuperAttribute(){
        $result = null;
        $data = $this->getData(self::KEY_SUPER_ATTRIBUTE);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    $result[$option->getCode()] = $option->getValue();
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setSuperGroup($super_group){
        return $this->setData(self::KEY_SUPER_GROUP,$super_group);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSuperGroup(){
        $result = null;
        $data = $this->getData(self::KEY_SUPER_GROUP);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    $result[$option->getCode()] = $option->getValue();
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setOptions($options){
        return $this->setData(self::KEY_CUSTOM_OPTION,$options);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getOptions(){
        $result = null;
        $data = $this->getData(self::KEY_CUSTOM_OPTION);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    if(isset($result[$option->getCode()])){
                        if (is_array($result[$option->getCode()])){
                            $result[$option->getCode()][] = $option->getValue();
                        }else{
                            $result[$option->getCode()] = [$result[$option->getCode()], $option->getValue()];
                        }
                    }else{
                        $result[$option->getCode()] = $option->getValue();
                    }
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBundleOption($bundle_option){
        return $this->setData(self::KEY_BUNDLE_OPTION,$bundle_option);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBundleOption(){
        $result = null;
        $data = $this->getData(self::KEY_BUNDLE_OPTION);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    if(isset($result[$option->getCode()])){
                        if (is_array($result[$option->getCode()])){
                            $result[$option->getCode()][] = $option->getValue();
                        }else{
                            $result[$option->getCode()] = [$result[$option->getCode()], $option->getValue()];
                        }
                    }else{
                        $result[$option->getCode()] = $option->getValue();
                    }
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setBundleOptionQty($bundle_option_qty){
        return $this->setData(self::KEY_BUNDLE_OPTION_QTY,$bundle_option_qty);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getBundleOptionQty(){
        $result = null;
        $data = $this->getData(self::KEY_BUNDLE_OPTION_QTY);
        if($data){
            foreach($data as $key => $option){
                if($option instanceof \Magestore\Webpos\Model\Checkout\Data\CartItemOption){
                    $result[$option->getCode()] = $option->getValue();
                }else{
                    $result[$key] = $option;
                }
            }
        }
        return $result;
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setIsCustomSale($isCustomSale){
        return $this->setData(self::KEY_IS_CUSTOM_SALE,$isCustomSale);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getIsCustomSale(){
        return $this->getData(self::KEY_IS_CUSTOM_SALE);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setQtyToShip($qtyToShip){
        return $this->setData(self::KEY_QTY_TO_SHIP,$qtyToShip);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getQtyToShip(){
        return $this->getData(self::KEY_QTY_TO_SHIP);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getItemData($key = false){
        if($key){
            return $this->_get($key);
        }else{
            $keys = [
                self::KEY_ID,self::KEY_QTY,self::KEY_CUSTOM_PRICE,self::KEY_SUPER_ATTRIBUTE,self::KEY_SUPER_GROUP,
                self::KEY_BUNDLE_OPTION,self::KEY_BUNDLE_OPTION_QTY,self::KEY_CUSTOM_OPTION,self::KEY_IS_CUSTOM_SALE,
                self::KEY_QTY_TO_SHIP,self::KEY_DISCOUNT_AMOUNT,self::KEY_BASE_DISCOUNT_AMOUNT,self::KEY_TAX_AMOUNT,
                self::KEY_BASE_TAX_AMOUNT
            ];
            $data = [];
            if(count($keys) > 0){
                foreach ($keys as $KEY){
                    $object = $this->_objectManager->create('Magestore\Webpos\Api\Data\Checkout\DataObjectInterface');
                    $object->setId($KEY);
                    $object->setValue($this->getData($KEY));
                    $data[] = $object;
                }
            }
            return $data;
        }
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setItemData($data){
        if(is_array($data)){
            $keys = [
                self::KEY_ID,self::KEY_QTY,self::KEY_CUSTOM_PRICE,self::KEY_SUPER_ATTRIBUTE,self::KEY_SUPER_GROUP,
                self::KEY_BUNDLE_OPTION,self::KEY_BUNDLE_OPTION_QTY,self::KEY_CUSTOM_OPTION,self::KEY_IS_CUSTOM_SALE,
                self::KEY_QTY_TO_SHIP,self::KEY_DISCOUNT_AMOUNT,self::KEY_BASE_DISCOUNT_AMOUNT,self::KEY_TAX_AMOUNT,
                self::KEY_BASE_TAX_AMOUNT
            ];
            if(count($keys) > 0){
                foreach ($keys as $KEY){
                    if(!isset($data[$KEY])){
                        continue;
                    }
                    $object = $this->_objectManager->create('Magestore\Webpos\Api\Data\Checkout\DataObjectInterface');
                    $object->setId($KEY);
                    $object->setValue($data[$KEY]);
                    $this->setData($KEY, $object);
                }
            }
            return $this;
        }
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getExtensionData(){
        return $this->getData(self::KEY_EXTENSION_DATA);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setExtensionData($extensionData){
        return $this->setData(self::KEY_EXTENSION_DATA, $extensionData);
    }
    
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCustomSaleTaxClassId(){
        return $this->getData(self::CUSTOM_SALE_TAX_CLASS_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCustomSaleTaxClassId($taxClassId){
        return $this->setData(self::CUSTOM_SALE_TAX_CLASS_ID, $taxClassId);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCustomSaleDescription(){
        return $this->getData(self::CUSTOM_SALE_DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCustomSaleDescription($customSaleDescription){
        return $this->setData(self::CUSTOM_SALE_DESCRIPTION, $customSaleDescription);
    }
    
    /**
     * get item extension data.
     *
     * @return string
     */
    public function getAmount(){
        return $this->getData(self::CUSTOMERCREDIT_AMOUNT);
    }

    /**
     * set item extension data.
     *
     * @param string
     * @return $this
     */
    public function setAmount($amount){
        return $this->setData(self::CUSTOMERCREDIT_AMOUNT, $amount);
    }
    
    /**
     * get item extension data.
     *
     * @return string
     */
    public function getCreditPriceAmount(){
        return $this->getData(self::CUSTOMERCREDIT_PRICE_AMOUNT);
    }

    /**
     * set item extension data.
     *
     * @param string
     * @return $this
     */
    public function setCreditPriceAmount($amount){
        return $this->setData(self::CUSTOMERCREDIT_PRICE_AMOUNT, $amount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getItemId(){
        return $this->getData(self::ITEM_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setItemId($itemId){
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getUseDiscount(){
        return $this->getData(self::USE_DISCOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setUseDiscount($useDiscount){
        return $this->setData(self::USE_DISCOUNT, $useDiscount);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getGiftcardTemplateId() {
        return $this->getData(self::GIFTCARD_TEMPLATE_ID);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setGiftcardTemplateId($giftCardTemplateId){
        return $this->setData(self::GIFTCARD_TEMPLATE_ID, $giftCardTemplateId);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getGiftcardTemplateImage(){
        return $this->getData(self::GIFTCARD_TEMPLATE_IMAGE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setGiftcardTemplateImage($giftcardTemplateImage){
        return $this->setData(self::GIFTCARD_TEMPLATE_IMAGE, $giftcardTemplateImage);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getMessage(){
        return $this->getData(self::GIFTCARD_MESSAGE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setMessage($message){
        return $this->setData(self::GIFTCARD_MESSAGE, $message);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getRecipientName(){
        return $this->getData(self::GIFTCARD_RECIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setRecipientName($recipientName){
        return $this->setData(self::GIFTCARD_RECIPIENT_NAME, $recipientName);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getRecipientEmail(){
        return $this->getData(self::GIFTCARD_RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setRecipientEmail($recipientEmail){
        return $this->setData(self::GIFTCARD_RECIPIENT_EMAIL, $recipientEmail);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getRecipientShip(){
        return $this->getData(self::GIFTCARD_RECIPIENT_SHIP);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setRecipientShip($recipientShip){
        return $this->setData(self::GIFTCARD_RECIPIENT_SHIP, $recipientShip);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getSendFriend(){
        return $this->getData(self::GIFTCARD_SEND_FRIEND);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setSendFriend($sendFriend){
        return $this->setData(self::GIFTCARD_SEND_FRIEND, $sendFriend);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getDayToSend(){
        return $this->getData(self::GIFTCARD_DAY_TO_SEND);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setDayToSend($dayToSend){
        return $this->setData(self::GIFTCARD_DAY_TO_SEND, $dayToSend);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getTimezoneToSend(){
        return $this->getData(self::GIFTCARD_TIMEZONE_TO_SEND);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setTimezoneToSend($timezoneToSend){
        return $this->setData(self::GIFTCARD_TIMEZONE_TO_SEND, $timezoneToSend);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getRecipientAddress(){
        return $this->getData(self::GIFTCARD_RECIPIENT_ADDRESS);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setRecipientAddress($recipientAddress){
        return $this->setData(self::GIFTCARD_RECIPIENT_ADDRESS, $recipientAddress);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getNotifySuccess(){
        return $this->getData(self::GIFTCARD_NOTIFY_SUCCESS);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setNotifySuccess($notifySuccess){
        return $this->setData(self::GIFTCARD_NOTIFY_SUCCESS, $notifySuccess);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCustomGiftcardAmount(){
        return $this->getData(self::GIFTCARD_M2EE_CUSTOM_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCustomGiftcardAmount($amount){
        return $this->setData(self::GIFTCARD_M2EE_CUSTOM_AMOUNT, $amount);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getGiftcardAmount(){
        return $this->getData(self::GIFTCARD_M2EE_AMOUNT);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setGiftcardAmount($notifySuccess){
        return $this->setData(self::GIFTCARD_M2EE_AMOUNT, $notifySuccess);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getGiftcardRecipientName(){
        return $this->getData(self::GIFTCARD_M2EE_RECEIPIENT_NAME);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setGiftcardRecipientName($notifySuccess){
        return $this->setData(self::GIFTCARD_M2EE_RECEIPIENT_NAME, $notifySuccess);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getGiftcardSenderName(){
        return $this->getData(self::GIFTCARD_M2EE_SENDER_NAME);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setGiftcardSenderName($notifySuccess){
        return $this->setData(self::GIFTCARD_M2EE_SENDER_NAME, $notifySuccess);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getGiftcardSenderEmail(){
        return $this->getData(self::GIFTCARD_M2EE_SENDER_EMAIL);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setGiftcardSenderEmail($notifySuccess){
        return $this->setData(self::GIFTCARD_M2EE_SENDER_EMAIL, $notifySuccess);
    }
    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getGiftcardRecipientEmail(){
        return $this->getData(self::GIFTCARD_M2EE_RECIPIENT_EMAIL);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setGiftcardRecipientEmail($notifySuccess){
        return $this->setData(self::GIFTCARD_M2EE_RECIPIENT_EMAIL, $notifySuccess);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getGiftcardMessage(){
        return $this->getData(self::GIFTCARD_M2EE_MESSAGE);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setGiftcardMessage($notifySuccess){
        return $this->setData(self::GIFTCARD_M2EE_MESSAGE, $notifySuccess);
    }



    /**
     * get customer name.
     *
     * @return string
     */
    public function getCustomerName(){
        return self::GIFTCARD_CUSTOMER_NAME;
    }

    /**
     * set customer name.
     *
     * @param string
     * @return $this
     */
    public function setCustomerName($customerName){
        return $this->setData(self::GIFTCARD_CUSTOMER_NAME, $customerName);
    }
}