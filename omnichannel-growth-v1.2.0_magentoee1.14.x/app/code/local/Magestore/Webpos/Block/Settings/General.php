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
 * @package     Magestore_Webpos
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Class General
 * @package Magestore\Webpos\Block\Settings
 */
class Magestore_Webpos_Block_Settings_General extends Magestore_Webpos_Block_AbstractBlock
{
    /**
     * @return string
     */
    public function _toHtml()
    {
        $isLogin = Mage::helper('webpos/permission')->getCurrentUser();
        if ($isLogin && !Mage::helper('webpos/permission')->isShowChoosePosLocation()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
