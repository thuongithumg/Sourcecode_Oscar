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
 * Giftvoucher Pdf Giftcard Model
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Model_Pdf_Giftcard extends Varien_Object
{

    /**
     * Return PDF document
     *
     * @param  array $giftvoucherIds
     * @return Zend_Pdf
     */
    public function getPdf($giftvoucherIds)
    {
        if ($giftvoucherIds) {
            $pdf = new Zend_Pdf();
            $this->_setPdf($pdf);
            $style = new Zend_Pdf_Style();
            $this->_setFontBold($style, 10);

            $giftvoucherIds = array_chunk($giftvoucherIds, 3);


            foreach ($giftvoucherIds as $giftvouchers) {
                $page = $pdf->newPage(Zend_Pdf_Page::SIZE_A4);
                $pdf->pages[] = $page;
                $this->y = 790;
                $i = 0;
                foreach ($giftvouchers as $giftvoucherId) {
                    $giftvoucher = Mage::getModel('giftvoucher/giftvoucher')->load($giftvoucherId);
                    $gifttemplate = Mage::getModel('giftvoucher/gifttemplate')->load($giftvoucher['giftcard_template_id']);
                    
                    // resize the width image to 300px
                    if ($gifttemplate && $gifttemplate['design_pattern'] != 4) {
                        if ($giftvoucher->getId()) {
                            $newImgWidth = ($page->getWidth() - 300) / 2;
                            $newImgHeight = 183;

                            $images = Mage::helper('giftvoucher/drawgiftcard')
                                ->getImagesInFolder($giftvoucher['gift_code']);
                            if (isset($images[0]) && is_file($images[0])) {
                                $image = Zend_Pdf_Image::imageWithPath($images[0]);
                                $page->drawImage($image, $newImgWidth, $this->y - 183, $newImgWidth + 300, $this->y);
                            }
                        }
                        $temp = $this->y - 200;
                    } else {                        
                        if ($giftvoucher->getId()) {
                            $newImgWidth = ($page->getWidth() - 300) / 2;
                            $images = Mage::helper('giftvoucher/drawgiftcard')
                                ->getImagesInFolder($giftvoucher['gift_code']);
                            if ($giftvoucher['message'] && $giftvoucher['message'] != '') {
                                $newImgHeight = 265;
                                if (isset($images[0]) && is_file($images[0])) {
                                    $image = Zend_Pdf_Image::imageWithPath($images[0]);
                                    $page->drawImage($image, $newImgWidth, $this->y - 265, $newImgWidth + 300, $this->y);
                                }
                                $temp = $this->y - 280;
                            } else {
                                $newImgHeight = 219;
                                if (isset($images[0]) && is_file($images[0])) {
                                    $image = Zend_Pdf_Image::imageWithPath($images[0]);
                                    $page->drawImage($image, $newImgWidth, $this->y - 219, $newImgWidth + 300, $this->y);
                                }
                                $temp = $this->y - 240;
                            }                                                
                        }                        
                    }
                }
            }
        }
        return $pdf;
    }

    /**
     * Before getPdf processing
     */
    protected function _beforeGetPdf()
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);
    }

    /**
     * After getPdf processing
     */
    protected function _afterGetPdf()
    {
        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(true);
    }

    /**
     * Set PDF object
     *
     * @param  Zend_Pdf $pdf
     * @return Magestore_Giftvoucher_Model_Pdf_Giftvoucher
     */
    protected function _setPdf(Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }

    /**
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertineC_Re-2.8.0.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as bold
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontBold($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Bd-2.8.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }

    /**
     * Set font as italic
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontItalic($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_It-2.8.2.ttf');
        $object->setFont($font, $size);
        return $font;
    }

}
