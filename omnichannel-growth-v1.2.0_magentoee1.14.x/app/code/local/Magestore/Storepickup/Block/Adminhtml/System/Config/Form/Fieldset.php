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
 * Config Fieldset Block
 *
 * @category    Magestore
 *
 * @package     Magestore_Storepickup
 *
 * @author      Magestore Developer
 */
class Magestore_Storepickup_Block_Adminhtml_System_Config_Form_Fieldset
 extends Mage_Adminhtml_Block_System_Config_Form_Fieldset {

	/**
	 * @param Varien_Data_Form_Element_Abstract $element
	 * @return string
     */
	public function render(Varien_Data_Form_Element_Abstract $element) {
		$html = parent::render($element);

		if ($storepickup = $this->getRequest()->getParam('storepickup')) {
			$html .= Mage::helper('adminhtml/js')->getScript('
				;
				Event.observe(window,"load",function(){
					if(config_edit_form) {
                	config_edit_form.select(".open").invoke("click");
					}
	                if($("carriers_storepickup-head") && !$("carriers_storepickup-head").hasClassName("open")){
	                    $("carriers_storepickup-head").click();
	                }
				});
				'
			);
		}
		return $html;
	}
}