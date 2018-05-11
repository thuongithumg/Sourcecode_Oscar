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
 * @package     Magestore_Coresuccess
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
class Magestore_Coresuccess_Block_Adminhtml_Page_Menu extends Mage_Adminhtml_Block_Page_Menu {

    protected $_menupath = null;
    protected $_module_key = null;

    /**
     * Initialize template and cache settings
     *
     */
    protected function _construct() {
        parent::_construct();
        $this->setTemplate('coresuccess/page/menu.phtml');
        $this->_url = Mage::getModel('adminhtml/url');
        $this->setCacheTags(array(self::CACHE_TAGS));
        $this->_module_key = $this->helper('coresuccess')->getCurrentModuleKey();
        if ($this->_module_key == 'mage') {
            $this->_module_key = $this->helper('coresuccess')->getCurrentSectionConfig();
        }
    }

    /**
     * Get active menu
     *
     * @return string
     */
    public function getActive() {
        if ($coreMenu = $this->getLayout()->getBlock('menu')) {
            return $coreMenu->getData('active');
        }
    }

    /**
     * Get Key pieces for caching block content
     *
     * @return array
     */
    public function getCacheKeyInfo() {
        $cacheKeyInfo = array(
            'coresuccess_top_nav',
            $this->getActive() . '/' . $this->_module_key,
            Mage::getSingleton('admin/session')->getUser()->getId(),
            Mage::app()->getLocale()->getLocaleCode()
        );
        // Add additional key parameters if needed
        $additionalCacheKeyInfo = $this->getAdditionalCacheKeyInfo();
        if (is_array($additionalCacheKeyInfo) && !empty($additionalCacheKeyInfo)) {
            $cacheKeyInfo = array_merge($cacheKeyInfo, $additionalCacheKeyInfo);
        }
        return $cacheKeyInfo;
    }

    /**
     * Get icon class of menu item
     *
     * @param string $menuItemId
     * @return string
     */
    public function getMenuIcon($menuItemId, $level) {
        $path = 'menu/' . $this->_module_key . '/children' . $this->getPath($menuItemId);
        if ($this->_module_key == 'webpos') {
            $path = 'menu/sales/children/webpos/children' . $this->getPath($menuItemId);
        }
        $menuConfig = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode($path);
        if ($menuConfig && $menuConfig->icon)
            return (string) $menuConfig->icon;
        if ($level == 0) {
            return 'fa-square-o';
        }
        return null;
    }

    /**
     * Get config path of menu item
     *
     * @param string $menuItemId
     * @return string
     */
    public function getPath($menuItemId) {
        return $this->_menupath . '/' . $menuItemId;
    }

    /**
     * Get menu level HTML code
     *
     * @param array $menu
     * @param int $level
     * @return string
     */
    public function getMenuLevel($menu, $level = 0, $parentId = null, $title = '') {
        $html = '<ul ' . (!$level ? 'id="erp-nav" style="display:block; float:left"' : 'id="erpplus_item_ul_' . $parentId . '"')
                . ($level == 1 ? ' name="erpplus_item_1" class="ullevel1" ' : '') . ' >' . PHP_EOL;
        if ($title && $level == 1) {
            $html .= '<h5 class="submenu-title">' . $title . '</h5>' . PHP_EOL;
        }
        $i = 0;
        foreach ($menu as $itemId => $item) {
            $i++;
            $prefixId = ($level == 0) ? $i : $level . '_' . $i;
            $itemIcon = $this->getMenuIcon($itemId, $level);
            if (!empty($item['children'])) {
                $item['url'] = '#';
            }
            $html .= '<li  onclick= "navitabs(' . $i . ')" id="erpplus_item_' . $prefixId . '" '
                    . ' name="erpplus_item_' . $level . '" '
                    . ' class="'
                    . (!empty($item['active']) ? ' active active-main active-ul' : '') . ' '
                    . (!empty($item['children']) ? ' parent' : '')
                    . (!empty($item['last']) ? ' last ' : '')
                    . (!empty($level) && !empty($item['last']) ? ' last' : '')
                    . ($item['url'] == '#' ? ' label-li ' : '')
                    . ' level' . $level . '"> <a href="' . $item['url'] . '" '
                    . (!empty($item['title']) ? 'title="' . $item['title'] . '"' : '') . ' '
                    . (!empty($item['click']) ? 'onclick="' . $item['click'] . '"' : '') . ' class="'
                    . (!empty($item['active']) ? 'active' : '')
                    . ($item['url'] == '#' ? ' label-item ' : '')
                    . '">'
                    . ($itemIcon ? '<i class="fa ' . $itemIcon . '"></i>' : '')
                    . '<span>' . $this->escapeHtml($item['label']) . '</span></a>' . PHP_EOL;

            if (!empty($item['children'])) {
                $this->_menupath .= '/' . $itemId;
                $html .= $this->getMenuLevel($item['children'], $level + 1, $prefixId, $item['label']);
                $this->_menupath = str_replace('/' . $itemId, '', $this->_menupath);
            }
            $html .= '</li>' . PHP_EOL;
        }
        $html .= '</ul>' . PHP_EOL;

        return $html;
    }

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
            $parent = $this->_getParentMenu();
        }
        if ($this->_module_key == 'webpos') {
            if (!$path)
                $path = 'sales/webpos/';
        }
        $path = $path ? $path : $this->_module_key . '/';
        return parent::_buildMenuArray($parent, $path, $level);
    }

    protected function _getParentMenu() {
        $menupath = $this->_module_key;
        if ($this->_module_key == 'webpos') {
            $menupath = 'sales/children/webpos';
        }
        $parent = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu/' . $menupath . '/children');

        return $parent;
    }

}
