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
 *   @category    Magestore
 *   @package     Magestore_Barcodesuccess
 *   @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 *   @license     http://www.magestore.com/license-agreement.html
 *
 *
 */

/**
 * Class Magestore_Barcodesuccess_Model_Template
 */
class Magestore_Barcodesuccess_Model_Template extends
    Mage_Core_Model_Abstract
{

    const TEMPLATE_ID      = 'template_id';
    const TYPE             = 'type';
    const NAME             = 'name';
    const PRIORITY         = 'priority';
    const STATUS           = 'status';
    const SYMBOLOGY        = 'symbology';
    const EXTRA_FIELD      = 'extra_field';
    const MEASUREMENT_UNIT = 'measurement_unit';
    const LABEL_PER_ROW    = 'label_per_row';
    const PAPER_WIDTH      = 'paper_width';
    const PAPER_HEIGHT     = 'paper_height';
    const LABEL_WIDTH      = 'label_width';
    const LABEL_HEIGHT     = 'label_height';
    const FONT_SIZE        = 'font_size';
    const TOP_MARGIN       = 'top_margin';
    const LEFT_MARGIN      = 'left_margin';
    const RIGHT_MARGIN     = 'right_margin';
    const BOTTOM_MARGIN    = 'bottom_margin';

    /*add by Peter*/
    const ATTRIBUTE_SHOW_ON_BARCODE  = 'product_attribute_show_on_barcode';
    const ROTATE    = 'rotate';
    /*end by Peter*/


    public function _construct()
    {
        parent::_construct();
        $this->_init('barcodesuccess/template');
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setTemplateId( $value )
    {
        return $this->setData(self::TEMPLATE_ID, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setType( $value )
    {
        return $this->setData(self::TYPE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setName( $value )
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setBarcodeId( $value )
    {
        return $this->setData(self::PRIORITY, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setStatus( $value )
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setSymbology( $value )
    {
        return $this->setData(self::SYMBOLOGY, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setExtraField( $value )
    {
        return $this->setData(self::EXTRA_FIELD, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setMeasurementUnit( $value )
    {
        return $this->setData(self::MEASUREMENT_UNIT, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setLabelPerRow( $value )
    {
        return $this->setData(self::LABEL_PER_ROW, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setPaperWidth( $value )
    {
        return $this->setData(self::PAPER_WIDTH, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setPaperHeight( $value )
    {
        return $this->setData(self::PAPER_HEIGHT, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setLabelWidth( $value )
    {
        return $this->setData(self::LABEL_WIDTH, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setLabelHeight( $value )
    {
        return $this->setData(self::LABEL_HEIGHT, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setFontSize( $value )
    {
        return $this->setData(self::FONT_SIZE, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setTopMargin( $value )
    {
        return $this->setData(self::TOP_MARGIN, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setLeftMargin( $value )
    {
        return $this->setData(self::LEFT_MARGIN, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setRightMargin( $value )
    {
        return $this->setData(self::RIGHT_MARGIN, $value);
    }

    /**
     * @param $value
     * @return Varien_Object
     */
    public function setBottomMargin( $value )
    {
        return $this->setData(self::BOTTOM_MARGIN, $value);
    }


    /**
     * @param $value
     * @return Varien_Object
     */
    public function setProductAttributeShowOnBarcode()
    {
        return $this->setData(self::ATTRIBUTE_SHOW_ON_BARCODE);
    }


    /**
     * @param $value
     * @return Varien_Object
     */
    public function setRotate()
    {
        return $this->setData(self::ROTATE);
    }


    /**
     * @return string
     */
    public function getTemplateId()
    {
        return $this->getData(self::TEMPLATE_ID);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @return string
     */
    public function getBarcodeId()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @return string
     */
    public function getSymbology()
    {
        return $this->getData(self::SYMBOLOGY);
    }

    /**
     * @return string
     */
    public function getExtraField()
    {
        return $this->getData(self::EXTRA_FIELD);
    }

    /**
     * @return string
     */
    public function getMeasurementUnit()
    {
        return $this->getData(self::MEASUREMENT_UNIT);
    }

    /**
     * @return string
     */
    public function getLabelPerRow()
    {
        return $this->getData(self::LABEL_PER_ROW);
    }

    /**
     * @return string
     */
    public function getPaperWidth()
    {
        return $this->getData(self::PAPER_WIDTH);
    }

    /**
     * @return string
     */
    public function getPaperHeight()
    {
        return $this->getData(self::PAPER_HEIGHT);
    }

    /**
     * @return string
     */
    public function getLabelWidth()
    {
        return $this->getData(self::LABEL_WIDTH);
    }

    /**
     * @return string
     */
    public function getLabelHeight()
    {
        return $this->getData(self::LABEL_HEIGHT);
    }

    /**
     * @return string
     */
    public function getFontSize()
    {
        return $this->getData(self::FONT_SIZE);
    }

    /**
     * @return string
     */
    public function getTopMargin()
    {
        return $this->getData(self::TOP_MARGIN);
    }

    /**
     * @return string
     */
    public function getLeftMargin()
    {
        return $this->getData(self::LEFT_MARGIN);
    }

    /**
     * @return string
     */
    public function getRightMargin()
    {
        return $this->getData(self::RIGHT_MARGIN);
    }

    /**
     * @return string
     */
    public function getBottomMargin()
    {
        return $this->getData(self::BOTTOM_MARGIN);
    }


    /**
     * @return string
     */
    public function getProductAttributeShowOnBarcode()
    {
        return $this->getData(self::ATTRIBUTE_SHOW_ON_BARCODE);
    }

    /**
     * @return string
     */
    public function getRotate()
    {
        return $this->getData(self::ROTATE);
    }

}