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
 * Giftvoucher draw helper
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Helper_Drawgiftcard extends Mage_Core_Helper_Data
{
    
    /**
     * Get the drawing directory of Gift Card
     *
     * @param null|string $giftcode
     * @return string
     */
    public function getImgDir($giftcode = null)
    {
        $gcTemplateDir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . $giftcode . DS;
        $io = new Varien_Io_File();
        $io->checkAndCreateFolder($gcTemplateDir, 0755);
        return $gcTemplateDir;
    }

    /**
     * Draw Gift Card templates
     * @param $giftcode
     */
    public function draw($giftcode)
    {

        if (isset($giftcode['giftcard_template_id']) && $giftcode['giftcard_template_id'] != null) {
            $giftcardTemplate = Mage::getModel('giftvoucher/gifttemplate')->load($giftcode['giftcard_template_id']);
            switch ($giftcardTemplate['design_pattern']) {
                case '2':
                    $this->generateTopImage($giftcode, $giftcardTemplate);
                    break;
                case '3':
                    $this->generateCenterImage($giftcode, $giftcardTemplate);
                    break;
                case '4':
                    $this->generateSimpleImage($giftcode, $giftcardTemplate);
                    break;
                case '5':
                    $this->generateSimpleImage($giftcode, $giftcardTemplate);
                    break;
                default:
                    $this->generateLeftImage($giftcode, $giftcardTemplate);
                    break;
            }
        }


    }

    /**
     * Draw the left template of Gift Card
     * @param $giftcode
     * @param $giftcardTemplate
     * @throws Mage_Core_Exception
     */
    public function generateLeftImage($giftcode, $giftcardTemplate)
    {

        $storeId = Mage::app()->getStore()->getId();
        $images = $this->getImagesInFolder($giftcode['gift_code']);
        if (isset($images[0]) && file_exists($images[0])) {
            unlink($images[0]);
        }

        $imageSuffix = Mage::getModel('core/date')->timestamp(now());

        $imgFile = $this->getImgDir($giftcode['gift_code']) . $giftcode['gift_code'] . '-' . $imageSuffix . '.png';
        $w = 600;
        $h = 365;

        $img = imagecreatetruecolor($w, $h);
        $textColor = $this->hexColorAllocate($img, $giftcardTemplate['text_color']);
        $styleColor = $this->hexColorAllocate($img, $giftcardTemplate['style_color']);
        $bgColor = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w - 1, $h - 1, $bgColor);


        $img2 = $this->createGCImage($giftcode['giftcard_template_image'], 'left');

        $img1 = $this->createGCBackground($giftcardTemplate['background_img'], 'left');

        $img3 = $this->createGCLogo();

        $img4 = $this->createGMessageBox('left');

        $x = 10;
        $y = 30;
        $fsize = 15;
        $font = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-Semibold.ttf';

        /* Insert Logo to Image */
        if ($img3) {
            $widthLogo = imagesx($img3);
            $heightLogo = imagesy($img3);
            imagecopyresampled($img2, $img3, (250 - $widthLogo) / 2, 265, 0, 0, $widthLogo, $heightLogo, $widthLogo, 
                $heightLogo);
        }

        /* Draw Expiry Date */
        if (Mage::helper('giftvoucher')->getGeneralConfig('show_expiry_date')) {
            if ($giftcode['expired_at']) {
                $expiryDate = Mage::helper('giftvoucher')->__('Expired: ') . 
                    date('m/d/Y', strtotime($giftcode['expired_at']));
                $textbox = imageftbbox(9, 0, $font, $expiryDate);
                imagefttext($img2, 9, 0, (250 - ($textbox[2] - $textbox[0])) / 2, 350, 
                    imagecolorallocate($img, 255, 255, 255), $font, $expiryDate);
            }
        }

        /* Draw Text */
        $word = $giftcardTemplate['caption'];
        $textbox = imageftbbox($fsize, 0, $font, $word);

        $stringArray = $this->processString($word, $font, $fsize, 350);
        // The width of textbox: $textbox[2] - $textbox[0]
        // The height of textbox: $textbox[7] - $textbox[1]
        // $x = ($w - ($textbox[2] - $textbox[0])) / 2;
        // $y = ($h - ($textbox[7] - $textbox[1])) / 2;

        for ($i = 0; $i < count($stringArray); $i++) {
            imagefttext($img1, $fsize, 0, $x, $y, $styleColor, $font, $stringArray[$i]);
            $y -= 1.4 * ($textbox[7] - $textbox[1]);
        }

        // $font = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-Light.ttf';
        /* Print "From:" and "To: " */

        $textbox = imageftbbox($fsize, 0, $font, Mage::helper('giftvoucher')->__('From: '));
        imagefttext($img1, 13, 0, 15, $y, $textColor, $font, Mage::helper('giftvoucher')->__('From: '));
        imagefttext($img1, 13, 0, $x + ($textbox[2] - $textbox[0]), $y, $styleColor, $font, 
            $giftcode['customer_name']);
        $y -= 1.55 * ($textbox[7] - $textbox[1]);

        $textbox = imageftbbox($fsize, 0, $font, Mage::helper('giftvoucher')->__('To: '));
        imagefttext($img1, 13, 0, 15, $y + 5, $textColor, $font, Mage::helper('giftvoucher')->__('To: '));
        imagefttext($img1, 13, 0, $x + ($textbox[2] - $textbox[0]), $y + 5, $styleColor, $font, 
            $giftcode['recipient_name']);
        $y -= 1.55 * ($textbox[7] - $textbox[1]);

        /* Print Customers' s messages */

        $xMessage = 5;
        $yMessage = 15;

        if (isset($giftcode['message']) && $giftcode['message'] != null) {
            $message = $giftcode['message'];
        } else {
            $message = '';
        }

        $stringArray = $this->processString($message, $font, 9, 322);

        for ($i = 0; $i < count($stringArray); $i++) {
            imagefttext($img4, 9, 0, $xMessage, $yMessage, $textColor, $font, $stringArray[$i]);
            $yMessage -= 1.25 * ($textbox[7] - $textbox[1]);
        }

        imagecopyresampled($img1, $img4, 14, $y - 10, 0, 0, 322, 90, 322, 90);

        /* Print Value */

        $valueY = $y + 100;
        $fsizePrice = 13;

        $textbox = imageftbbox($fsize, 0, $font, Mage::helper('giftvoucher')->__('Value '));
        imagefttext($img1, 10, 0, 15, $valueY, $textColor, $font, Mage::helper('giftvoucher')->__('Value '));
        $valueY -= 1.55 * ($textbox[7] - $textbox[1]);

        $price = Mage::getModel('directory/currency')->setData('currency_code', $giftcode['currency'])
            ->format($giftcode['balance'], array('display' => Zend_Currency::USE_SYMBOL), false);

        $textbox = imageftbbox($fsizePrice, 0, $font, $price);
        imagefttext($img1, $fsizePrice, 0, 15, $valueY + 5, $styleColor, $font, $price);
        $valueY -= 1.55 * ($textbox[7] - $textbox[1]);

        /* Print Gift Code */

        $fontCode = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-SemiboldItalic.ttf';
        $codeY = $y + 105;
        $textbox = imageftbbox(13, 0, $fontCode, $giftcode['gift_code']);
        imagefttext($img1, 13, 0, 335 - ($textbox[2] - $textbox[0]), $codeY, $styleColor, $fontCode, 
            $giftcode['gift_code']);
        $codeY -= ($textbox[7] - $textbox[1]);

        /* Print Barcode */
        $barcode = Mage::helper('giftvoucher')->getGeneralConfig('barcode_enable');
        if ($barcode) {
            $newImgBarcode = $this->resizeBarcodeImage($giftcode['gift_code']);
            $newImgBarcodeX = imagesx($newImgBarcode);
            $newImgBarcodeY = imagesy($newImgBarcode);
            imagecopyresampled($img1, $newImgBarcode, 335 - $newImgBarcodeX, $codeY - 5, 0, 0, 
                $newImgBarcodeX, $newImgBarcodeY, $newImgBarcodeX, $newImgBarcodeY);
        }

        /* Print Notes */

        if (isset($giftcardTemplate['notes']) && $giftcardTemplate['notes'] != null) {
            $notes = $giftcardTemplate['notes'];
        } else {
            $notes = Mage::getStoreConfig('giftvoucher/print_voucher/note', $storeId);
            $notes = str_replace(array(
                '{store_url}',
                '{store_name}',
                '{store_address}'
                ), array(
                '<span class="print-notes">' . Mage::app()->getStore($storeId)->getBaseUrl() . '</span>',
                '<span class="print-notes">' . Mage::app()->getStore($storeId)->getFrontendName() . '</span>',
                '<span class="print-notes">' . Mage::getStoreConfig('general/store_information/address', $storeId) . 
                    '</span>'
                ), $notes);
            $notes = strip_tags($notes);
        }

        $stringArray = $this->processString($notes, $font, 9, 350);
        for ($i = 0; $i < count($stringArray); $i++) {
            imagefttext($img1, 9, 0, $x, $codeY + 58, $textColor, $font, $stringArray[$i]);
            $codeY -= 1.3 * ($textbox[7] - $textbox[1]);
        }

        /* End */


        /* Draw Images */
        imagecopyresampled($img, $img2, 0, 0, 0, 0, 250, 365, 250, 365);


        /**
         * Draw Background 
         */
        imagecopyresampled($img, $img1, 250, 0, 0, 0, 350, 365, 350, 365);

        imagepng($img, $imgFile);
        imagedestroy($img);
    }

    /**
     * Draw the simple template of Gift Card
     * @param $giftcode
     * @param $giftcardTemplate
     */
    public function generateSimpleImage($giftcode, $giftcardTemplate)
    {

        $storeId = Mage::app()->getStore()->getId();
        $images = $this->getImagesInFolder($giftcode['gift_code']);
        if (isset($images[0]) && file_exists($images[0])) {
            unlink($images[0]);
        }

        $imageSuffix = Mage::getModel('core/date')->timestamp(now());

        $imgFile = $this->getImgDir($giftcode['gift_code']) . $giftcode['gift_code'] . '-' . $imageSuffix . '.png';
        
        if (isset($giftcode['message']) && $giftcode['message'] != null) {
            $w = 600;
            $h = 529;
        } else {
            $w = 600;
            $h = 437;
        }

        $img = imagecreatetruecolor($w, $h);
        $textColor = $this->hexColorAllocate($img, $giftcardTemplate['text_color']);
        $styleColor = $this->hexColorAllocate($img, $giftcardTemplate['style_color']);
        $bgColor = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w - 1, $h - 1, $bgColor);

        // if (isset($giftcode['giftcard_template_image']) && $giftcode['giftcard_template_image'] != null)
        $img2 = $this->createGCImage($giftcode['giftcard_template_image'], 'simple');

        $img1 = $this->createGCBackground($giftcardTemplate['background_img'], 'simple', $giftcode);

        $img3 = $this->createGCLogo('simple');        
        
        /* Print Barcode */
        $barcode = Mage::helper('giftvoucher')->getGeneralConfig('barcode_enable');
        if ($barcode) {
            $this->columnImage(3, $img1);
            $newImgBarcode = $this->resizeBarcodeImage($giftcode['gift_code']);
            $newImgBarcodeX = imagesx($newImgBarcode);
            $newImgBarcodeY = imagesy($newImgBarcode);
            imagecopyresampled($img1, $newImgBarcode, 400 + (200 - $newImgBarcodeX) / 2, 317 + (120 - $newImgBarcodeY) / 2, 0, 0, 
                $newImgBarcodeX, $newImgBarcodeY, $newImgBarcodeX, $newImgBarcodeY);
        } else {
            $this->columnImage(2, $img1);
        }
        
        /* Insert Logo to Image */
        if ($img3) {
            $widthLogo = imagesx($img3);
            $heightLogo = imagesy($img3);

            if ($barcode) {
                imagecopyresampled($img1, $img3, (200 - $widthLogo) / 2, 317 + (120 - $heightLogo) / 2, 0, 0, $widthLogo, $heightLogo, $widthLogo, $heightLogo);
            } else {
                imagecopyresampled($img1, $img3, (300 - $widthLogo) / 2, 317 + (120 - $heightLogo) / 2, 0, 0, $widthLogo, $heightLogo, $widthLogo, $heightLogo);
            }
        }
        
        /* Print Value */
        $font = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-Semibold.ttf';
        $fsizePrice = 32;
        
        $price = Mage::getModel('directory/currency')->setData('currency_code', $giftcode['currency'])
            ->format($giftcode['balance'], array('display' => Zend_Currency::USE_SYMBOL), false);
        
        for ($i = 0; $i < 32 ; $i ++) {
            $textbox = imageftbbox($fsizePrice, 0, $font, $price);
            $valueX = $textbox[2] - $textbox[0];
            if ($barcode) {
                if ($valueX > 180) {
                    $fsizePrice -= 1;
                }
            } else {
                if ($valueX > 280) {
                    $fsizePrice -= 1;
                }
            }
        }        
        
        if ($barcode) {
            imagefttext($img1, $fsizePrice, 0, 200 + (200 - $valueX) / 2, 370, $textColor, $font, $price);
        } else {
            imagefttext($img1, $fsizePrice, 0, 300 +  (300 - $valueX) / 2, 370, $textColor, $font, $price);
        }
        
        /* Print Gift Code */
        $fontCode = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-Semibold.ttf';
        $fsizeCode = 15;

        for ($i = 0; $i < 15 ; $i ++) {
            $textbox = imageftbbox($fsizeCode, 0, $fontCode, $giftcode['gift_code']);
            $codeX = $textbox[2] - $textbox[0];
            if ($barcode) {
                if ($codeX > 180) {
                    $fsizeCode -= 1;
                }
            } else {
                if ($codeX > 280) {
                    $fsizeCode -= 1;
                }
            }
        }

        if ($barcode) {
            imagefttext($img1, $fsizeCode, 0, 200 + (200 - $codeX) / 2, 410, $styleColor, $fontCode, 
                $giftcode['gift_code']);
        } else {
            imagefttext($img1, $fsizeCode, 0, 300 + (300 - $codeX) / 2, 410, $styleColor, $fontCode, 
                $giftcode['gift_code']);
        }
        
        /* Print Customers' s messages */
        $xMessage = 20;
        $yMessage = 455;

        if (isset($giftcode['message']) && $giftcode['message'] != null) {
            $message = $giftcode['message'];
            $stringArray = $this->processString($message, $font, 9, 580);       
            
            for ($i = 0; $i < count($stringArray); $i++) {
                imagefttext($img1, 9, 0, $xMessage, $yMessage, $textColor, $font, $stringArray[$i]);
                $yMessage += 20;
            }
        }       

        /* Draw Images */
        imagecopyresampled($img1, $img2, 8, 8, 0, 0, 584, 310, 584, 310);


        /* Draw Background */
        imagecopyresampled($img, $img1, 0, 0, 0, 0, 600, 529, 600, 529);

        imagepng($img, $imgFile);
        imagedestroy($img);
    }


    /**
     * Draw the top template of Gift Card
     * @param $giftcode
     * @param $giftcardTemplate
     * @throws Mage_Core_Exception
     */
    public function generateTopImage($giftcode, $giftcardTemplate)
    {

        $storeId = Mage::app()->getStore()->getId();
        $images = $this->getImagesInFolder($giftcode['gift_code']);
        if (isset($images[0]) && file_exists($images[0])) {
            unlink($images[0]);
        }

        $imageSuffix = Mage::getModel('core/date')->timestamp(now());

        $imgFile = $this->getImgDir($giftcode['gift_code']) . $giftcode['gift_code'] . '-' . $imageSuffix . '.png';
        $w = 600;
        $h = 365;

        $img = imagecreatetruecolor($w, $h);
        $textColor = $this->hexColorAllocate($img, $giftcardTemplate['text_color']);
        $styleColor = $this->hexColorAllocate($img, $giftcardTemplate['style_color']);
        $bgColor = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w - 1, $h - 1, $bgColor);

        // if (isset($giftcode['giftcard_template_image']) && $giftcode['giftcard_template_image'] != null)
        $img2 = $this->createGCImage($giftcode['giftcard_template_image'], 'top');

        $img1 = $this->createGCBackground($giftcardTemplate['background_img'], 'top');

        $img3 = $this->createGCLogo();

        $img4 = $this->createGMessageBox('top');

        $img5 = $this->createGCBackground('bkg-title.png');

        $img6 = $this->createGCBackground('bkg-value.png');

        $x = 10;
        $y = 33;
        $fsize = 15;
        $font = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-Semibold.ttf';

        /* Draw Expiry Date */
        if (Mage::helper('giftvoucher')->getGeneralConfig('show_expiry_date')) {
            if ($giftcode['expired_at']) {
                $expiryDate = Mage::helper('giftvoucher')->__('Expired: ') . 
                    date('m/d/Y', strtotime($giftcode['expired_at']));
                $textbox = imageftbbox(9, 0, $font, $expiryDate);
                imagefttext($img2, 9, 0, (580 - ($textbox[2] - $textbox[0])), 25, 
                    imagecolorallocate($img, 255, 255, 255), $font, $expiryDate);
            }
        }


        /* Draw Text */

        $word = $giftcardTemplate['caption'];
        $textbox = imageftbbox($fsize, 0, $font, $word);

        $word = $this->processTitle($word, $font, $fsize, 370);
        imagefttext($img5, $fsize, 0, $x, $y, $styleColor, $font, $word);


        /* Print Value */
        $fontPrice = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-ExtraBold.ttf';
        $fsizePrice = 14;

        $price = Mage::getModel('directory/currency')->setData('currency_code', $giftcode['currency'])
            ->format($giftcode['balance'], array('display' => Zend_Currency::USE_SYMBOL), false);

        $textbox = imageftbbox($fsizePrice, 0, $fontPrice, $price);
        $valueX = $textbox[2] - $textbox[0];
        imagefttext($img6, $fsizePrice, 0, 210 - $valueX, $y + 3, $styleColor, $font, $price);

        $fsizeValue = 9;
        $textbox = imageftbbox($fsizeValue, 0, $fontPrice, Mage::helper('giftvoucher')->__('Value '));
        $valueX += $textbox[2] - $textbox[0] + 15;
        imagefttext($img6, $fsizeValue, 0, 210 - $valueX, $y, $styleColor, $font, 
            Mage::helper('giftvoucher')->__('Value '));

        /* Print "From:" and "To: " */

        $textbox = imageftbbox($fsize, 0, $font, Mage::helper('giftvoucher')->__('From: '));
        imagefttext($img1, 13, 0, 15, $y - 5, $textColor, $font, Mage::helper('giftvoucher')->__('From: '));
        imagefttext($img1, 13, 0, $x + ($textbox[2] - $textbox[0]), $y - 5, $textColor, $font, 
            $giftcode['customer_name']);
        $y -= 1.55 * ($textbox[7] - $textbox[1]);

        $textbox = imageftbbox($fsize, 0, $font, Mage::helper('giftvoucher')->__('To: '));
        imagefttext($img1, 13, 0, 15, $y, $textColor, $font, Mage::helper('giftvoucher')->__('To: '));
        imagefttext($img1, 13, 0, $x + ($textbox[2] - $textbox[0]), $y, $textColor, $font, 
            $giftcode['recipient_name']);
        $y -= 1.55 * ($textbox[7] - $textbox[1]);

        /* Print Customers' s messages */

        $xMessage = 5;
        $yMessage = 15;

        if (isset($giftcode['message']) && $giftcode['message'] != null) {
            $message = $giftcode['message'];
        } else {
            $message = '';
        }

        $stringArray = $this->processString($message, $font, 9, 340);

        for ($i = 0; $i < count($stringArray); $i++) {
            imagefttext($img4, 9, 0, $xMessage, $yMessage, $textColor, $font, $stringArray[$i]);
            $yMessage -= 1.25 * ($textbox[7] - $textbox[1]);
        }

        imagecopyresampled($img1, $img4, 14, $y - 7, 0, 0, 343, 97, 343, 97);

        /* Print Gift Code */

        $fontCode = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-SemiboldItalic.ttf';
        $codeY = 20;
        $textbox = imageftbbox(11, 0, $fontCode, $giftcode['gift_code']);
        imagefttext($img1, 11, 0, 590 - ($textbox[2] - $textbox[0]), $codeY, $textColor, $fontCode, 
            $giftcode['gift_code']);
        $codeY -= ($textbox[7] - $textbox[1]);

        /* Print Barcode */

        $barcode = Mage::helper('giftvoucher')->getGeneralConfig('barcode_enable');
        if ($barcode) {
            $newImgBarcode = $this->resizeBarcodeImage($giftcode['gift_code']);
            $newImgBarcodeX = imagesx($newImgBarcode);
            $newImgBarcodeY = imagesy($newImgBarcode);
            imagecopyresampled($img1, $newImgBarcode, 590 - $newImgBarcodeX, $codeY - 5, 0, 0, 
                $newImgBarcodeX, $newImgBarcodeY, $newImgBarcodeX, $newImgBarcodeY);
        }

        /* Print Notes */

        if (isset($giftcardTemplate['notes']) && $giftcardTemplate['notes'] != null) {
            $notes = $giftcardTemplate['notes'];
        } else {
            $notes = Mage::getStoreConfig('giftvoucher/print_voucher/note', $storeId);
            $notes = str_replace(array(
                '{store_url}',
                '{store_name}',
                '{store_address}'
                ), array(
                '<span class="print-notes">' . Mage::app()->getStore($storeId)->getBaseUrl() . '</span>',
                '<span class="print-notes">' . Mage::app()->getStore($storeId)->getFrontendName() . '</span>',
                '<span class="print-notes">' . Mage::getStoreConfig('general/store_information/address', $storeId) . 
                    '</span>'
                ), $notes);
            $notes = strip_tags($notes);
        }

        $stringArray = $this->processString($notes, $font, 8, 240);
        for ($i = 0; $i < count($stringArray); $i++) {
            $textbox = imageftbbox(8, 0, $font, $stringArray[$i]);
            imagefttext($img1, 8, 0, 590 - ($textbox[2] - $textbox[0]), $codeY + 58, $textColor, $font, 
                $stringArray[$i]);
            $codeY += 18.5;
        }
        /* End */

        /* Insert Logo to Image */
        if ($img3) {
            $widthLogo = imagesx($img3);
            $heightLogo = imagesy($img3);
            imagecopyresampled($img2, $img3, 13, 0, 0, 0, $widthLogo, $heightLogo, $widthLogo, $heightLogo);
        }


        /* Insert Backgound Value Image */
        imagecopyresampled($img5, $img6, 381, 0, 0, 0, 219, 52, 219, 52);

        /* Insert Background Title Image */
        imagecopyresampled($img2, $img5, 0, 138, 0, 0, 600, 52, 600, 52);

        /* Draw Images */
        imagecopyresampled($img, $img2, 0, 0, 0, 0, 600, 190, 600, 190);


        /* Draw Background */
        imagecopyresampled($img, $img1, 0, 190, 0, 0, 600, 175, 600, 175);

        imagepng($img, $imgFile);
        imagedestroy($img);
    }

    /**
     * Draw the center template of Gift Card
     * @param $giftcode
     * @param $giftcardTemplate
     * @throws Mage_Core_Exception
     */
    public function generateCenterImage($giftcode, $giftcardTemplate)
    {

        $storeId = Mage::app()->getStore()->getId();
        $images = $this->getImagesInFolder($giftcode['gift_code']);
        if (isset($images[0]) && file_exists($images[0])) {
            unlink($images[0]);
        }

        $imageSuffix = Mage::getModel('core/date')->timestamp(now());

        $imgFile = $this->getImgDir($giftcode['gift_code']) . $giftcode['gift_code'] . '-' . $imageSuffix . '.png';
        $w = 600;
        $h = 365;

        $img = imagecreatetruecolor($w, $h);
        $textColor = $this->hexColorAllocate($img, $giftcardTemplate['text_color']);
        $styleColor = $this->hexColorAllocate($img, $giftcardTemplate['style_color']);
        $bgColor = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $w - 1, $h - 1, $bgColor);

        // if (isset($giftcode['giftcard_template_image']) && $giftcode['giftcard_template_image'] != null)
        $img2 = $this->createGCImage($giftcode['giftcard_template_image']);

        $img3 = $this->createGCLogo();

        $img4 = $this->createGMessageBox();

        $img5 = $this->createGCBackground('bkg-title.png');

        $img6 = $this->createGCBackground('bkg-value.png');

        $x = 10;
        $y = 33;
        $fsize = 15;
        $font = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-Semibold.ttf';

        /* Draw Expiry Date */
        if (Mage::helper('giftvoucher')->getGeneralConfig('show_expiry_date')) {
            if ($giftcode['expired_at']) {
                $expiryDate = Mage::helper('giftvoucher')->__('Expired: ') . 
                    date('m/d/Y', strtotime($giftcode['expired_at']));
                $textbox = imageftbbox(9, 0, $font, $expiryDate);
                imagefttext($img2, 9, 0, (580 - ($textbox[2] - $textbox[0])), 25, 
                    imagecolorallocate($img, 255, 255, 255), $font, $expiryDate);
            }
        }


        /* Draw Text */
        $fsize = 15;
        $font = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-Semibold.ttf';
        $word = $giftcardTemplate['caption'];
        $textbox = imageftbbox($fsize, 0, $font, $word);

        $word = $this->processTitle($word, $font, $fsize, 370);
        // Chieu dai cua textbox: $textbox[2] - $textbox[0]
        // Chieu rong cua textbox: $textbox[7] - $textbox[1]

        imagefttext($img5, $fsize, 0, $x, $y, $styleColor, $font, $word);

        /* Print Value */

        $fontPrice = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-ExtraBold.ttf';
        $fsizePrice = 14;

        $price = Mage::getModel('directory/currency')->setData('currency_code', $giftcode['currency'])
            ->format($giftcode['balance'], array('display' => Zend_Currency::USE_SYMBOL), false);

        $textbox = imageftbbox($fsizePrice, 0, $fontPrice, $price);
        $valueX = $textbox[2] - $textbox[0];
        imagefttext($img6, $fsizePrice, 0, 210 - $valueX, $y + 3, $styleColor, $font, $price);

        $fsizeValue = 9;
        $textbox = imageftbbox($fsizeValue, 0, $fontPrice, Mage::helper('giftvoucher')->__('Value '));
        $valueX += $textbox[2] - $textbox[0] + 15;
        imagefttext($img6, $fsizeValue, 0, 210 - $valueX, $y, $styleColor, $font, 
            Mage::helper('giftvoucher')->__('Value '));

        /* Print "From:" and "To: " */

        $y += 135;
        $textbox = imageftbbox($fsize, 0, $font, Mage::helper('giftvoucher')->__('From: '));
        imagefttext($img2, 13, 0, 15, $y - 5, $textColor, $font, Mage::helper('giftvoucher')->__('From: '));
        imagefttext($img2, 13, 0, $x + ($textbox[2] - $textbox[0]), $y - 5, $styleColor, $font, 
            $giftcode['customer_name']);
        $y -= 1.55 * ($textbox[7] - $textbox[1]);

        $textbox = imageftbbox($fsize, 0, $font, Mage::helper('giftvoucher')->__('To: '));
        imagefttext($img2, 13, 0, 15, $y, $textColor, $font, Mage::helper('giftvoucher')->__('To: '));
        imagefttext($img2, 13, 0, $x + ($textbox[2] - $textbox[0]), $y, $styleColor, $font, 
            $giftcode['recipient_name']);
        $y -= 1.55 * ($textbox[7] - $textbox[1]);

        /* Print Gift Code */

        $fontCode = Mage::getBaseDir('lib') . DS . 'Magestore' . DS . 'fonts' . DS . 'OpenSans-SemiboldItalic.ttf';
        $codeY = 160;
        $textbox = imageftbbox(11, 0, $fontCode, $giftcode['gift_code']);
        imagefttext($img2, 11, 0, 590 - ($textbox[2] - $textbox[0]), $codeY, $styleColor, $fontCode, 
            $giftcode['gift_code']);
        $codeY -= ($textbox[7] - $textbox[1]);

        /* Print Barcode */

        $barcode = Mage::helper('giftvoucher')->getGeneralConfig('barcode_enable');
        if ($barcode) {
            $newImgBarcode = $this->resizeBarcodeImage($giftcode['gift_code']);
            $newImgBarcodeX = imagesx($newImgBarcode);
            $newImgBarcodeY = imagesy($newImgBarcode);
            imagecopyresampled($img2, $newImgBarcode, 590 - $newImgBarcodeX, $codeY - 5, 0, 0, 
                $newImgBarcodeX, $newImgBarcodeY, $newImgBarcodeX, $newImgBarcodeY);
        }

        /* Print Customers' s messages */

        $xMessage = 5;
        $yMessage = 15;

        if (isset($giftcode['message']) && $giftcode['message'] != null) {
            $message = $giftcode['message'];
        } else {
            $message = '';
        }

        $stringArray = $this->processString($message, $font, 9, 568);

        for ($i = 0; $i < count($stringArray); $i++) {
            imagefttext($img4, 9, 0, $xMessage, $yMessage, $textColor, $font, $stringArray[$i]);
            $yMessage -= 1.25 * ($textbox[7] - $textbox[1]);
        }

        imagecopyresampled($img2, $img4, 16, $y + 5, 0, 0, 568, 97, 568, 97);

        /* Print Notes */

        $y += 110;

        if (isset($giftcardTemplate['notes']) && $giftcardTemplate['notes'] != null) {
            $notes = $giftcardTemplate['notes'];
        } else {
            $notes = Mage::getStoreConfig('giftvoucher/print_voucher/note', $storeId);
            $notes = str_replace(array(
                '{store_url}',
                '{store_name}',
                '{store_address}'
                ), array(
                '<span class="print-notes">' . Mage::app()->getStore($storeId)->getBaseUrl() . '</span>',
                '<span class="print-notes">' . Mage::app()->getStore($storeId)->getFrontendName() . '</span>',
                '<span class="print-notes">' . Mage::getStoreConfig('general/store_information/address', $storeId) . 
                    '</span>'
                ), $notes);
            $notes = strip_tags($notes);
        }

        $stringArray = $this->processString($notes, $font, 9, 570);
        for ($i = 0; $i < count($stringArray); $i++) {
            imagefttext($img2, 9, 0, 16, $y + 10, $textColor, $font, $stringArray[$i]);
            $y -= 1.55 * ($textbox[7] - $textbox[1]);
        }

        /* End */

        /* Insert Logo to Image */
        if ($img3) {
            $widthLogo = imagesx($img3);
            $heightLogo = imagesy($img3);
            imagecopyresampled($img2, $img3, 13, 0, 0, 0, $widthLogo, $heightLogo, $widthLogo, $heightLogo);
        }


        /* Insert Backgound Value Image */
        imagecopyresampled($img5, $img6, 381, 0, 0, 0, 219, 52, 219, 52);

        /* Insert Background Title Image */
        imagecopyresampled($img2, $img5, 0, 85, 0, 0, 600, 52, 600, 52);

        /* Draw Images */
        imagecopyresampled($img, $img2, 0, 0, 0, 0, 600, 365, 600, 365);

        imagepng($img, $imgFile);
        imagedestroy($img);
    }

    /**
     * Draw message to Image
     *
     * @param string $txt
     * @param string $font
     * @param int $fsize
     * @param int $widthBackground
     * @return array
     */
    public function processString($txt, $font, $fsize, $widthBackground)
    {

        $txtArr = explode("\n", $txt);
        $count = 0;
        $result = array();
        for ($j = 0; $j < count($txtArr); $j++) {
            $box = imageftbbox($fsize, 0, $font, $txtArr[$j]);
            $txtLength = $box[2] - $box[0];

            if ($txtLength < $widthBackground) {
                $result[$count] = $txtArr[$j];
            } else {
                $strArr = explode(" ", $txtArr[$j]);
                $length = 0;
                $string = imageftbbox($fsize, 0, $font, ' ');
                $inc = $string[2] - $string[0];

                for ($i = 0; $i < count($strArr); $i++) {
                    if ($strArr[$i] == '') {
                        $strLength = 1;
                    } else {
                        $textbox = imageftbbox($fsize, 0, $font, $strArr[$i]);
                        $strLength = $textbox[2] - $textbox[0] + $inc;
                    }

                    if ($strLength > ($widthBackground - 6 * $inc)) {
                        $count ++;
                        $length = $strLength;
                        $strArr[$i] = $this->processTitle($strArr[$i], $font, $fsize, $widthBackground);
                    } else {
                        $length += $strLength;

                        if ($length > ($widthBackground - 6 * $inc)) {
                            $count ++;
                            $length = $strLength;
                        }
                    }
                    if (!isset($result[$count])) {
                        $result[$count] = '';
                    }

                    $result[$count] .= $strArr[$i] . ' ';
                }
            }
            $count ++;
        }
        return $result;
    }

    /**
     * Draw title to Image
     *
     * @param string $txt
     * @param string $font
     * @param int $fsize
     * @param int $widthBackground
     * @return array
     */
    public function processTitle($txt, $font, $fsize, $widthBackground)
    {

        $box = imageftbbox($fsize, 0, $font, $txt);
        $txtLength = $box[2] - $box[0];
        $string = imageftbbox($fsize, 0, $font, ' ');
        $inc = $string[2] - $string[0];

        if ($txtLength < $widthBackground) {
            $result = $txt;
        } else {
            $length = 0;
            $result = '';

            for ($i = 0; $i < strlen($txt); $i++) {
                $textbox = imageftbbox($fsize, 0, $font, $txt[$i]);
                $strLength = $textbox[2] - $textbox[0];
                $length += $strLength;

                if ($length >= ($widthBackground - 6 * $inc)) {
                    break;
                }

                $result .= $txt[$i];
            }
        }

        return $result;
    }

    /**
     * Convert Image object
     * @param $filename
     * @return resource
     * @throws Mage_Exception
     */
    public function imagecreatefromfile($filename)
    {
        if (!file_exists($filename)) {
            throw new Mage_Exception('File "' . $filename . '" not found.');
        }
        switch (strtolower(pathinfo($filename, PATHINFO_EXTENSION))) {
            case 'jpeg':
            case 'jpg':
                return imagecreatefromjpeg($filename);
                break;

            case 'png':
                return imagecreatefrompng($filename);
                break;

            case 'gif':
                return imagecreatefromgif($filename);
                break;

            default:
                throw new Mage_Exception('File "' . $filename . '" is not valid jpg, png or gif image.');
                break;
        }
    }

    /**
     * Create a Gift Card image object
     * @param $filename
     * @param null $type
     * @return resource
     * @throws Mage_Exception
     */
    public function createGCImage($filename, $type = null)
    {
        if (isset($type) && $type != null) {
            $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'images' . DS . 
                $type . DS . $filename;
        } else {
            $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'images' . DS . $filename;
        }

        if (($filename == null) || (!file_exists($dir))) {
            $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . 'default.png';
        }

        return $this->imagecreatefromfile($dir);
    }

    /**
     * Create a Gift Card background object
     * @param $filename
     * @param null $type
     * @param null $giftcode
     * @return resource
     * @throws Mage_Exception
     */
    public function createGCBackground($filename, $type = null, $giftcode = null)
    {
        if ($filename) {
            if (isset($type) && $type != null) {
                $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'background' . 
                    DS . $type . DS . $filename;
            } else {
                $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'background' . 
                    DS . $filename;
            }
        } else {
            if (isset($type) && $type == 'simple') {
                if (isset($giftcode['message']) && $giftcode['message'] != null) {
                    $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'background' . 
                        DS . 'simple' . DS . 'bg_message.png';
                } else {
                    $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'background' . 
                        DS . 'simple' . DS . 'bg_none.png';
                }
            } else {
                $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . 'default.png';
            }
        }

        return $this->imagecreatefromfile($dir);
    }

    /**
     * Create a Gift Card logo object
     * @param null $type
     * @return bool|resource
     * @throws Mage_Exception
     */
    public function createGCLogo($type = null)
    {
        $storeId = Mage::app()->getStore()->getId();
        $image = Mage::getStoreConfig('giftvoucher/print_voucher/logo', $storeId);
        if ($image) {
            $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'pdf' . DS . 'logo' . 
                DS . str_replace("/", DS, $image);
            $imgLogo = $this->imagecreatefromfile($dir);
            
            if ($type == 'simple') {
                $ratio = imagesx($imgLogo) / imagesy($imgLogo);  
                $resizeLogoUrl = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . 'logo' . 
                    DS . 'simple' .DS . str_replace("/", DS, $image);
                if ($ratio <= (200 / 120) && $ratio >= 1) {                    
                    $newHeight = round(180 * imagesy($imgLogo) / imagesx($imgLogo));
                    if (!is_file($resizeLogoUrl)) {
                        $resizeLogoObj = new Varien_Image($dir);
                        $resizeLogoObj->constrainOnly(TRUE);
                        $resizeLogoObj->keepAspectRatio(TRUE);
                        $resizeLogoObj->keepFrame(false);
                        $resizeLogoObj->keepTransparency(true);
                        $resizeLogoObj->resize(180, $newHeight);
                        $resizeLogoObj->save($resizeLogoUrl);
                    }                    
                } else {
                    $newWidth = round(90 * imagesx($imgLogo) / imagesy($imgLogo));
                    if (!is_file($resizeLogoUrl)) {
                        $resizeLogoObj = new Varien_Image($dir);
                        $resizeLogoObj->constrainOnly(TRUE);
                        $resizeLogoObj->keepAspectRatio(TRUE);
                        $resizeLogoObj->keepFrame(false);
                        $resizeLogoObj->keepTransparency(true);
                        $resizeLogoObj->resize($newWidth, 90);
                        $resizeLogoObj->save($resizeLogoUrl);
                    }
                }
            } else {
                $newWidth = round(63 * imagesx($imgLogo) / imagesy($imgLogo));               
                $resizeLogoUrl = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . 'logo' . 
                    DS . str_replace("/", DS, $image);
                
                if (!is_file($resizeLogoUrl)) {
                    $resizeLogoObj = new Varien_Image($dir);
                    $resizeLogoObj->constrainOnly(TRUE);
                    $resizeLogoObj->keepAspectRatio(TRUE);
                    $resizeLogoObj->keepFrame(false);
                    $resizeLogoObj->keepTransparency(true);
                    $resizeLogoObj->resize($newWidth, 63);
                    $resizeLogoObj->save($resizeLogoUrl);
                }
            }
            
            return $this->imagecreatefromfile($resizeLogoUrl);
        } else {
            return false;
        }
    }

    /**
     * Create a Gift Card message image object
     * @param null $type
     * @return resource
     * @throws Mage_Exception
     */
    public function createGMessageBox($type = null)
    {
        if (isset($type) && $type != null) {
            $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'messagebox' . 
                DS . $type . DS . 'default.png';
        } else {
            $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'messagebox' . 
                DS . 'default.png';
        }
        return $this->imagecreatefromfile($dir);
    }

    /**
     * Resize Barcode image
     * @param $code
     * @return resource
     */
    public function resizeBarcodeImage($code)
    {
        $barcode = Mage::helper('giftvoucher')->getGeneralConfig('barcode_enable');
        $barcodeType = Mage::helper('giftvoucher')->getGeneralConfig('barcode_type');

        if ($barcodeType == 'code128') {
            $barcodeUrl = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'barcode' . 
                DS . $code . '.png';

            $resizeBarcodeUrl = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . $code . 
                DS . 'barcode.png';
            $resizeBarcodeObj = new Varien_Image($barcodeUrl);
            $resizeBarcodeObj->constrainOnly(TRUE);
            $resizeBarcodeObj->keepAspectRatio(TRUE);
            $resizeBarcodeObj->keepFrame(false);
            $resizeBarcodeObj->resize(180, 40);
            $resizeBarcodeObj->save($resizeBarcodeUrl);

            return imagecreatefrompng($resizeBarcodeUrl);
        } else {
            $qr = new Magestore_Giftvoucher_QRCode($code);
            $content = file_get_contents($qr->getResult());
            $fileName = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . $code . DS . 'qrcode.png';
            file_put_contents($fileName, $content);

            $resizeBarcodeObj = new Varien_Image($fileName);
            $resizeBarcodeObj->constrainOnly(TRUE);
            $resizeBarcodeObj->keepAspectRatio(TRUE);
            $resizeBarcodeObj->keepFrame(false);
            $resizeBarcodeObj->resize(180, 40);
            $resizeBarcodeObj->save($fileName);

            return imagecreatefrompng($fileName);
        }
    }

    /**
     * Allocate color for an image
     * @param $img
     * @param $hex
     * @return int
     */
    public function hexColorAllocate($img, $hex)
    {
        $hex = ltrim($hex, '#');
        $a = hexdec(substr($hex, 0, 2));
        $b = hexdec(substr($hex, 2, 2));
        $c = hexdec(substr($hex, 4, 2));
        return imagecolorallocate($img, $a, $b, $c);
    }

    /**
     * Get the directory of gift code image
     * 
     * @param string $code
     * @return string
     */
    public function getImagesInFolder($code)
    {
        $directory = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . $code . DS;
        return glob($directory . $code . "*.png");
    }

    /**
     * Get the directory of gift code image
     *
     * @param string $code
     * @return string
     */
    public function getBarcodeInFolder($code)
    {
        $directory = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'draw' . DS . $code . DS;
        return glob($directory . "barcode.png");
    }

    /**
     * @param $int
     * @param $image
     * @throws Mage_Exception
     */
    public function columnImage($int, $image)
    {
        $dir = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'background' . 
            DS . 'simple' . DS . 'line.png';
        $lineImage = $this->imagecreatefromfile($dir);
        
        for ($i = 0; $i < $int; $i++) {
            $x = 600 * ($i + 1) / $int;
            /* Draw Lines */
            imagecopyresampled($image, $lineImage, $x, 333, 0, 0, 1, 90, 1, 90);
        }
    }

}
