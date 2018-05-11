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
 * Giftvoucher product helper
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Helper_Template extends Mage_Core_Helper_Data
{

    /**
     *
     * @return string
     */
    public function getLogo()
    {
        if ($printLogo = Mage::helper('giftvoucher')->getPrintConfig('logo')) {
            return Mage::app()->getStore()->getBaseUrl('media') . 'giftvoucher/pdf/logo/' . $printLogo;
        }
        return Mage::getDesign()->getSkinUrl(Mage::getStoreConfig('design/header/logo_src'));
    }

    /**
     *
     * @return string
     */
    public function getBkgTitleUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'giftvoucher/template/background/bkg-title.png';
    }

    /**
     *
     * @return string
     */
    public function getBkgValueUrl()
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'giftvoucher/template/background/bkg-value.png';
    }

    /**
     *
     * @return string
     */
    public function getMessageSample()
    {
        return Mage::helper('giftvoucher')->__('Write message here...');
    }

    /**
     *
     * @return float
     */
    public function getGiftCardValueSample()
    {
        return Mage::helper('core')->formatCurrency(100);
    }

    /**
     *
     * @return string
     */
    public function getBarcodeFileSample()
    {
        if(Mage::helper('giftvoucher')->getGeneralConfig('barcode_enable')){
            if (Mage::helper('giftvoucher')->getGeneralConfig('barcode_type') == 'code128') {
                return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'giftvoucher/template/barcode/default.png';
            } else {
                return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'giftvoucher/template/barcode/qr.png';
            }
        } else {
            return null;
        }
    }

    /**
     *
     * @return string
     */
    public function getExpiredDataSample()
    {
        if (Mage::helper('giftvoucher')->getGeneralConfig('show_expiry_date')) {
            return Mage::helper('giftvoucher')->__('Expired: ') . date('m/d/Y', strtotime(now()));
        } else {
            return '';
        }
    }

    /**
     *
     * @return string
     */
    public function getGiftCodeSample()
    {
        return 'GIFT-XXXX-XXXX';
    }

    /**
     *
     * @return string
     */
    public function getNotesSample()
    {
        return Mage::helper('giftvoucher')->__('Converting to cash is not allowed. You can redeem this gift card when checkout at your online store.');
    }

    /**
     *
     * @return string
     */
    public function getTextColorSample()
    {
        return '6C6C6C';
    }

    /**
     *
     * @return string
     */
    public function getStyleColorSample()
    {
        return '6C6C6C';
    }

    /**
     *
     * @return string
     */
    public function getFromText()
    {
        return 'From: ';
    }

    /**
     *
     * @return string
     */
    public function getToText()
    {
        return 'To: ';
    }

    /**
     *
     * @return string
     */
    public function getTitleText()
    {
        return 'Title of Gift Card';
    }

    /**
     *
     * @return string
     */
    public function getValueText()
    {
        return 'Value';
    }

    /**
     *
     * @return array
     */
    public function getSampleData()
    {
        return [
            Magestore_Giftvoucher_Model_GiftTemplate::LOGO_URL_PRINT => $this->getLogo(),
            Magestore_Giftvoucher_Model_GiftTemplate::IMAGE_URL_PRINT => '',
            Magestore_Giftvoucher_Model_GiftTemplate::BACKGROUND_URL_PRINT => '',
            Magestore_Giftvoucher_Model_GiftTemplate::VALUE_TEXT_PRINT => $this->getValueText(),
            Magestore_Giftvoucher_Model_GiftTemplate::FROM_TEXT_PRINT => $this->getFromText(),
            Magestore_Giftvoucher_Model_GiftTemplate::TO_TEXT_PRINT => $this->getToText(),
            Magestore_Giftvoucher_Model_GiftTemplate::TITLE_PRINT => $this->getTitleText(),
            Magestore_Giftvoucher_Model_GiftTemplate::MESSAGE_PRINT => $this->getMessageSample(),
            Magestore_Giftvoucher_Model_GiftTemplate::VALUE_PRINT => $this->getGiftCardValueSample(),
            Magestore_Giftvoucher_Model_GiftTemplate::GIFTCODE_PRINT => $this->getGiftCodeSample(),
            Magestore_Giftvoucher_Model_GiftTemplate::BARCODE_URL_PRINT => $this->getBarcodeFileSample(),
            Magestore_Giftvoucher_Model_GiftTemplate::EXPIRED_DATE_PRINT => $this->getExpiredDataSample(),
            Magestore_Giftvoucher_Model_GiftTemplate::NOTES_PRINT => $this->getNotesSample(),
            Magestore_Giftvoucher_Model_GiftTemplate::TEXT_COLOR_PRINT => $this->getTextColorSample(),
            Magestore_Giftvoucher_Model_GiftTemplate::STYLE_COLOR_PRINT => $this->getStyleColorSample(),
            Magestore_Giftvoucher_Model_GiftTemplate::BKG_VALUE_URL_PRINT => $this->getBkgValueUrl(),
            Magestore_Giftvoucher_Model_GiftTemplate::BKG_TITLE_URL_PRINT => $this->getBkgTitleUrl()
        ];
    }

    /**
     * @param $giftTemplate
     * @return mixed
     */
    public function getFirstImageUrl($giftTemplate)
    {
        $images = explode(',', $giftTemplate->getImages());
        $designPattern = $giftTemplate->getDesignPattern();
        return $this->getImageUrl(reset($images), $designPattern);
    }

    /**
     * @param $giftTemplate
     * @return mixed
     */
    public function getBackgroundImageUrl($giftTemplate)
    {
        $backgroundImage = $giftTemplate->getBackgroundImg();
        $designPattern = $giftTemplate->getDesignPattern();
        $dirBackground = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'background' . DS . $designPattern . DS . $backgroundImage;
        if (file_exists($dirBackground)) {
            return Mage::getBaseUrl('media') . 'giftvoucher/template/background/' . $designPattern . '/' . $backgroundImage;
        } else {
            return '';
        }
    }

    /**
     *
     * @param string $image
     * @return string
     */
    public function getImageUrl($image, $designPattern)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'giftvoucher/template/images/' . $designPattern . '/' . $image;
    }

    /**
     *
     * @param string $image
     * @return string
     */
    public function getCustomImageUrl($image)
    {
        return Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'giftvoucher/template/images/' . $image;
    }

    /**
     * Get content of template file
     *
     * @param string $designPattern
     * @return string
     */
    public function getTemplateContent($designPattern)
    {
        $ioAdapter = new Varien_Io_File();
        $ioAdapter->open(array('path' => Mage::getBaseDir('locale')));
        $templateFile = $this->getTemplateFileFromDesign($designPattern);
        $filePath = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'design-template' . DS . $templateFile . '.html';
        return (string) $ioAdapter->read($filePath);

    }

    /**
     * Get template file from design pattern
     *
     * @param string $designPattern
     * @return string
     */
    public function getTemplateFileFromDesign($designPattern)
    {
        $templateFile = Magestore_Giftvoucher_Model_GiftTemplate::DEFAULT_TEMPLATE_ID;
        $availabelTemplates = $this->getAvailableTemplates();
        if (in_array($designPattern, $availabelTemplates)) {
            $templateFile = $designPattern;
        }

        return $templateFile;
    }

    /**
     * Get list of available gift card templates
     *
     * @return array
     */
    public function getAvailableTemplates()
    {
        $result = [];
        $fileObject = new Varien_Io_File();
        $directory = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'design-template' . DS;
        $fileObject->open(array('path' => $directory));
        $fileList =  $fileObject->ls();
        foreach ($fileList as $file) {
            $result[] = str_replace('.html', '', $file['text']);
        }

        return $result;
    }


    /**
     * Get print gift code data
     *
     * @param Magestore_Giftvoucher_Model_Giftvoucher $giftcode
     * @return array
     */
    public function toPrintData($giftcode, $giftTemplate)
    {
        return [
            Magestore_Giftvoucher_Model_GiftTemplate::VALUE_TEXT_PRINT => $this->getValueText(),
            Magestore_Giftvoucher_Model_GiftTemplate::FROM_TEXT_PRINT => $this->getFromText(),
            Magestore_Giftvoucher_Model_GiftTemplate::TO_TEXT_PRINT => $this->getToText(),
            Magestore_Giftvoucher_Model_GiftTemplate::TITLE_PRINT => $this->getTitleText(),
            Magestore_Giftvoucher_Model_GiftTemplate::LOGO_URL_PRINT => $this->getLogo(),
            Magestore_Giftvoucher_Model_GiftTemplate::IMAGE_URL_PRINT => ($giftcode->getGiftcardCustomImage()) ?
                $this->getCustomImageUrl($giftcode->getGiftcardTemplateImage())
                : $this->getImageUrl($giftcode->getGiftcardTemplateImage(), $giftTemplate->getDesignPattern()),
            Magestore_Giftvoucher_Model_GiftTemplate::BACKGROUND_URL_PRINT => $this->getBackgroundImageUrl($giftTemplate),
            Magestore_Giftvoucher_Model_GiftTemplate::MESSAGE_PRINT => $giftcode->getMessage(),
            Magestore_Giftvoucher_Model_GiftTemplate::VALUE_PRINT => $this->getFormatPriceBalance($giftcode),
            Magestore_Giftvoucher_Model_GiftTemplate::GIFTCODE_PRINT => $giftcode->getGiftCode(),
            Magestore_Giftvoucher_Model_GiftTemplate::BARCODE_URL_PRINT => Mage::helper('giftvoucher/barcode')->getBarcodeImageSource($giftcode->getGiftCode()),
            Magestore_Giftvoucher_Model_GiftTemplate::EXPIRED_DATE_PRINT => $giftcode->getExpiredAt()
                ? date('m/d/Y', strtotime($giftcode->getExpiredAt()))
                : null,
            Magestore_Giftvoucher_Model_GiftTemplate::SENDER_NAME_PRINT => $giftcode->getCustomerName(),
            Magestore_Giftvoucher_Model_GiftTemplate::RECIPIENT_NAME_PRINT => $giftcode->getRecipientName(),
            Magestore_Giftvoucher_Model_GiftTemplate::BKG_VALUE_URL_PRINT => $this->getBkgValueUrl(),
            Magestore_Giftvoucher_Model_GiftTemplate::BKG_TITLE_URL_PRINT => $this->getBkgTitleUrl(),
        ];
    }
    
    /**
     * 
     * @param Magestore_Giftvoucher_Model_Giftvoucher $giftcode
     * @return string
     */
    public function getFormatPriceBalance($giftcode)
    {
        $currency = Mage::getModel('directory/currency')->load($giftcode->getCurrency());
        return $currency->format($giftcode->getBalance(), array(), false);
    }

}
