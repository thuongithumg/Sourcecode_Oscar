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
 * Adminhtml Giftvoucher Generategiftcard Edit Block
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */

class Magestore_Giftvoucher_Block_Adminhtml_Generategiftcard_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{

    /**
     * Magestore_Giftvoucher_Block_Adminhtml_Generategiftcard_Edit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'giftvoucher';
        $this->_controller = 'adminhtml_generategiftcard';

        $this->_updateButton('save', 'label', Mage::helper('giftvoucher')->__('Save Pattern'));
        $this->_updateButton('delete', 'label', Mage::helper('giftvoucher')->__('Delete Template'));
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save',
            ), -100);

        if ($this->getTemplateGenerate()->getIsGenerated()) {
            $this->_removeButton('save');
            $this->_removeButton('reset');
            $this->_removeButton('saveandcontinue');
            $this->_addButton('duplicate', array(
                'label' => Mage::helper('adminhtml')->__('Duplicate'),
                'onclick' => 'duplicate()',
                'class' => 'save',
                ), -100);
        } else {
            $this->_addButton('generate', array(
                'label' => Mage::helper('adminhtml')->__('Save And Generate'),
                'onclick' => 'saveAndGenerate()',
                'class' => 'save',
                ), -100);
        }
        $this->_formScripts[] = "            
            function saveAndContinueEdit(){
                editForm.submit('" . $this->getUrl('*/*/save', array(
                'id' => $this->getRequest()->getParam('id'),
                'back' => 'edit'
            )) . "');
            }
            
            Event.observe(window, 'load', function(){
                $('list_images').up('tr').hide();
            });
            
            var image_current;
            var gift_template_id;
            function loadImageTemplate(template_id,image){
                    gift_template_id=template_id;
                    current_image=image;
                    new Ajax.Request('"
            . $this->getUrl('*/*/giftimages', array('_current' => true))
            . "', {
                    parameters: {
                                 form_key: FORM_KEY,
                                 gift_template_id: gift_template_id,
                                 current_image:current_image,
                                 },
                    evalScripts: true,
                    onSuccess: function(transport) {
                        if(transport.responseText){
                            $('list_images').up('tr').show(); 
                            $('list_images').update(transport.responseText);
                        if($$('#gift-image-carosel img').length>=4)
                        carousel = new Carousel('carousel-wrapper', 
                                                $$('#gift-image-carosel img'), 
                                                $$('#gift-image-carosel a'), 
                                                {
                                                    duration: 0.5,
                                                    transition: 'sinoidal',
                                                    visibleSlides: 4,
                                                    circular: false
                                                });
                        changeSelectImages(-1);
                        }
                        else{
                        $('list_images').update(transport.responseText);
                        $('list_images').up('tr').hide();
                        }
                    }
                });
            }
            
            function changeSelectImages(id,image){
                if(id == -1){
                       image_current=$('div-image-for-'+gift_template_id+'-'+selected_image.value);
                       image_current.addClassName('gift-active');
                       image_current.down('.egcSwatch-arrow').show(); 
                       $('giftcard_template_image').value=$('current_image').value;
                }
                else
                {
                image_current.removeClassName('gift-active');
                image_current.down('.egcSwatch-arrow').hide();
                image_current=$('div-image-for-'+gift_template_id+'-'+id);
                image_current.addClassName('gift-active');
                image_current.down('.egcSwatch-arrow').show();
                $('giftcard_template_image').value=image;
                }
            }
             
            function saveAndGenerate(){
                editForm.submit('" . $this->getUrl('*/*/generate', array(
                'id' => $this->getRequest()->getParam('id')
            )) . "');
            }
            function duplicate(){
                editForm.submit('" . $this->getUrl('*/*/duplicate', array(
                'id' => $this->getRequest()->getParam('id')
            )) . "');
            }
        ";
    }

    /**
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('template_data') && Mage::registry('template_data')->getId()) {
            return Mage::helper('giftvoucher')->__("Edit Gift Code Pattern '%s'", 
                $this->htmlEscape(Mage::registry('template_data')->getTemplateName()));
        } else {
            return Mage::helper('giftvoucher')->__('New Gift Code Pattern');
        }
    }

    /**
     * @return false|Mage_Core_Model_Abstract|mixed
     */
    public function getTemplateGenerate()
    {
        if (Mage::registry('template_data')) {
            return Mage::registry('template_data');
        }
        return Mage::getModel('giftvoucher/template');
    }

}
