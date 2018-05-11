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
 * @package     Magestore_Inventory
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Warehouse Adminhtml Block
 *
 * @category    Magestore
 * @package     Magestore_Coresuccess
 * @author      Magestore Developer
 */
class Magestore_Coresuccess_Block_Adminhtml_Rewrite_Menu extends Mage_Adminhtml_Block_Page_Menu {

    protected $_erpMenus = array();
    protected $_erpMainMenu = null;

    /**
     * Recursive Build Menu array
     *
     * @param Varien_Simplexml_Element $parent
     * @param string $path
     * @param int $level
     * @return array
     */
    protected function _buildMenuArray(Varien_Simplexml_Element $parent = null, $path = '', $level = 0) {
        if (is_null($parent)) {
            $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
        }

        $parentArr = array();
        $sortOrder = 0;
        foreach ($parent->children() as $childName => $child) {
            if (1 == $child->disabled) {
                continue;
            }

            $aclResource = 'admin/' . ($child->resource ? (string) $child->resource : $path . $childName);
            if (!$this->_checkAcl($aclResource) || !$this->_isEnabledModuleOutput($child)) {
                continue;
            }

            if ($child->depends && !$this->_checkDepends($child->depends)) {
                continue;
            }

            $menuArr = array();

            $menuArr['label'] = $this->_getHelperValue($child);

            $menuArr['sort_order'] = $child->sort_order ? (int) $child->sort_order : $sortOrder;

            if ($child->action) {
                $menuArr['url'] = $this->_url->getUrl((string) $child->action, array('_cache_secret_key' => true));
            } else {
                $menuArr['url'] = '#';
                $menuArr['click'] = 'return false';
            }

            $menuArr['active'] = ($this->getActive() == $path . $childName) || (strpos($this->getActive(), $path . $childName . '/') === 0);

            $menuArr['level'] = $level;


            /* remove erp modules from the main menu of Magento */
            $erpmenu = false;
            $childAttributes = $child->attributes();
            if (isset($childAttributes['module'])) {
                $moduleKey = (string) $childAttributes['module'];
                if ($this->helper('coresuccess')->isERPmodule($moduleKey)) {
                    $erpmenu = true;
                }
            }
            if (!$erpmenu) {
                if ($child->children) {
                    $menuArr['children'] = $this->_buildMenuArray($child->children, $path . $childName . '/', $level + 1);
                }                    
                $parentArr[$childName] = $menuArr;
                if (!$this->_erpMainMenu) {
                    $this->_erpMainMenu = &$parentArr;
                }
                if ($childName == 'magestore_coresuccess') {
                    if (count($this->_erpMenus)) {
                        foreach ($this->_erpMenus as $erpMenuKey => $erpMenu) {
                            $this->_erpMainMenu['magestore_coresuccess']['children'][$erpMenuKey] = $erpMenu;
                        }
                    }
                }
            } else {
                if (!isset($this->_erpMenus[$moduleKey])) {                       
                    $this->_erpMenus[$moduleKey] = $menuArr;
                    if ($child->children) {
                        $this->_erpMenus[$moduleKey]['children'] = $this->_buildMenuArray($child->children, $path . $childName . '/', $level + 1);
                    }                    
                    if (isset($this->_erpMainMenu['magestore_coresuccess'])) {
                        $this->_erpMainMenu['magestore_coresuccess']['children'][$moduleKey] = $this->_erpMenus[$moduleKey];
                    }
                } else {
                    if ($child->children) {
                        $menuArr['children'] = $this->_buildMenuArray($child->children, $path . $childName . '/', $level + 1);
                    }                       
                    $parentArr[$childName] = $menuArr;
                }
            }
                    

            $sortOrder++;
        }

        uasort($parentArr, array($this, '_sortMenu'));

        while (list($key, $value) = each($parentArr)) {
            $last = $key;
        }
        if (isset($last)) {
            $parentArr[$last]['last'] = true;
        }

        return $parentArr;
    }

}
