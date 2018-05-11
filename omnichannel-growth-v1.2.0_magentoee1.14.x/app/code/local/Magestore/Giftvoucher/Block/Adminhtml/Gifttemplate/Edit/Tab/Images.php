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
 * Class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Edit_Tab_Images
 */
class Magestore_Giftvoucher_Block_Adminhtml_Gifttemplate_Edit_Tab_Images extends Mage_Adminhtml_Block_Widget_Form {

    /**
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        if (Mage::getSingleton('adminhtml/session')->getGifttemplateData()) {
            $data = Mage::getSingleton('adminhtml/session')->getGifttemplateData();
            Mage::getSingleton('adminhtml/session')->setGifttemplateData(null);
        } elseif (Mage::registry('gifttemplate_data')) {
            $data = Mage::registry('gifttemplate_data')->getData();
        }
        $data['number_image'] = 0;
        if (isset($data['images']) && $data['images']) {
            $images = $data['images'];
            $str = '';
            if ($images) {
                $str.='<div class=\"carousel\" id=\"gift-image-carosel\">
                            <a href=\"javascript:\" class=\"carousel-control next\" rel=\"next\">›</a>
                            <a href=\"javascript:\" class=\"carousel-control prev\" rel=\"prev\">‹</a>
                            <div class=\"gift-middle\" id=\"carousel-wrapper\">
                                <div class=\"inner\" style=\"width: 4000px;\">
                  ';

                $images = explode(',', $images);
                foreach ($images as $image) {
                    $str.='<div id=\"' . $image . '\" style=\"position:relative; float: left;border: 2px solid white;\">';
                    $str.='<img src=\"' . Mage::getBaseUrl("media") . 'giftvoucher/template/images/' . $data['design_pattern'] . '/' . $image . '\" alt=\"\" style=\"width:100px;height:100px\"
                    >';
                    $str.='<div style=\"position: absolute;top: 105px;\">';
                    $str.='<a class=\"preview-img\" href=\"javascript:previewImage(\'' . $image . '\')\">' . Mage::helper('giftvoucher')->__("Preview") . '</a>
                           ||<a class=\"remove-img\" href=\"javascript:removeImage(\'' . $image . '\')\">' . Mage::helper('giftvoucher')->__("Remove") . '</a>';
                    $str.='</div>';
                    $str.='</div>';
                }

                $str.='</div>
                </div>
               </div>';
            }
        }
        $fieldset = $form->addFieldset('images_fieldset', array('legend' => Mage::helper('giftvoucher')->__('Upload Images')));
        $fieldset->addField('number_image', 'hidden', array(
            'name' => 'number_image', //declare this as array. Otherwise only one image will be uploaded
        ));

        if (isset($str) && $str != null) {
            $list_image = str_replace(PHP_EOL, '', $str);
            $list_image = preg_replace(array('/\r/', '/\n/'), '', $str);
        } else {
            $list_image = '';
        }
        $fieldset->addField('upload_images', 'hidden', array('name' => 'upload_images', //declare this as array. Otherwise only one image will be uploaded
            'label' => Mage::helper('giftvoucher')->__('Images'),
            'title' => Mage::helper('giftvoucher')->__('Images'),
//            'required' => true,
            'after_element_html' => '<div> 
			<span style="font-family:Arial"><span style="color: #003366">' . Mage::helper('giftvoucher')->__("Click to add file(s)") . '</span></span>
			<button id="addMore" title="Add more image" type="button" class="scalable add" onclick="AddFileUpload()"><span>' . Mage::helper("giftvoucher")->__("Add") . '</span></button>
			<span>&nbsp;&nbsp;' . Mage::helper('giftvoucher')->__('Recommended size: ') . '<span id="giftcard-notes-simple" style="display: none">584x310.</span><span id="giftcard-notes-top" style="display: none">600x190.</span><span id="giftcard-notes-center" style="display: none">600x365.</span><span id="giftcard-notes-left" style="display: none">250x365.</span>' . Mage::helper('giftvoucher')->__('&nbsp;&nbsp;Support gif, jpg, png files.') . '</span>
			<br /><br />
			  <div id="FileUploadContainer">
			  <!--FileUpload Controls will be added here -->               </div> 
			 <br /><br />
			</td>
			</div>
			'
        ));
        $fieldset->addField('list_image', 'hidden', array(
            'name' => 'list_image', //declare this as array. Otherwise only one image will be uploaded
            'label' => Mage::helper('giftvoucher')->__('Images'),
            'title' => Mage::helper('giftvoucher')->__('Images'),
//            'required' => true,
            'after_element_html' => '<div id="fileuploaded" class="">
                <h3>' . Mage::helper("giftvoucher")->__("Uploaded images") . '</h3>
                <div></div>           
</div>' . '<script>
    list_image="' . $list_image . '";
        if(!list_image){
            $("fileuploaded").up("tr").hide();
        }
        else { 
             $("fileuploaded").up("tr").show();
             $("fileuploaded").down("div").update(list_image);
             if($$("#gift-image-carosel img").length>=4){
              carousel=new Carousel("carousel-wrapper", $$("#gift-image-carosel img"), $$("#gift-image-carosel .carousel-control"), {
                                duration: 0.5,
                                transition: "sinoidal",
                                visibleSlides: 4,
                                circular: false
                            });
              }
             }
</script>'
        ));
        $form->setValues($data);
        return parent::_prepareForm();
    }

}
