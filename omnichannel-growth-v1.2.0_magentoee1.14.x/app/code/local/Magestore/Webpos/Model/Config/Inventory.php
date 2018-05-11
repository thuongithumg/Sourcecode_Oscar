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

class Magestore_Webpos_Model_Config_Inventory extends Magestore_Webpos_Model_Abstract
{
    public function getConfig()
    {
        $results = array();

        $configs = Mage::helper('webpos')->getStoreConfig('cataloginventory');
        if (!empty($configs)){

        }

        foreach($configs['item_options'] as $config => $value) {
            if($config == 'min_sale_qty') {
                try {
                    if(is_array($value)) {
                        $options = unserialize($value);
                    }else{
                        $options = $value;
                    }
                } catch(\Exception $e) {
                    $options = $value;
                }
                $values = array();
                if(is_array($options)) {
                    foreach($options as $groupId => $minQty) {
                        $values[] = array('group' => $groupId, 'value' => $minQty);
                    }
                } else {
                    $values = $value;
                }
                $configs['item_options']['min_sale_qty'] = $values;
            }
        }
        /* convert configs to flat path */
        foreach($configs as $index => $subConfigs) {
            foreach($subConfigs as $subIndex => $value) {
                $results['cataloginventory/'.$index.'/'.$subIndex] = $value;
            }
        }

        return $results;
    }
}
