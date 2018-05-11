<?php

class Magestore_Webpos_Block_Adminhtml_Field_Webposinstallation extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * render config row
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
		$id = $element->getHtmlId();
        $html  = "<tr id='row_" . $id . "'>";
		$html .= "<td colspan='3' style='line-height: 27px;'><div style='padding-left:5px;font-weight: bold; border-bottom: 1px solid #dfdfdf;'>";
        $html .= $element->getLabel();
        $html .= "</div></td>";
        $html .= "<td class='value' style='padding-left:50px !important;'><a style='text-decoration:none;' href='https://docs.google.com/viewer?url=https://www.magestore.com/media/productfile/i/n/installation-guide.pdf' target='_bank'>".$element->getLabel()."</a>";
        $html .= "</td></tr>";
        return $html;
    }
}
