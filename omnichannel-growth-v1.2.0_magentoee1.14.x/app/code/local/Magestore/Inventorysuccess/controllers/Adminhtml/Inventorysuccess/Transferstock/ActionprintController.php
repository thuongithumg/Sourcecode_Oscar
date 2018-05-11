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
use Magestore_Inventorysuccess_Model_Transferstock_Activity as Activity;
class Magestore_Inventorysuccess_Adminhtml_Inventorysuccess_Transferstock_ActionprintController
    extends Mage_Adminhtml_Controller_Action
{

    protected $y;
    protected $_pdf;
    protected $string;


    /**
     * @return string
     */
    public function getFileName(){
        $name = 'products_list.pdf';
        return $name;
    }

    /**
     * return pdf file
     */
    public function printAction()
    {
        $pdf = new \Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new \Zend_Pdf_Style();
        $this->_setFontBold($style, 10);
        $page = $this->newPage();

        /* add header */
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.45));
        $page->drawRectangle(25, $this->y, 570, $this->y - 75);
        $page->setFillColor(new \Zend_Pdf_Color_GrayScale(1));
        $this->_setFontRegular($page, 10);

        /* draw header text */
        $this->drawTextHeader($page);

        /*add header text of table item*/
        $this->y -= 50;
        $this->_drawHeader($page);

        /* add item */
        $items = $this->generateSampleData();
        foreach($items as $item){
            $varienObject = new Varien_Object();
            $varienObject->setData($item);

            /* Draw item */
            $this->_drawItem($varienObject, $page, $pdf);
            $page = end($pdf->pages);
        }

        $pdfData = $pdf->render();
        $filename = $this->getFileName();

        return $this->_prepareDownloadResponse(
            $filename, $pdfData,
            'application/pdf'
        );
    }

    /**
     * Set PDF object
     *
     * @param  \Zend_Pdf $pdf
     * @return $this
     */
    protected function _setPdf(\Zend_Pdf $pdf)
    {
        $this->_pdf = $pdf;
        return $this;
    }
    /**
     * Retrieve PDF object
     *
     * @throws Mage_Core_Exception
     * @return Zend_Pdf
     */
    protected function _getPdf()
    {
        if (!$this->_pdf instanceof Zend_Pdf) {
            Mage::throwException(Mage::helper('sales')->__('Please define PDF object before using.'));
        }

        return $this->_pdf;
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
     * Set font as regular
     *
     * @param  Zend_Pdf_Page $object
     * @param  int $size
     * @return Zend_Pdf_Resource_Font
     */
    protected function _setFontRegular($object, $size = 7)
    {
        $font = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
        $object->setFont($font, $size);
        return $font;
    }


    /**
     * Create new page and assign to PDF object
     *
     * @param  array $settings
     * @return Zend_Pdf_Page
     */
    public function newPage(array $settings = array())
    {
        /* Add new table head */
        $page = $this->_getPdf()->newPage(Zend_Pdf_Page::SIZE_A4);
        $this->_getPdf()->pages[] = $page;
        $this->y = 800;
        if (!empty($settings['table_header'])) {
            $this->_drawHeader($page);
        }
        return $page;
    }


    /**
     * Draw table header for product items
     *
     * @param  Zend_Pdf_Page $page
     * @return void
     */
    protected function _drawHeader(Zend_Pdf_Page $page)
    {
        /* Add table head */
        $this->_setFontRegular($page, 10);
        $page->setFillColor(new Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
        $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
        $page->setLineWidth(0.5);
        $page->drawRectangle(25, $this->y, 570, $this->y-15);
        $this->y -= 10;
        $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

        //columns headers
        $lines[0][] = array(
            'text' => Mage::helper('sales')->__('Products'),
            'feed' => 100,
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('Qty'),
            'feed'  => 35
        );

        $lines[0][] = array(
            'text'  => Mage::helper('sales')->__('SKU'),
            'feed'  => 565,
            'align' => 'right'
        );

        $lineBlock = array(
            'lines'  => $lines,
            'height' => 10
        );

        $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));
        $page->setFillColor(new Zend_Pdf_Color_GrayScale(0));
        $this->y -= 20;
    }



    /**
     * @param $page
     * @return mixed
     */
    public function drawTextHeader($page){
        $activityId    = $this->getRequest()->getParam('activity_id');
        $transferActivity = Mage::getModel('inventorysuccess/transferstock_activity');
        if($activityId){
            $transferActivity->load($activityId);
        }
        $activity_type = $transferActivity->getActivityType();
        if($activity_type == Activity::ACTIVITY_TYPE_RETURNING){
            $page->drawText(__('Return Id # ') . $activityId, 35, $this->y -= 15, 'UTF-8');
        }elseif($activity_type == Activity::ACTIVITY_TYPE_RECEIVING){
            $page->drawText(__('Received Id # ') . $activityId, 35, $this->y -= 15, 'UTF-8');
        }elseif($activity_type == Activity::ACTIVITY_TYPE_DELIVERY){
            $page->drawText(__('Delivery Id # ') . $activityId, 35, $this->y -= 15, 'UTF-8');
        }
        $createAt = Mage::app()->getLocale()->storeDate(
            Mage::app()->getStore(),
            Varien_Date::toTimestamp($transferActivity->getCreatedAt()),
            true
        );
        $page->drawText(__('Created At : ') . $createAt, 35, $this->y -= 10, 'UTF-8');
        $page->drawText(__('Created By : ') . $transferActivity->getCreatedBy(), 35, $this->y -= 10, 'UTF-8');
        $page->drawText(__('Note :  ') . $transferActivity->getNote(), 35, $this->y -= 10, 'UTF-8');
        return $page;
    }


    /**
     * Draw lines
     *
     * draw items array format:
     * lines        array;array of line blocks (required)
     * shift        int; full line height (optional)
     * height       int;line spacing (default 10)
     *
     * line block has line columns array
     *
     * column array format
     * text         string|array; draw text (required)
     * feed         int; x position (required)
     * font         string; font style, optional: bold, italic, regular
     * font_file    string; path to font file (optional for use your custom font)
     * font_size    int; font size (default 7)
     * align        string; text align (also see feed parametr), optional left, right
     * height       int;line spacing (default 10)
     *
     * @param  Zend_Pdf_Page $page
     * @param  array $draw
     * @param  array $pageSettings
     * @throws Mage_Core_Exception
     * @return Zend_Pdf_Page
     */
    public function drawLineBlocks(Zend_Pdf_Page $page, array $draw, array $pageSettings = array())
    {
        foreach ($draw as $itemsProp) {
            if (!isset($itemsProp['lines']) || !is_array($itemsProp['lines'])) {
                Mage::throwException(Mage::helper('sales')->__('Invalid draw line data. Please define "lines" array.'));
            }
            $lines  = $itemsProp['lines'];
            $height = isset($itemsProp['height']) ? $itemsProp['height'] : 10;

            if (empty($itemsProp['shift'])) {
                $shift = 0;
                foreach ($lines as $line) {
                    $maxHeight = 0;
                    foreach ($line as $column) {
                        $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                        if (!is_array($column['text'])) {
                            $column['text'] = array($column['text']);
                        }
                        $top = 0;
                        foreach ($column['text'] as $part) {
                            $top += $lineSpacing;
                        }

                        $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                    }
                    $shift += $maxHeight;
                }
                $itemsProp['shift'] = $shift;
            }

            if ($this->y - $itemsProp['shift'] < 15) {
                $page = $this->newPage($pageSettings);
            }

            foreach ($lines as $line) {
                $maxHeight = 0;
                foreach ($line as $column) {
                    $fontSize = empty($column['font_size']) ? 10 : $column['font_size'];
                    if (!empty($column['font_file'])) {
                        $font = Zend_Pdf_Font::fontWithPath($column['font_file']);
                        $page->setFont($font, $fontSize);
                    } else {
                        $fontStyle = empty($column['font']) ? 'regular' : $column['font'];
                        switch ($fontStyle) {
                            case 'bold':
                                $font = $this->_setFontBold($page, $fontSize);
                                break;
                            case 'italic':
                                $font = $this->_setFontItalic($page, $fontSize);
                                break;
                            default:
                                $font = $this->_setFontRegular($page, $fontSize);
                                break;
                        }
                    }

                    if (!is_array($column['text'])) {
                        $column['text'] = array($column['text']);
                    }

                    $lineSpacing = !empty($column['height']) ? $column['height'] : $height;
                    $top = 0;
                    foreach ($column['text'] as $part) {
                        if ($this->y - $lineSpacing < 15) {
                            $page = $this->newPage($pageSettings);
                        }

                        $feed = $column['feed'];
                        $textAlign = empty($column['align']) ? 'left' : $column['align'];
                        $width = empty($column['width']) ? 0 : $column['width'];
                        switch ($textAlign) {
                            case 'right':
                                if ($width) {
                                    $feed = $this->getAlignRight($part, $feed, $width, $font, $fontSize);
                                }
                                else {
                                    $feed = $feed - $this->widthForStringUsingFontSize($part, $font, $fontSize);
                                }
                                break;
                            case 'center':
                                if ($width) {
                                    $feed = $this->getAlignCenter($part, $feed, $width, $font, $fontSize);
                                }
                                break;
                        }
                        $page->drawText($part, $feed, $this->y-$top, 'UTF-8');
                        $top += $lineSpacing;
                    }

                    $maxHeight = $top > $maxHeight ? $top : $maxHeight;
                }
                $this->y -= $maxHeight;
            }
        }
        return $page;
    }


    /**
     * Returns the total width in points of the string using the specified font and
     * size.
     *
     * This is not the most efficient way to perform this calculation. I'm
     * concentrating optimization efforts on the upcoming layout manager class.
     * Similar calculations exist inside the layout manager class, but widths are
     * generally calculated only after determining line fragments.
     *
     * @param  string $string
     * @param  Zend_Pdf_Resource_Font $font
     * @param  float $fontSize Font size in points
     * @return float
     */
    public function widthForStringUsingFontSize($string, $font, $fontSize)
    {
        $drawingString = '"libiconv"' == ICONV_IMPL ?
            iconv('UTF-8', 'UTF-16BE//IGNORE', $string) :
            @iconv('UTF-8', 'UTF-16BE', $string);

        $characters = array();
        for ($i = 0; $i < strlen($drawingString); $i++) {
            $characters[] = (ord($drawingString[$i++]) << 8) | ord($drawingString[$i]);
        }
        $glyphs = $font->glyphNumbersForCharacters($characters);
        $widths = $font->widthsForGlyphs($glyphs);
        $stringWidth = (array_sum($widths) / $font->getUnitsPerEm()) * $fontSize;
        return $stringWidth;

    }


    /**
     * @return array
     */
    public function generateSampleData() {
        $activityId    = $this->getRequest()->getParam('activity_id');
        $transferActivityProduct = Mage::getModel('inventorysuccess/transferstock_activity_product')->getCollection();
        $data = array();
        if($activityId){
            $transferActivityProduct->addFieldToFilter('activity_id',$activityId);
        }
        foreach ($transferActivityProduct as $product) {
            $data[]= array(
                'name' => $product->getData('product_name'),
                'sku' => $product->getData('product_sku'),
                'qty' => $product->getData('qty'),
            );
        }
        return $data;
    }

    /**
     * @param \Magento\Framework\DataObject $item
     * @param \Zend_Pdf_Page $page
     * @param $pdf
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _drawItem(Varien_Object $item, Zend_Pdf_Page $page)
    {

        $lines = array();
        // draw Product name
        $lines[0] = array(
            array('text' => Mage::helper('core/string')->str_split($item->getName(), 60, true, true),
                'feed' => 100)
        );
        // draw QTY
        $lines[0][] = array('text' => $item->getQty() * 1, 'feed' => 35);
        // draw SKU
        $lines[0][] = array(
            'text' =>  Mage::helper('core/string')->str_split($item->getSku(), 25),
            'feed' => 565,
            'align' => 'right',
        );
        $lineBlock = array('lines' => $lines, 'height' => 20);
        $page = $this->drawLineBlocks($page, array($lineBlock), array('table_header' => true));

    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('admin/inventorysuccess/stockcontrol/stock_transfer');
    }
}
