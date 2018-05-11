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
 * @package     Magestore_Purchaseordersuccess
 * @copyright   Copyright (c) 2016 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Purchaseordersuccess Helper
 *
 * @category    Magestore
 * @package     Magestore_Purchaseordersuccess
 * @author      Magestore Developer
 */
class Magestore_Purchaseordersuccess_Block_Adminhtml_Form_Field_Element_Date
    extends Varien_Data_Form_Element_Date
{


    public function __construct($attributes=array())
    {
        parent::__construct($attributes);
    }
    /**
     * Output the input field and assign calendar instance to it.
     * In order to output the date:
     * - the value must be instantiated (Zend_Date)
     * - output format must be set (compatible with Zend_Date)
     *
     * @return string
     */
    public function getElementHtml()
    {
        $this->addClass('input-text');

        $html = sprintf(
            '<input name="%s" id="%s" value="%s" %s style="width:110px !important;" />'
            . ' <img src="%s" alt="" class="v-middle" id="%s_trig" title="%s" style="%s" />',
            $this->getName(), $this->getHtmlId(), $this->_escape($this->getValue()), $this->serialize($this->getHtmlAttributes()),
            $this->getImage(), $this->getHtmlId(), 'Select Date', ($this->getDisabled() ? 'display:none;' : '')
        );
        $outputFormat = $this->getFormat();
        if (empty($outputFormat)) {
            throw new Exception('Output format is not specified. Please, specify "format" key in constructor, or set it using setFormat().');
        }
        $displayFormat = Varien_Date::convertZendToStrFtime($outputFormat, true, (bool)$this->getTime());
        
        if ($this->getMinDate()) {
            $minDate = '
                var minDate = new Date("'.$this->getMinDate().'");
                if(minDate.getFullYear() > date.getFullYear())
                    return true;
                if(minDate.getFullYear() >= date.getFullYear() && minDate.getMonth() > date.getMonth())
                    return true;
                if(minDate.getFullYear() >= date.getFullYear() && minDate.getMonth() >= date.getMonth() 
                && minDate.getDate() > date.getDate())
                    return true;
            ';
        } else {
            $minDate = '';
        }
        
        $html .= sprintf('
            <script type="text/javascript">
            //<![CDATA[
                Calendar.setup({
                    inputField: "%s",
                    ifFormat: "%s",
                    showsTime: %s,
                    button: "%s_trig",
                    align: "Bl",
                    singleClick : true,
                    disableFunc: function(date){
                        %s
                    },
                });
            //]]>
            </script>',
            $this->getHtmlId(), $displayFormat,
            $this->getTime() ? 'true' : 'false', $this->getHtmlId(),
            $minDate
        );

        $html .= $this->getAfterElementHtml();

        return $html;
    }
}