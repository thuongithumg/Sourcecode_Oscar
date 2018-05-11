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

class Magestore_Giftvoucher_Helper_Barcode extends Mage_Core_Helper_Data
{
    /* default barcode config value */
    const SYMBOLOGY = 'code128';
    const FONT_SIZE = 16;
    const HEIGHT = 0;
    const WIDTH = 0;
    const IMAGE_TYPE = 'png';
    const DRAW_TEXT = false;

    /**
     * Generate source of barcode image
     *
     * @param string $barcodeString
     * @param array $config
     *
     * @return string
     */
    public function getBarcodeSource($barcodeString, $config = [])
    {
        $symbology = isset($config['symbology']) ? $config['symbology'] : self::SYMBOLOGY;
        $fontSize = isset($config['font_size']) ? $config['font_size'] : self::FONT_SIZE;
        $height = isset($config['height']) ? $config['height'] : self::HEIGHT;
        $width = isset($config['width']) ? $config['width'] : self::WIDTH;
        $imageType = isset($config['image_type']) ? $config['image_type'] : self::IMAGE_TYPE;
        $fontSize = isset($config['font_size']) ? $config['font_size'] : self::FONT_SIZE;
        $drawText = isset($config['drawText']) ? $config['drawText'] : self::DRAW_TEXT;

        $barcodeOptions = [
            'text' => $barcodeString,
            'fontSize' => $fontSize,
            'drawText' => $drawText
        ];
        $rendererOptions = [
            'width' => $width,
            'height' => $height,
            'imageType' => $imageType
        ];

        $source = \Zend_Barcode::factory(
            $symbology, 'image', $barcodeOptions, $rendererOptions
        );

        ob_start();
        imagepng($source->draw());
        $barcode = ob_get_contents();
        ob_end_clean();

        return base64_encode($barcode);
    }

    /**
     * Get barcode source in png image format
     *
     * @param string $barcodeString
     * @param array $config
     * @return string
     */
    public function getBarcodeImageSource($barcodeString, $config = [])
    {
        if (Mage::helper('giftvoucher')->getGeneralConfig('barcode_enable')) {
            if (Mage::helper('giftvoucher')->getGeneralConfig('barcode_type') == 'code128') {
                return 'data:image/png;base64,' . $this->getBarcodeSource($barcodeString, $config);
            } else {
                return 'data:image/png;base64,' . $this->getQrcodeSource($barcodeString);
            }
        } else {
            return null;
        }
    }

    public function getQrcodeSource($barcodeString)
    {
        $qr = new Magestore_Giftvoucher_QRCode($barcodeString);
        $content = file_get_contents($qr->getResult());
        return base64_encode($content);
    }

}
