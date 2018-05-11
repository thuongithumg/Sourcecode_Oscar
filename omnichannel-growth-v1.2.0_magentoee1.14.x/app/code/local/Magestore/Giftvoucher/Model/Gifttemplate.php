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
 * Giftvoucher Gifttemplate Model
 * 
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @author      Magestore Developer
 */

class Magestore_Giftvoucher_Model_Gifttemplate extends Mage_Core_Model_Abstract
{

    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const GIFTCARD_TEMPLATE_ID = 'giftcard_template_id';
    const TEMPLATE_NAME = 'template_name';
    const NOTES = 'notes';
    const STYLE_COLOR = 'style_color';
    const TEXT_COLOR = 'text_color';
    const DESIGN_PATTERN = 'design_pattern';
    const IMAGES = 'images';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const STATUS = 'status';
    /**#@-*/

    const STATUS_ENABLE = 1;
    const STATUS_DISABLE = 2;

    const DEFAULT_TEMPLATE_ID = 'amazon-giftcard-01';

    /**
     * Constants for keys of print data array
     */
    const LOGO_URL_PRINT = 'logo_url';
    const IMAGE_URL_PRINT = 'giftImageUrl';
    const BACKGROUND_URL_PRINT = 'giftBackgroundUrl';
    const MESSAGE_PRINT = 'giftMessage';
    const VALUE_PRINT = 'giftValue';
    const GIFTCODE_PRINT = 'giftCode';
    const BARCODE_URL_PRINT = 'barcodeUrl';
    const EXPIRED_DATE_PRINT = 'expiredDate';
    const NOTES_PRINT = 'notes';
    const TEXT_COLOR_PRINT = 'textColor';
    const STYLE_COLOR_PRINT = 'styleColor';
    const SENDER_NAME_PRINT = 'senderName';
    const RECIPIENT_NAME_PRINT = 'recipientName';
    const TITLE_PRINT = 'giftCardTitleText';
    const FROM_TEXT_PRINT = 'fromText';
    const TO_TEXT_PRINT = 'toText';
    const VALUE_TEXT_PRINT = 'giftCardValueText';
    const BKG_VALUE_URL_PRINT = 'bkgValueUrl';
    const BKG_TITLE_URL_PRINT = 'bkgTitleUrl';

    public function _construct()
    {
        parent::_construct();
        $this->_init('giftvoucher/gifttemplate');
    }

}
