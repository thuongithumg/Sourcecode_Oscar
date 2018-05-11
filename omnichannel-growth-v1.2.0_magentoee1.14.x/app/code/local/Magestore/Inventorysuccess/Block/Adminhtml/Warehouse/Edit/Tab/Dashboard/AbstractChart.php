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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Warehouse Edit Abstract Chart Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_Dashboard_AbstractChart extends Mage_Core_Block_Template
{
    /**
     * @var string
     */
    protected $_template = 'inventorysuccess/warehouse/edit/tab/dashboard/columnchart.phtml';

    /**
     * @var string
     */
    protected $containerId;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $subtitle = '';

    /**
     * @var string
     */
    protected $xAxisTitle = '';

    /**
     * @var string
     */
    protected $yAxisTitle = '';

    /**
     * @var string
     */
    protected $tooltip = '';

    /**
     * @var array
     */
    protected $seriesName = array();

    /**
     * @var array
     */
    protected $seriesType = array();

    /**
     * @var array
     */
    protected $seriesData = array();

    /**
     * @var array
     */
    protected $seriesDataLabel = array();

    /**
     * @var array
     */
    protected $localeFormat;

    /**
     * Initialize factory instance
     *
     * @param array $args
     */
    public function __construct(array $args)
    {
        parent::__construct($args);
        $this->localeFormat = Mage::app()->getLocale()->getJsPriceFormat();
        $this->setTitle('Abstract Chart');
    }

    /**
     * Preparing global layout
     *
     * You can redefine this method in child classes for changing layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
    }

    /**
     * set container id
     *
     * @param array $data
     * @return $this
     */
    public function setContainerId($containerId){
        $this->containerId = $containerId;
        return $this;
    }

    /**
     * get container id
     *
     * @return string
     */
    public function getContainerId(){
        return $this->containerId;
    }

    /**
     * set chart title
     *
     * @param string $title
     * @return $this
     */
    public function setTitle($title = ''){
        $this->title = $title;
        return $this;
    }

    /**
     * get chart title
     *
     * @return string
     */
    public function getTitle(){
        return $this->title;
    }

    /**
     * set chart subtitle
     *
     * @param string $subtitle
     * @return $this
     */
    public function setSubtitle($subtitle = ''){
        $this->subtitle = $subtitle;
        return $this;
    }

    /**
     * get chart subtitle
     *
     * @return string
     */
    public function getSubtitle(){
        return $this->subtitle;
    }

    /**
     * set X axis title
     *
     * @param string $title
     * @return $this
     */
    public function setXAxisTitle($title = ''){
        $this->xAxisTitle = $title;
        return $this;
    }

    /**
     * get X axis title
     *
     * @return string
     */
    public function getXAxisTitle(){
        return $this->xAxisTitle;
    }

    /**
     * set Y axis title
     *
     * @param string $title
     * @return $this
     */
    public function setYAxisTitle($title = ''){
        $this->yAxisTitle = $title;
        return $this;
    }

    /**
     * get Y axis title
     *
     * @return string
     */
    public function getYAxisTitle(){
        return $this->yAxisTitle;
    }

    /**
     * set tooltip text
     *
     * @param string $title
     * @return $this
     */
    public function setTooltip($tooltip = ''){
        $this->tooltip = $tooltip;
        return $this;
    }

    /**
     * get tooltip text
     *
     * @return string
     */
    public function getTooltip(){
        return $this->tooltip;
    }

    /**
     * set series name
     *
     * @param array $name
     * @return $this
     */
    public function setSeriesName($name = array()){
        $this->seriesName = $name;
        return $this;
    }

    /**
     * get series name
     *
     * @return string
     */
    public function getSeriesName(){
        return $this->seriesName;
    }

    /**
     * set series type
     *
     * @param array $type
     * @return $this
     */
    public function setSeriesType($type = array()){
        $this->seriesType = $type;
        return $this;
    }

    /**
     * get series type
     *
     * @return string
     */
    public function getSeriesType(){
        return $this->seriesType;
    }

    /**
     * set series data
     *
     * @param array $data
     * @return $this
     */
    public function setSeriesData($data = array()){
        $this->seriesData = $data;
        return $this;
    }

    /**
     * get series data
     *
     * @return string
     */
    public function getSeriesData(){
        return $this->seriesData;
    }

    /**
     * set series data label
     *
     * @param array $data
     * @return $this
     */
    public function setSeriesDataLabel($dataLabel = array()){
        $this->seriesDataLabel = $dataLabel;
        return $this;
    }

    /**
     * get series data label
     *
     * @return string
     */
    public function getSeriesDataLabel(){
        return $this->seriesDataLabel;
    }
}