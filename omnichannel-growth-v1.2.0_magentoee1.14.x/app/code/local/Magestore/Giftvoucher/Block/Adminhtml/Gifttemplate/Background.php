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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Background
 */
class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Background extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    /**
     * @param Varien_Object $row
     * @return mixed|null|string
     * @throws Exception
     */
    public function render(Varien_Object $row) {
        $actionName = $this->getRequest()->getActionName();
        $image = $row->getData($this->getColumn()->getIndex());
        if (strpos($actionName, 'export') === 0) {
            return $image;
        }
        if ($image) {
            return '<img src="' . Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'giftvoucher/template/background/' . $image . ' " width="60 px" height="60px" />';
        } else {
            return null;
        }
    }

}