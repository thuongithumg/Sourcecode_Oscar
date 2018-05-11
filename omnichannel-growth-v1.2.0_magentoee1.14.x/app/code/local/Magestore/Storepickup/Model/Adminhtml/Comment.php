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
 * @package     Magestore_Storepickup
 * @module      Storepickup
 * @author      Magestore Developer
 *
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

/**
 * Class Magestore_Storepickup_Model_Adminhtml_Comment
 */
class Magestore_Storepickup_Model_Adminhtml_Comment
{
    /**
     * @return string
     */
    public function getCommentText(){
        $comment = 'To register a Google Map API key, please follow the guide <a href="'.Mage::getBlockSingleton('adminhtml/widget')->getUrl('*/storepickup_guide/index').'">here</a>';
        return $comment;
    }
}
