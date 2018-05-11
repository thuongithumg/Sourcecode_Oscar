<?php

class Magestore_Webpospaypal_Block_Adminhtml_Config_Redirect  extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * render config row
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
	{
		$redirectUrl = Mage::getUrl('webpospaypal/config/paypalsignin', array('_nosid'=>true, '_secure'=>true));
		$html = "<div class='redirect-url' style='width: 550px;'>$redirectUrl</div>";
		return $html;
	}
}
