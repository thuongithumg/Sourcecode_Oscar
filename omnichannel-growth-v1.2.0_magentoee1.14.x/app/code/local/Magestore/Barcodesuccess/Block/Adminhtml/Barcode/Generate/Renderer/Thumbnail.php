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

class Magestore_Barcodesuccess_Block_Adminhtml_Barcode_Generate_Renderer_Thumbnail
    extends
    Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render( Varien_Object $row )
    {
        $product = Mage::getModel('catalog/product')->load($row->getData('entity_id'));
        try {
            $thumbnail = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(70);
        } catch ( Exception $e ) {
            $thumbnail = Mage::getDesign()->getSkinUrl('images/catalog/product/placeholder/image.jpg', array('_area' => 'frontend'));
        }
        $name = $row->getData('name');
        $html = "<img width='70px' src = '$thumbnail' alt='$name'/>";
        return $html;
    }

}