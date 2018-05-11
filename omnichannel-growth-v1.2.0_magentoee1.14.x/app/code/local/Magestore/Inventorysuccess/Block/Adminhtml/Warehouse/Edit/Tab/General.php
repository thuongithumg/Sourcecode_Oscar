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
 * @package     Magestore_Inventorysuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Warehouse Edit Form General Tab Block
 *
 * @category    Magestore
 * @package     Magestore_Inventorysuccess
 * @author      Magestore Developer
 */
class Magestore_Inventorysuccess_Block_Adminhtml_Warehouse_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $data = Mage::getSingleton('adminhtml/session')->getData('warehouse_param');
        Mage::getSingleton('adminhtml/session')->setData('warehouse_param', null);
        if (!$data) {
            $data = Mage::registry('current_warehouse')->getData();
        }

        $warehouse = Mage::registry('current_warehouse');
        $disable = false;
        if ($warehouse->getId() &&
            !Magestore_Coresuccess_Model_Service::permissionService()->checkPermission(
                'admin/inventorysuccess/stocklisting/warehouse_list/view_warehouse/edit_general_information', $warehouse
            )
        ) {
            $disable = true;
        }

        $fieldset = $form->addFieldset('warehouse_general_form', array(
            'legend' => $this->__('General Information')
        ));
        $fieldset->addField('warehouse_id',
            'hidden',
            array(
                'name' => 'warehouse_id',
            )
        );
        $fieldset->addField('warehouse_name',
            'text',
            array(
                'name'      => 'warehouse_name',
                'label'     => $this->__('Warehouse Name'),
                'title'     => $this->__('Warehouse Name'),
                'required'  => true,
                'disabled'  => $disable
            )
        );
        $fieldset->addField('warehouse_code',
            'text',
            array(
                'name'      => 'warehouse_code',
                'label'     => $this->__('Warehouse Code'),
                'title'     => $this->__('Warehouse Code'),
                'required'  => true,
                'disabled'  => $disable
            )
        );
        $fieldset->addField('contact_email',
            'text',
            array(
                'name'      => 'contact_email',
                'label'     => $this->__('Contact Email'),
                'title'     => $this->__('Contact Email'),
                'class'     => 'validate-email',
                'disabled'  => $disable
            )
        );
        $fieldset->addField('telephone',
            'text',
            array(
                'name'      => 'telephone',
                'label'     => $this->__('Telephone'),
                'title'     => $this->__('Telephone'),
                'class'     => 'validate-phoneLax',
                'disabled'  => $disable
            )
        );
        $fieldset->addField('street',
            'text',
            array(
                'name'      => 'street',
                'label'     => $this->__('Street'),
                'title'     => $this->__('Street'),
                'disabled'  => $disable
            )
        );
        $fieldset->addField('city',
            'text',
            array(
                'name'      => 'city',
                'label'     => $this->__('City'),
                'title'     => $this->__('City'),
                'disabled'  => $disable
            ));
        $fieldset->addField('country_id',
            'select',
            array(
                'name'      => 'country_id',
                'label'     => $this->__('Country'),
                'values'    => Mage::getModel('inventorysuccess/warehouse_options_country')->getCountryListHash(),
                'disabled'  => $disable
            )
        );
        $fieldset->addField('regionEl',
            'note',
            array(
                'name' => 'regionEl',
                'label' => $this->__('State/Province'),
                'text' => $this->getLayout()
                    ->createBlock('inventorysuccess/adminhtml_warehouse_edit_tab_renderer_region')
                    ->setDisabled($disable)
                    ->setTemplate('inventorysuccess/warehouse/region.phtml')
                    ->toHtml(),
            )
        );
        $fieldset->addField('postcode',
            'text',
            array(
                'name'      => 'postcode',
                'label'     => $this->__('Zip/Postal Code'),
                'title'     => $this->__('Zip/Postal Code'),
                'disabled'  => $disable
            )
        );
        
        if(Magestore_Coresuccess_Model_Service::stockService()->isLinkWarehouseToStore()) {
            $field = $fieldset->addField('store_id', 'multiselect', array(
                'name'      => 'stores[]',
                'label'     => Mage::helper('inventorysuccess')->__('Magento Store View'),
                'title'     => Mage::helper('inventorysuccess')->__('Magento Store View'),
                'required'  => false,
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(true, false),
            ));
     
        }
        
        if (Mage::helper('core')->isModuleEnabled('Magestore_Webpos')) {
            $locationId = 0;
            $warehouseId = 0;
            if(isset($data['warehouse_id'])) {
                $warehouseLocationMap = Mage::getModel('inventorysuccess/warehouseLocationMap')
                    ->load($data['warehouse_id'], 'warehouse_id');
                $locationId = $warehouseLocationMap->getLocationId();
                $warehouseId = $data['warehouse_id'];
            }
            $data['location_id'] = $locationId;
            $fieldset->addField('location_id', 'select', array(
                'label'  => Mage::helper('inventorysuccess')->__('Location'),
                'name'   => 'location_id',
                'values' => Magestore_Coresuccess_Model_Service::locationService()->toLocationOptionArray($warehouseId)
            ));
        }
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}