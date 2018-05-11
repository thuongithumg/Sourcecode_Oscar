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
 * Giftvoucher default helper
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Helper_Data extends Mage_Core_Helper_Data
{

    protected $_imageUrl;
    protected $_imageName;
    protected $_imageReturn;

    /**
     * Get Gift Card general configuration
     *
     * @param string $code
     * @param int|null $store
     * @return boolean
     */
    public function getGeneralConfig($code, $store = null)
    {
        if ($code == 'barcode_enable' || $code == 'barcode_type') {
            return Mage::getStoreConfig('giftvoucher/print_voucher/' . $code, $store);
        }
        return Mage::getStoreConfig('giftvoucher/general/' . $code, $store);
    }

    /**
     * Get Gift Card print configuration
     *
     * @param $code
     * @param null $store
     * @return mixed
     */
    public function getPrintConfig($code, $store = null)
    {
        return Mage::getStoreConfig('giftvoucher/print_voucher/' . $code, $store);
    }

    /**
     * Get Gift Card interface configuration
     *
     * @param string $code
     * @param int|null $store
     * @return boolean
     */
    public function getInterfaceConfig($code, $store = null)
    {
        return Mage::getStoreConfig('giftvoucher/interface/' . $code, $store);
    }

    /**
     * Get Gift Card checkout configuration
     *
     * @param string $code
     * @param int|null $store
     * @return boolean
     */
    public function getInterfaceCheckoutConfig($code, $store = null)
    {
        return Mage::getStoreConfig('giftvoucher/interface_checkout/' . $code, $store);
    }

    /**
     * Check the Gift Card whether it is allowed to use credits or not
     *
     * @param int|null $store
     * @return boolean
     */
    public function isAllowRedeem($store = null)
    {
        if ($this->getGeneralConfig('enablecredit', $store)) {
            return true;
        }
        if ($this->getGeneralConfig('allow_enterprise_balance', $store) 
            && Mage::getStoreConfig('customer/enterprise_customerbalance/is_enabled', $store)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get Gift Card email configuration
     *
     * @param $code
     * @param int|null $store
     * @return bool
     */
    public function getEmailConfig($code, $store = null)
    {
        return Mage::getStoreConfig('giftvoucher/email/' . $code, $store);
    }

    /**
     * Returns a gift code string
     *
     * @param $expression
     * @return string
     * @internal param string $param
     */
    public function calcCode($expression)
    {
        if ($this->isExpression($expression)) {
            return preg_replace_callback('#\[([AN]{1,2})\.([0-9]+)\]#', array($this, 'convertExpression'), $expression);
        } else {
            return $expression;
        }
    }

    /**
     * Convert a expression to the numeric and alphabet
     *
     * @param string $param
     * @return string
     */
    public function convertExpression($param)
    {
        $alphabet = (strpos($param[1], 'A')) === false ? '' : 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $alphabet .= (strpos($param[1], 'N')) === false ? '' : '0123456789';
        return $this->getRandomString($param[2], $alphabet);
    }

    /**
     * Check a string whether it is a expression or not
     *
     * @param string $string
     * @return int|boolean
     */
    public function isExpression($string)
    {
        return preg_match('#\[([AN]{1,2})\.([0-9]+)\]#', $string);
    }

    /**
     * Get Gift Card product options configuration
     *
     * @return array
     */
    public function getGiftVoucherOptions()
    {
        $option = explode(',', Mage::helper('giftvoucher')->getInterfaceCheckoutConfig('display'));
        $result = array();
        foreach ($option as $key => $val) {
            if ($val == 'amount') {
                $result['amount'] = $this->__('Gift Card value');
            }
            if ($val == 'giftcard_template_id') {
                $result['giftcard_template_id'] = $this->__('Gift Card template');
            }
            if ($val == 'customer_name') {
                $result['customer_name'] = $this->__('Sender name');
            }
            if ($val == 'recipient_name') {
                $result['recipient_name'] = $this->__('Recipient name');
            }
            if ($val == 'recipient_email') {
                $result['recipient_email'] = $this->__('Recipient email address');
            }
            if ($val == 'recipient_ship') {
                $result['recipient_ship'] = $this->__('Ship to recipient');
            }
            if ($val == 'recipient_address') {
                $result['recipient_address'] = $this->__('Recipient address');
            }
            if ($val == 'message') {
                $result['message'] = $this->__('Custom message');
            }
            if ($val == 'day_to_send') {
                $result['day_to_send'] = $this->__('Day to send');
            }
            if ($val == 'timezone_to_send') {
                $result['timezone_to_send'] = $this->__('Time zone');
            }
            if ($val == 'giftcard_use_custom_image') {
                $result['giftcard_use_custom_image'] = $this->__('Use custom image');
            }
        }
        return $result;
    }

    /**
     * Get the full Gift Card options
     *
     * @return array
     */
    public function getFullGiftVoucherOptions()
    {
        return array(
            'customer_name' => $this->__('Sender Name'),
            'giftcard_template_id' => $this->__('Giftcard Template'),
            'send_friend' => $this->__('Send Gift Card to friend'),
            'recipient_name' => $this->__('Recipient name'),
            'recipient_email' => $this->__('Recipient email'),
            'recipient_ship' => $this->__('Ship to recipient'),
            'recipient_address' => $this->__('Recipient address'),
            'message' => $this->__('Custom message'),
            'day_to_send' => $this->__('Day To Send'),
            'timezone_to_send' => $this->__('Time zone'),
            'email_sender' => $this->__('Email To Sender'),
            'amount' => $this->__('Amount'),
            'giftcard_template_image' => $this->__('Giftcard Image'),
            'giftcard_use_custom_image' => $this->__('Use Custom Image'),
            'notify_success' => $this->__('Notify when the recipient receives Gift Card.')
        );
    }

    /**
     * Get the hidden gift code
     *
     * @param string $code
     * @return string
     */
    public function getHiddenCode($code)
    {
        $prefix = $this->getGeneralConfig('showprefix');
        $prefixCode = substr($code, 0, $prefix);
        $suffixCode = substr($code, $prefix);
        if ($suffixCode) {
            $hiddenChar = $this->getGeneralConfig('hiddenchar');
            if (!$hiddenChar) {
                $hiddenChar = 'X';
            } else {
                $hiddenChar = substr($hiddenChar, 0, 1);
            }
            $suffixCode = preg_replace('#([A-Z,0-9]{1})#', $hiddenChar, $suffixCode);
        }
        return $prefixCode . $suffixCode;
    }

    /**
     * Check gift codes whether they are available to add or not
     *
     * @return boolean
     */
    public function isAvailableToAddCode()
    {
        $codes = Mage::getSingleton('giftvoucher/session')->getCodes();
        if ($max = Mage::helper('giftvoucher')->getGeneralConfig('maximum')) {
            if (count($codes) >= $max) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check code can used to checkout or not
     * 
     * @param mixed $code
     * @return boolean
     */
    public function canUseCode($code)
    {
        if (!$code) {
            return false;
        }
        if (is_string($code)) {
            $code = Mage::getModel('giftvoucher/giftvoucher')->loadByCode($code);
        }
        if (!($code instanceof Magestore_Giftvoucher_Model_Giftvoucher)) {
            return false;
        }
        if (!$code->getId()) {
            return false;
        }
        if (Mage::app()->getStore()->isAdmin()) {
            return true;
        }
        $shareCard = intval($this->getGeneralConfig('share_card'));
        if ($shareCard < 1) {
            return true;
        }
        $customersUsed = $code->getCustomerIdsUsed();
        if ($shareCard > count($customersUsed) 
            || in_array(Mage::getSingleton('customer/session')->getCustomerId(), $customersUsed)
        ) {
            return true;
        }
        return false;
    }

    /**
     * @return array
     */
    public function getAllowedCurrencies()
    {
        $optionArray = array();
        $baseCode = Mage::app()->getBaseCurrencyCode();
        $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
        $rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCode, array_values($allowedCurrencies));

        foreach ($rates as $key => $value) {
            $test = Mage::app()->getLocale()->currency($key);
            $optionArray[] = array('value' => $key, 'label' => $test->getName());
        }

        if (!count($optionArray)) {
            $test = Mage::app()->getLocale()->currency($baseCode);
            $optionArray[] = array('value' => $baseCode, 'label' => $test->getName());
        }

        return $optionArray;
    }

    /**
     * @return string
     */
    public function getCheckGiftCardUrl()
    {
        return Mage::getUrl('giftvoucher/index/check');
    }

    /**
     * Upload template image
     * @param $type
     * @return string
     */
    public static function uploadImage($type, $designPattern)
    {
        self::createImageFolder($type);
        if (strpos($type, 'image') !== false) {
            $imagePath = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'images' . DS . $designPattern . DS;
            $imagePathEnd = 'images';
        } else {
            $imagePath = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'background' . DS . $designPattern . DS;
            $imagePathEnd = 'background';
        }
        $image = "";
        if (isset($_FILES[$type]['name']) && $_FILES[$type]['name'] != '') {
            try {
                /* Starting upload */
                $uploader = new Varien_File_Uploader($type);

                // Any extention would work
                $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                $uploader->setAllowRenameFiles(true);

                $uploader->setFilesDispersion(false);
                $result = $uploader->save($imagePath, $_FILES[$type]['name']);

                $image = $uploader->getUploadedFileName();
//                self::resizeImage(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'giftvoucher/template/' .
//                    $imagePathEnd . '/' . $result['file']);
//                self::customResizeImage(Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) .
//                    'giftvoucher/template/' . $imagePathEnd . '/', $result['file'], $imagePathEnd);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')
                    ->addError(Mage::helper('giftvoucher')->__('The file uploaded has invalid format. Support jpg, jpeg, gif, png files only.'));
            }
        }
        return $image;
    }

    /**
     * Create folder for template image
     * @param $type
     */
    public static function createImageFolder($type)
    {
        if (strpos($type, 'image') !== false) {
            $imagePath = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'images' . DS;
        } else {
            $imagePath = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'background' . DS;
        }
        if (!is_dir($imagePath)) {
            try {
                mkdir($imagePath);
                chmod($imagePath, 0777);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * Create folder for the Gift Card product image
     * @param $parent
     * @param $type
     * @param bool $tmp
     */
    public static function createImageFolderHaitv($parent, $type, $tmp = false)
    {
        if ($type !== '') {
            $urlType = $type . DS;
        } else {
            $urlType = '';
        }
        if ($tmp) {
            $imagePath = Mage::getBaseDir('media') . DS . 'tmp' . DS . 'giftvoucher' . DS . 'images' . DS;
        } else {
            $imagePath = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . 
                DS . $parent . DS . $urlType;
        }
        if (!is_dir($imagePath)) {
            try {
                mkdir($imagePath);
                chmod($imagePath, 0777);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * Delete image
     * 
     * @param string $image
     * @return boolean
     */
    public static function deleteImageFile($image)
    {

        if (!$image) {
            return;
        }
        $dirImg = Mage::getBaseDir() . str_replace("/", DS, strstr($image, '/media'));
        if (!file_exists($dirImg)) {
            return;
        }

        try {
            unlink($dirImg);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Convert notes
     * 
     * @param string $notes
     * @return string
     */
    public function convertNotes($notes)
    {
        $notes = str_replace(array(
            '{store_url}',
            '{store_name}',
            '{store_address}'
            ), array(
            Mage::app()->getStore($this->getStoreId())->getBaseUrl(),
            Mage::app()->getStore($this->getStoreId())->getFrontendName(),
            Mage::getStoreConfig('general/store_information/address', $this->getStoreId())
            ), $notes);
        return $notes;
    }

    /**
     * Resize a center Gift Card image
     * @param $imageUrl
     */
    public static function resizeImage($imageUrl)
    {
        $imageUrl = Mage::getBaseDir() . str_replace("/", DS, strstr($imageUrl, '/media'));
        if (file_exists($imageUrl)) {
            $imageObj = new Varien_Image($imageUrl);
            $imageObj->constrainOnly(TRUE);
            $imageObj->keepAspectRatio(false);
            $imageObj->keepFrame(false);
            $imageObj->resize(600, 365);
            self::deleteImageFile($imageUrl);
            $imageObj->save($imageUrl);
        }
    }

    /**
     * Resize the left, top and simple images
     * @param $imagePath
     * @param $imageName
     * @param $imageType
     */
    public static function customResizeImage($imagePath, $imageName, $imageType)
    {
        $imagePath = Mage::getBaseDir() . str_replace("/", DS, strstr($imagePath, '/media'));
        $imageUrl = $imagePath . $imageName;
        if (file_exists($imageUrl)) {
            self::createImageFolderHaitv($imageType, 'left');
            self::createImageFolderHaitv($imageType, 'top');
            self::createImageFolderHaitv($imageType, 'simple');
            self::createImageFolderHaitv($imageType, 'amazon');
            if ($imageType == 'images') {
                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(False);
                $imageObj->keepFrame(false);
                $imageObj->resize(600, 190);
                $imageObj->save($imagePath . 'top/' . $imageName);

                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(False);
                $imageObj->keepFrame(false);
                $imageObj->resize(250, 365);
                $imageObj->save($imagePath . 'left/' . $imageName);
                
                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(False);
                $imageObj->keepFrame(false);
                $imageObj->resize(584, 310);
                $imageObj->save($imagePath . 'simple/' . $imageName);

                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(False);
                $imageObj->keepFrame(false);
                $imageObj->resize(584, 310);
                $imageObj->save($imagePath . 'amazon/' . $imageName);
            } else {
                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(False);
                $imageObj->keepFrame(false);
                $imageObj->resize(600, 175);
                $imageObj->save($imagePath . 'top/' . $imageName);

                $imageObj = new Varien_Image($imageUrl);
                $imageObj->constrainOnly(TRUE);
                $imageObj->keepAspectRatio(False);
                $imageObj->keepFrame(false);
                $imageObj->resize(350, 365);
                $imageObj->save($imagePath . 'left/' . $imageName);
            }
        }
    }

    /**
     * Create barcode image
     * @param $giftCode
     * @throws Zend_Barcode_Exception
     * @throws Zend_Barcode_Renderer_Exception
     * @throws Zend_Exception
     */
    public function createBarcode($giftCode)
    {
        Mage::helper('giftvoucher')->createImageFolderHaitv('barcode', '');
        $options = array(
            'text' => $giftCode, 
            'barHeight' => 40, 
            'barThickWidth' => 2, 
            'drawText' => false, 
            'barHeight' => 42, 
            'withQuietZones' => false, 
            'barThinWidth' => 1, 
            'factor' => 1
        );
        $barcode = new Zend_Barcode_Object_Code128($options);

        $barImage = new Zend_Barcode_Renderer_Image();
        $barImage->setBarcode($barcode);
        $resource = $barImage->draw();
        $imageUrl = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'barcode' . 
            DS . $giftCode . '.png';
        imagepng($resource, $imageUrl);
        imagedestroy($resource);

        $imageObj = new Varien_Image($imageUrl);
        $imageObj->constrainOnly(TRUE);
        $imageObj->keepAspectRatio(true);
        $imageObj->keepFrame(true);
        $imageObj->backgroundColor(array(255, 255, 255));
        $imageObj->resize($imageObj->getOriginalWidth() + 8, 40);
        $imageObj->save($imageUrl);
    }

    /**
     * Get thumbnail Gift Card product image
     * @param $url
     * @param $filename
     * @param $urlImage
     * @return $this
     */
    public function getProductThumbnail($url, $filename, $urlImage)
    {
        $this->_imageUrl = null;
        $this->_imageName = null;
        $this->_imageReturn = null;

        $this->_imageUrl = $url;
        $this->_imageName = $filename;
        $this->_imageReturn = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . $urlImage;
        return $this;
    }

    /**
     * Resize thumbnail Gift Card product image
     * @param $width
     * @param null $height
     * @return $this
     */
    public function resize($width, $height = null)
    {
        $imageReturn = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 
            "tmp/giftvoucher/cache/" . $this->_imageName;
        $this->_imageReturn = $imageReturn;
        if ($height == null) {
            $height = $width;
        }
        $imageUrl = Mage::getBaseDir('media') . DS . 'tmp' . DS . 'giftvoucher' . DS . 'cache' . 
            DS . str_replace("/", DS, $this->_imageName);
        $imageObj = new Varien_Image($this->_imageUrl);
        $imageObj->constrainOnly(TRUE);
        $imageObj->keepAspectRatio(TRUE);
        $imageObj->keepFrame(TRUE);
        $imageObj->backgroundColor(array(255, 255, 255));
        $imageObj->resize($width, $height);
        try {
            $imageObj->save($imageUrl);
        } catch (Exception $e) {
            
        }
        return $this;
    }

    /**
     * @param $size
     * @return $this
     */
    public function setWatermarkSize($size)
    {
        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->_imageReturn) {
            return $this->_imageReturn;
        }
        return '';
    }

    /**
     * Get the rate of items on quote
     * 
     * @param Mage_Sales_Model_Product $product
     * @param Mage_Core_Model_Store $store
     * @return float
     */
    public function getItemRateOnQuote($product, $store)
    {
        //Calculate rate to subtract taxable amount
        $priceIncludesTax = 
            (bool) Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $store);
        $taxClassId = $product->getTaxClassId();
        if ($taxClassId && $priceIncludesTax) {
            $request = Mage::getSingleton('tax/calculation')->getRateRequest(false, false, false, $store);
            $rate = Mage::getSingleton('tax/calculation')
                ->getRate($request->setProductClassId($taxClassId));
            return $rate;
        }
        return 0;
    }


    /**
     * @return mixed
     */
    public function getFoldPaperNote()
    {
        return Mage::getStoreConfig('giftvoucher/print_voucher/fold_paper_notes');
    }

    /**
     * @return array
     */
    public function getAllTemplatePatternId()
    {
        $dir = new DirectoryIterator(Mage::getBaseDir('media') . DS .  'giftvoucher' . DS . 'design-template' . DS);
        $extensions = "html";
        $templateFiles = array();

        foreach ($dir as $fileinfo) {
            if ($fileinfo->isFile() && stristr($extensions, $fileinfo->getExtension())) {
                $fileName = $fileinfo->getFilename();
                $fileName = str_replace('.html', '', $fileName);
                $templateFiles[] = $fileName;
            }
        }

        return $templateFiles;
    }

}
