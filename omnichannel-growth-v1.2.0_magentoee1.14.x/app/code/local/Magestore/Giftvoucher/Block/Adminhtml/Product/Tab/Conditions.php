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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Product_Tab_Conditions
 */
class Magestore_Giftvoucher_Block_Adminhtml_Product_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $product = Mage::registry('current_product');
        $model = Mage::getSingleton('giftvoucher/product');
        if (!$model->getId() && $product->getId()) {
            $model->loadByProduct($product);
        }
        $data = $model->getData();
        $model->setData('conditions', $model->getData('conditions_serialized'));

        $configSettings = Mage::getSingleton('cms/wysiwyg_config')->getConfig(
                array(
                    'add_widgets' => false,
                    'add_variables' => false,
                    'add_images' => false,
                    'files_browser_window_url' => $this->getBaseUrl() . 'admin/cms_wysiwyg_images/index/',
        ));

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('giftvoucher_');
        $fieldset = $form->addFieldset('description_fieldset', array('legend' => Mage::helper('giftvoucher')->__('Description')));

        $fieldset->addField('giftcard_description', 'editor', array(
            'label' => Mage::helper('giftvoucher')->__('Describe conditions applied to shopping cart when using this gift code'),
            'title' => Mage::helper('giftvoucher')->__('Describe conditions applied to shopping cart when using this gift code'),
            'name' => 'giftcard_description',
            'wysiwyg' => true,
            'config' => $configSettings,
        ));
        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
                ->setTemplate('promo/fieldset.phtml')
                ->setNewChildUrl($this->getUrl('adminhtml/promo_quote/newConditionHtml/form/giftvoucher_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array('legend' => Mage::helper('giftvoucher')->__('Allow using Gift Card only if the following shopping cart conditions are met (leave blank for all shopping carts)')))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('giftvoucher')->__('Conditions'),
            'title' => Mage::helper('giftvoucher')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));
        $fieldset->addField('hidden', 'hidden', array(
            'name' => 'hidden',
            'after_element_html' => '
				  <script type="text/javascript">
                                  //Add validate data
                                  $("gift_value").className+=" validate-number validate-greater-than-zero";
                                  $("gift_from").className+=" validate-greater-than-zero validate-number validate-gift-range";
                                  $("gift_to").className+=" validate-greater-than-zero validate-zero-or-greater ";
                                  $("gift_dropdown").className+=" validate-greater-than-zero validate-gift-dropdown ";
                                  $("gift_price").className+=" validate-gift-dropdown-price ";
                                  Event.observe(window, "load", function(){hidesettingGC();});
                                  if ($("gift_type")) {
                                    Event.observe($("gift_type"), "change", function()
                                        {
                                            hidesettingGC();
                                        }
                                        );
                                        }
                                    if ($("gift_price_type")) {
                                    Event.observe($("gift_price_type"), "change", function()
                                        {
                                            hidesettingGC();
                                        }
                                        );
                                        }    
//                                  $("gift_type").on("change", function(event) {
//                                    
//                                  });
//                                   $("gift_price_type").on("change", function(event) {
//                                    hidesettingGC();
//                                  });
				  function hidesettingGC(){
                                        if($("gift_price_type").value==' . Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT . ')
                                        {
                                           $("gift_price").disabled=true;
                                           $("gift_price_type").up("td").down(".note").hide();
//                                           $("gift_price_type").up("td").down(".note").update("' . $this->__('Gift Card price is the same as Gift Card value by default.') . '");
                                        }
                                        else if($("gift_price_type").value==' . Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_FIX . ')
                                        {
                                           $("gift_price").up("tr").down("label").update("' . $this->__("Gift Card price") . '<span class=\"required\">*</span>");
                                           $("gift_price").disabled=false;
                                           $("gift_price_type").up("td").down(".note").hide();
                                           $("gift_price").up("td").down(".note").update("' . $this->__("Enter fixed price(s) corresponding to Gift Card value(s).For example:<br />Type of Gift Card value: Dropdown values<br />Gift Card values : 20,30,40<br />Gift Card price: 15,25,35<br />So customers only have to pay $25 for a $30 Gift card.") . '");
                                        }
                                        else if($("gift_price_type").value==' . Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_PERCENT . ')
                                            {
                                                 $("gift_price").up("tr").down("label").update("' . $this->__("Percentage") . '<span class=\"required\">*</span>");
                                                 $("gift_price").disabled=false;
                                                 $("gift_price_type").up("td").down(".note").hide();
                                                 $("gift_price").up("td").down(".note").update("' . $this->__("Enter percentage(s) of Gift Card value(s) to calculate Gift Card price(s). For example:<br />Type of Gift Card value: Dropdown values<br />Gift Card values: 20,30,40<br />Percentage: 90,90,90<br />So customers only have to pay 90% of Gift Card value, $36 for a $40 Gift card for instance.") . '");
                                            }
					if($("gift_type").value == ' . Magestore_Giftvoucher_Model_Gifttype::GIFT_TYPE_FIX . '){
						$("gift_value").disabled=false;
                                                $("gift_from").disabled=true;
                                                $("gift_to").disabled=true;
                                                $("gift_dropdown").disabled=true;
                                                $("gift_value").up("tr").show();
						$("gift_from").up("tr").hide();
						$("gift_to").up("tr").hide();
                                                $("gift_dropdown").up("tr").hide();
                                                $("gift_price_type")[1].show();
						}
					else if($("gift_type").value == ' . Magestore_Giftvoucher_Model_Gifttype::GIFT_TYPE_RANGE . '){
						$("gift_value").disabled=true;
                                                 $("gift_from").disabled=false;
                                                $("gift_to").disabled=false;
                                                $("gift_dropdown").disabled=true;
                                                $("gift_value").up("tr").hide();
						$("gift_from").up("tr").show();
						$("gift_to").up("tr").show();
                                                $("gift_price_type")[1].hide();
                                                $("gift_dropdown").up("tr").hide();
                                                if($("gift_price_type").value=="1")
                                                $("gift_price_type")[0].selected=true;
                                                if($("gift_price_type").value=="2")
                                                $("gift_price_type")[2].selected=true;
                                                
						}
                                      else if($("gift_type").value == ' . Magestore_Giftvoucher_Model_Gifttype::GIFT_TYPE_DROPDOWN . '){
						$("gift_value").disabled=true;
                                                $("gift_from").disabled=true;
                                                $("gift_to").disabled=true;
                                                $("gift_dropdown").disabled=false;
                                                $("gift_value").up("tr").hide();
						$("gift_from").up("tr").hide();
						$("gift_to").up("tr").hide();
                                                $("gift_dropdown").up("tr").show();
                                                $("gift_price_type")[1].show();
						}
                                     if($("gift_price_type").value==' . Magestore_Giftvoucher_Model_Giftpricetype::GIFT_PRICE_TYPE_DEFAULT . '){
                                         $("gift_price").up("tr").hide();
                                     }
                                     else $("gift_price").up("tr").show();
				  }
                                error_range ="' . Mage::helper("giftvoucher")->__("Minimum Gift Card value must be lower than maximum Gift Card value.") . '";
                                Validation.add("validate-gift-range", error_range, function(v) {
                                   if(parseInt($("gift_from").value)>parseInt($("gift_to").value))
                                   return false;
                                   else return true;
                                });
                                error_dropdown ="' . Mage::helper("giftvoucher")->__("Input not correct") . '";
                                Validation.add("validate-gift-dropdown", error_dropdown, function(v) {
                                   parten=/^(\d,{0,1})+$/;
                                   
                                   return (parten.test($("gift_dropdown").value));
                                });
                                Validation.add("validate-gift-dropdown-price", error_dropdown, function(v) {
                                   if($("gift_dropdown").value && $("gift_type").value == ' . Magestore_Giftvoucher_Model_Gifttype::GIFT_TYPE_DROPDOWN . ')
                                   {
                                        cnt_dropdown=$("gift_dropdown").value.split(",").length-1;
                                        if($("gift_price").value)
                                        {
                                            cnt_giftprice=$("gift_price").value.split(",").length-1;
                                            if(cnt_dropdown!==cnt_giftprice)
                                            {
                                            return false;
                                            }
                                            else return true;
                                        }
                                        
                                   }
                                   
                                   else return true;
                                });
				  </script>',
        ));

        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * @return string
     */
    public function getTabLabel() {
        return Mage::helper('giftvoucher')->__('Shopping Cart Conditions ');
    }

    /**
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('giftvoucher')->__('Shopping Cart Conditions ');
    }

    /**
     * @return bool
     */
    public function canShowTab() {
        if (Mage::registry('current_product')->getTypeId() == 'giftvoucher') {
            return true;
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isHidden() {
        if (Mage::registry('current_product')->getTypeId() == 'giftvoucher') {
            return false;
        }
        return true;
    }

}
