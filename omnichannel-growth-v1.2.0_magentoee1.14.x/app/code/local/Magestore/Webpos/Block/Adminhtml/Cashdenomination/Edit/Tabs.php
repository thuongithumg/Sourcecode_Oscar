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
 * Created by PhpStorm.
 * User: Quoc Viet
 * Date: 07/07/2015
 * Time: 9:58 SA
 */
class Magestore_Webpos_Block_Adminhtml_Cashdenomination_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('denomination_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('webpos')->__('Denomination Information'));
    }

    /**
     * prepare before render block to html
     *
     * @return Magestore_Webpos_Block_Adminhtml_Webpos_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('webpos')->__('Denomination Information'),
            'title'     => Mage::helper('webpos')->__('Denomination Information'),
            'content'   => $this->getLayout()
                ->createBlock('webpos/adminhtml_cashdenomination_edit_tab_form')
                ->toHtml(),
        ));


        return parent::_beforeToHtml();
    }
}