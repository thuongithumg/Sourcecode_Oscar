<?php
/**
 *
 *  Magestore
 *   NOTICE OF LICENSE
 *
 *   This source file is subject to the Magestore.com license that is
 *   available through the world-wide-web at this URL:
 *   http://www.magestore.com/license-agreement.html
 *
 *   DISCLAIMER
 *
 *   Do not edit or add to this file if you wish to upgrade this extension to newer
 *   version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Barcodesuccess_Block_Barcode_Template
 */
class Magestore_Barcodesuccess_Block_Barcode_Template extends
    Mage_Core_Block_Template
{

    /**
     * @return Mage_Core_Block_Abstract
     */
    public function _prepareLayout()
    {
        $this->setTemplate('barcodesuccess/barcode/template.phtml');
        return parent::_prepareLayout();
    }

    /**
     * @return array
     */
    public function getBarcodes()
    {
        $datas = array();
        if ( $this->getData('barcodes') ) {
            $datas = $this->getData('barcodes');
        }
        $barcodes = array();
        if ( $datas ) {
            $template = $this->getTemplateData();
            foreach ( $datas as $data ) {
                if ( empty($data['qty']) ) {
                    $data['qty'] = $template['label_per_row'];
                }
                for ( $i = 1; $i <= $data['qty']; $i++ ) {
                    $barcodes[] = $this->getBarcodeSource($data);
                }
            }
        }
        return $barcodes;
    }

    /**
     * @param $data
     * @return string
     */
    public function getBarcodeSource( $data )
    {
        $source = "";
        $result = array();
        if ( $data ) {
            $template        = $this->getTemplateData();
            $type            = $template['symbology'];
            $barcodeOptions  = array(
                'text'     => $data['barcode'],
                'fontSize' => $template['font_size'],
                //                'barHeight' => 7,
                //                'factor'    => 7
            );
            $rendererOptions = array(
                //'width' => '198',
                'height'    => '0',
                'imageType' => 'png',
            );
            $source          = \Zend_Barcode::factory(
                $type, 'image', $barcodeOptions, $rendererOptions
            );

            if(isset($template['product_attribute_show_on_barcode'])){
                $attributeDatas = $this->getAttributeSoucre($data['product_id'],$template['product_attribute_show_on_barcode']);
            }else{
                $attributeDatas = array();
            }
        }
        $result['attribute_data'] = $attributeDatas;
        $result['barcode_source'] = $source;
        return $result;
    }

    public function getAttributeSoucre($product_id , $attributes){
        $attributeArray = array();
        if($product_id && $attributes && $attributes != ''){
            if(is_array($attributes)){
            $array = explode(',' ,$attributes[0]);
            }else{
                $array = explode(',' ,$attributes);
            }
            $prod = Mage::getModel('catalog/product')->load($product_id);
            foreach($array as $key){
                if( $key && ($text = ($prod->getAttributeText($key) ? $prod->getAttributeText($key) : $prod->getData($key))))
                {
                    if( ($key ==='sku') ||($key ==='name') ){
                        $attributeArray[] =  (is_numeric($text) ? (int)$text : $text);
                    }elseif(($key ==='price')){
                        $price = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->toCurrency($text);
                        $attributeArray[] = '[' . $key . '] ' . $price;
                    } else {
                        $attributeArray[] = '[' . $key . '] ' . (is_numeric($text) ? (int)$text : $text);
                    }
                }
            }
        }
        return $attributeArray;
    }

    /**
     * @return string
     */
    public function getTemplateData()
    {
        $data = array();
        if ( $this->getData('template_data') ) {
            $data = $this->getData('template_data');
        } elseif ( $this->getTemplateId() ) {
            $template = Mage::getModel('barcodesuccess/template')->load($this->getTemplateId());
            if ( $template->getId() ) {
                $data = $template->getData();
            }
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::FONT_SIZE]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::FONT_SIZE] = '24';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::LABEL_PER_ROW]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::LABEL_PER_ROW] = '1';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::MEASUREMENT_UNIT]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::MEASUREMENT_UNIT] = Magestore_Barcodesuccess_Model_Source_Template_Measurement::MM;
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::PAPER_HEIGHT]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::PAPER_HEIGHT] = '30';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::PAPER_WIDTH]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::PAPER_WIDTH] = '100';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::LABEL_HEIGHT]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::LABEL_HEIGHT] = '30';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::LABEL_WIDTH]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::LABEL_WIDTH] = '100';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::LEFT_MARGIN]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::LEFT_MARGIN] = '0';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::RIGHT_MARGIN]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::RIGHT_MARGIN] = '0';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::BOTTOM_MARGIN]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::BOTTOM_MARGIN] = '0';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::TOP_MARGIN]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::TOP_MARGIN] = '0';
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::SYMBOLOGY]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::SYMBOLOGY] = Magestore_Barcodesuccess_Model_Source_Template_Symbology::CODE_128;
        }
        if ( empty($data[Magestore_Barcodesuccess_Model_Template::TYPE]) ) {
            $data[Magestore_Barcodesuccess_Model_Template::TYPE] = Magestore_Barcodesuccess_Model_Source_Template_Type::TYPE_STANDARD;
        }
        return $data;
    }

    /**
     * @return bool
     */
    public function isJewelry()
    {
        $template = $this->getTemplateData();
        return ($template['type'] == Magestore_Barcodesuccess_Model_Source_Template_Type::TYPE_JEWELRY) ? true : false;
    }

}
