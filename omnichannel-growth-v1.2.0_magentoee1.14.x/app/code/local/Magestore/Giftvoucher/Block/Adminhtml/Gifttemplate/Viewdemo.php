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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Viewdemo
 */
class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Viewdemo extends Mage_Adminhtml_Block_Template {

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Viewdemo constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->setTemplate('giftvoucher/template/serializer.phtml');
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPattern() {
        return Mage::registry('pattern');
    }

}
