<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Api\Data\Checkout;


/**
 * Interface CartItemInterface
 * @package Magestore\Webpos\Api\Data\Checkout
 */
interface CartItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**#@+
     * Constants for field names
     */
    const KEY_ID = 'id';
    const KEY_QTY = 'qty';
    const KEY_DISCOUNT_AMOUNT = 'discount_amount';
    const KEY_BASE_DISCOUNT_AMOUNT = 'base_discount_amount';
    const KEY_TAX_AMOUNT = 'tax_amount';
    const KEY_BASE_TAX_AMOUNT = 'base_tax_amount';
    const KEY_CUSTOM_PRICE = 'custom_price';
    const KEY_SUPER_ATTRIBUTE = 'super_attribute';
    const KEY_SUPER_GROUP = 'super_group';
    const KEY_BUNDLE_OPTION = 'bundle_option';
    const KEY_BUNDLE_OPTION_QTY = 'bundle_option_qty';
    const KEY_CUSTOM_OPTION = 'options';
    const KEY_IS_CUSTOM_SALE = 'is_custom_sale';
    const KEY_EXTENSION_DATA = 'extension_data';

    const KEY_QTY_TO_SHIP = 'qty_to_ship';
    const KEY_CUSTOM_SALE_ID = 'customsale';
    const CUSTOM_SALE_PRODUCT_SKU = 'webpos-customsale';
    const CUSTOM_SALE_TAX_CLASS_ID = 0;
    const CUSTOM_SALE_DESCRIPTION = 'custom_sale_description';

    const CUSTOMERCREDIT_AMOUNT = 'amount';
    const CUSTOMERCREDIT_PRICE_AMOUNT = 'credit_price_amount';

    const GIFTCARD_AMOUNT = 'amount';
    const GIFTCARD_TEMPLATE_ID = 'giftcard_template_id';
    const GIFTCARD_TEMPLATE_IMAGE = 'giftcard_template_image';

    const GIFTCARD_MESSAGE = 'message';
    const GIFTCARD_CUSTOMER_NAME = 'customer_name';
    const GIFTCARD_RECIPIENT_NAME = 'recipient_name';
    const GIFTCARD_RECIPIENT_EMAIL = 'recipient_email';
    const GIFTCARD_RECIPIENT_SHIP = 'recipient_ship';
    const GIFTCARD_SEND_FRIEND = 'send_friend';
    const GIFTCARD_DAY_TO_SEND = 'day_to_send';
    const GIFTCARD_TIMEZONE_TO_SEND = 'timezone_to_send';
    const GIFTCARD_RECIPIENT_ADDRESS = 'recipient_address';
    const GIFTCARD_NOTIFY_SUCCESS = 'notify_success';

//    intergrade Giftcard Magento 2 EE
    const GIFTCARD_M2EE_CUSTOM_AMOUNT = 'custom_giftcard_amount';
    const GIFTCARD_M2EE_AMOUNT = 'giftcard_amount';
    const GIFTCARD_M2EE_SENDER_NAME = 'giftcard_sender_name';
    const GIFTCARD_M2EE_RECEIPIENT_NAME = 'giftcard_recipient_name';
    const GIFTCARD_M2EE_SENDER_EMAIL = 'giftcard_sender_email';
    const GIFTCARD_M2EE_RECIPIENT_EMAIL = 'giftcard_recipient_email';
    const GIFTCARD_M2EE_MESSAGE = 'giftcard_message';

    const ITEM_ID = 'item_id';
    const USE_DISCOUNT = 'use_discount';
    /**#@-*/

    /**
     * Returns the product id.
     *
     * @return string|int id. Otherwise, null.
     */
    public function getId();

    /**
     * Sets the product id.
     *
     * @param string|int $id
     * @return $this
     */
    public function setId($id);
    
    /**
     * Returns the item quantity.
     *
     * @return float Qty. Otherwise, null.
     */
    public function getQty();
    
    /**
     * Sets the item quantity.
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);
    
    /**
     * Returns the item custom price.
     *
     * @return float.
     */
    public function getCustomPrice();
    
    /**
     * Sets the item custom price.
     *
     * @param float $customPrice
     * @return $this
     */
    public function setCustomPrice($customPrice);
    
    /**
     * Sets the item supper attribute.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $super_attribute
     * @return $this
     */
    public function setSuperAttribute($super_attribute);
    
    /**
     * Returns the item supper attribute.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] super attribute. Otherwise, null.
     */
    public function getSuperAttribute();
    
    /**
     * Sets the item supper group.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $super_group
     * @return $this
     */
    public function setSuperGroup($super_group);
    
    /**
     * Returns the item supper group.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] super group. Otherwise, null.
     */
    public function getSuperGroup();
    
    /**
     * Sets the item custom options.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $options
     * @return $this
     */
    public function setOptions($options);
    
    /**
     * Returns the item custom options.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] options. Otherwise, null.
     */
    public function getOptions();
    
    /**
     * Sets the item bundle option.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $bundle_option
     * @return $this
     */
    public function setBundleOption($bundle_option);
    
    /**
     * Returns the item bundle option.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] bundle option. Otherwise, null.
     */
    public function getBundleOption();
    
    /**
     * Sets the item bundle option qty.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] $bundle_option_qty
     * @return $this
     */
    public function setBundleOptionQty($bundle_option_qty);
    
    /**
     * Returns the item bundle option qty.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\CartItemOptionInterface[] bundle option qty. Otherwise, null.
     */
    public function getBundleOptionQty();
    
    /**
     * Sets is custom sale item
     *
     * @param boolean $isCustomSale
     * @return $this
     */
    public function setIsCustomSale($isCustomSale);
    
    /**
     * Returns is custom sale item.
     *
     * @return boolean is custom sale. Otherwise, null.
     */
    public function getIsCustomSale();
    
    /**
     * Sets qty to ship
     *
     * @param float $qtyToShip
     * @return $this
     */
    public function setQtyToShip($qtyToShip);
    
    /**
     * Returns qty to ship.
     *
     * @return float qty to ship. Otherwise, null.
     */
    public function getQtyToShip();

    /**
     * get all data.
     *
     * @param string $key
     * @return \Magestore\Webpos\Api\Data\Checkout\DataObjectInterface[]
     */
    public function getItemData($key = false);
    
    /**
     * set item data.
     *
     * @param anyType $data
     * @return $this
     */
    public function setItemData($data);

    /**
     * get item extension data.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[]
     */
    public function getExtensionData();

    /**
     * set item extension data.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\ExtensionDataInterface[] $data
     * @return $this
     */
    public function setExtensionData($extensionData);

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getCustomSaleDescription();

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function setCustomSaleDescription($customSaleDescription);
    
    /**
     * get item extension data.
     *
     * @return string
     */
    public function getAmount();

    /**
     * set item extension data.
     *
     * @param string
     * @return $this
     */
    public function setAmount($amount);
    
    /**
     * get item extension data.
     *
     * @return string
     */
    public function getCreditPriceAmount();

    /**
     * set item extension data.
     *
     * @param string
     * @return $this
     */
    public function setCreditPriceAmount($amount);


    /**
     * Returns the item id.
     *
     * @return string|int id. Otherwise, null.
     */
    public function getItemId();

    /**
     * Sets the item id.
     *
     * @param string|int $itemId
     * @return $this
     */
    public function setItemId($itemId);


    /**
     * Returns the use discount.
     *
     * @return string|int id. Otherwise, null.
     */
    public function getUseDiscount();

    /**
     * Sets the use discount.
     *
     * @param string|int $useDiscount
     * @return $this
     */
    public function setUseDiscount($useDiscount);

    /**
     * get gift card template id.
     *
     * @return string
     */
    public function getGiftcardTemplateId();

    /**
     * set gift card template id.
     *
     * @param string
     * @return $this
     */
    public function setGiftcardTemplateId($giftCardTemplateId);

    /**
     * get gift card template image.
     *
     * @return string
     */
    public function getGiftcardTemplateImage();

    /**
     * set gift card template image.
     *
     * @param string
     * @return $this
     */
    public function setGiftcardTemplateImage($giftcardTemplateImage);
    /**
     * get gift card message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * set gift card message.
     *
     * @param string
     * @return $this
     */
    public function setMessage($message);
    /**
     * get recipient name.
     *
     * @return string
     */
    public function getRecipientName();

    /**
     * set recipient name.
     *
     * @param string
     * @return $this
     */
    public function setRecipientName($recipientName);
    /**
     * get customer name.
     *
     * @return string
     */
    public function getCustomerName();

    /**
     * set customer name.
     *
     * @param string
     * @return $this
     */
    public function setCustomerName($customerName);
    /**
     * get recipient email.
     *
     * @return string
     */
    public function getRecipientEmail();

    /**
     * set recipient email.
     *
     * @param string
     * @return $this
     */
    public function setRecipientEmail($recipientEmail);
    /**
     * get recipient ship.
     *
     * @return string
     */
    public function getRecipientShip();

    /**
     * set recipient ship.
     *
     * @param string
     * @return $this
     */
    public function setRecipientShip($recipientShip);
    /**
     * get send friend.
     *
     * @return string
     */
    public function getSendFriend();

    /**
     * set send friend.
     *
     * @param string
     * @return $this
     */
    public function setSendFriend($sendFriend);
    /**
     * get day to send.
     *
     * @return string
     */
    public function getDayToSend();

    /**
     * set day to send.
     *
     * @param string
     * @return $this
     */
    public function setDayToSend($dayToSend);
    /**
     * get timezone to send.
     *
     * @return string
     */
    public function getTimezoneToSend();

    /**
     * set timezone to send.
     *
     * @param string
     * @return $this
     */
    public function setTimezoneToSend($timezoneToSend);
    /**
     * get recipient address
     *
     * @return string
     */
    public function getRecipientAddress();

    /**
     * set recipient address
     *
     * @param string
     * @return $this
     */
    public function setRecipientAddress($recipientAddress);
    /**
     * get notify success
     *
     * @return string
     */
    public function getNotifySuccess();

    /**
     * set notify success
     *
     * @param string
     * @return $this
     */
    public function setNotifySuccess($notifySuccess);
    /**
     * get giftcard custom amount
     *
     * @return string
     */
    public function getCustomGiftcardAmount();

    /**
     * set giftcard custom amount
     *
     * @param string
     * @return $this
     */
    public function setCustomGiftcardAmount($amount);
    /**
     * get gift card amount
     *
     * @return string
     */
    public function getGiftcardAmount();

    /**
     * set gift card amount
     *
     * @param string
     * @return $this
     */
    public function setGiftcardAmount($amount);
    /**
     * get giftcard receipient name
     *
     * @return string
     */
    public function getGiftcardRecipientName();

    /**
     * set giftcard receipient name
     *
     * @param string
     * @return $this
     */
    public function setGiftcardRecipientName($name);
    /**
     * get giftcard sender name
     *
     * @return string
     */
    public function getGiftcardSenderName();

    /**
     * set giftcard sender name
     *
     * @param string
     * @return $this
     */
    public function setGiftcardSenderName($name);
    /**
     * get giftcard sender email
     *
     * @return string
     */
    public function getGiftcardSenderEmail();

    /**
     * set giftcard sender email
     *
     * @param string
     * @return $this
     */
    public function setGiftcardSenderEmail($email);
    /**
     * get giftcard recipient email
     *
     * @return string
     */
    public function getGiftcardRecipientEmail();

    /**
     * set giftcard recipient email
     *
     * @param string
     * @return $this
     */
    public function setGiftcardRecipientEmail($email);
    /**
    * get giftcard messages
    *
    * @return string
    */
    public function getGiftcardMessage();

    /**
    * set giftcard messages
    *
    * @param string
    * @return $this
    */
    public function setGiftcardMessage($messages);
}
