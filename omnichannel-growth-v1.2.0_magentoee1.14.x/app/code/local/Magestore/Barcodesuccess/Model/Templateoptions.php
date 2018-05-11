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
 * @package     Magestore_Inventorywarehouse
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Inventorywarehouse Model
 *
 * @category    Magestore
 * @package     Magestore_Barcodesuccess
 * @author      Magestore Developer
 */
class Magestore_Barcodesuccess_Model_Templateoptions
{
    public function toOptionArray()
    {
        $result = array();
        $template = Mage::getModel('barcodesuccess/template')->getCollection()
            ->addFieldToFilter(Magestore_Barcodesuccess_Model_Template::STATUS, Magestore_Barcodesuccess_Model_Source_Template_Status::ACTIVE);
        //zend_debug::dump($template->getData());die;
        foreach($template->getData() as $key){
            $value = array(
                'value' => $key['template_id'],
                'label' => $key['name']
            );
            array_push($result, $value);
        }
        return $result;
    }
}

