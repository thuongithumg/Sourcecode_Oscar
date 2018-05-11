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
 * Adminhtml Gifttemplate controller
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Adminhtml_Giftvoucher_GifttemplateController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Initialize action
     *
     * @return Magestore_Giftvoucher_Adminhtml_GifttemplateController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('giftvoucher/gifttemplate')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Gift Template Manager'),
                Mage::helper('adminhtml')->__('Gift  Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Gift Template'))
            ->_title($this->__('Manage Gift Template'));
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Create new Gift template
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * View and edit item action
     */
    public function editAction()
    {
        $tempId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('giftvoucher/gifttemplate')->load($tempId);

        if ($model->getId() || $tempId == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }
            Mage::register('gifttemplate_data', $model);

            $this->loadLayout();
            $this->_setActiveMenu('giftvoucher/gifttemplate');

            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Gift Template Manager'),
                Mage::helper('adminhtml')->__('Gift Template Manager')
            );
            $this->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Template News'), Mage::helper('adminhtml')->__('Template News')
            );

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('giftvoucher/adminhtml_gifttemplate_edit'))
                ->_addLeft($this->getLayout()->createBlock('giftvoucher/adminhtml_gifttemplate_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('giftvoucher')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save item action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $numberImage = $data['number_image'];
            $designPattern = $data['design_pattern'];
            //progress upload multi images

            if (isset($_FILES['images'])) {
                $imgArrays = $this->reArrayFiles($_FILES['images'], $numberImage);
                $imgUploaded = array();
                for ($i = 0; $i < count($imgArrays); $i++) {
                    $_FILES['image_' . $i] = $imgArrays[$i];
                    $image = Mage::helper('giftvoucher')->uploadImage('image_' . $i, $designPattern);
                    if ($image != '') {
                        $imgUploaded[] = $image;
                    }
                }
            }
            //progress upload and delete background image
            if (isset($data['background_img']['delete']) && $data['background_img']['delete'] == 1) {
                Mage::helper('giftvoucher')->deleteImageFile($data['background_img']['value']);
            }
            $background = Mage::helper('giftvoucher')->uploadImage('background_img', $designPattern);
            if ($background || (isset($data['background_img']['delete']) && $data['background_img']['delete'])) {
                $data['background_img'] = $background;
            } else {
                unset($data['background_img']);
            }

            //save data to database
            $model = Mage::getModel('giftvoucher/gifttemplate');
            $id = $this->getRequest()->getParam('id');
            $model->load($id);

            //save image
            if (isset($imgUploaded) && count($imgUploaded)) {
                if ($model->getImages()) {
                    $currenImg = explode(',', $model->getImages());
                }
                if (isset($currenImg) && count($currenImg)) {
                    $arrayImg = array_merge($imgUploaded, $currenImg);
                } else {
                    $arrayImg = $imgUploaded;
                }
                $data['images'] = implode(',', $arrayImg);
            }
            $model->setData($data)->setId($id);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('giftvoucher')->__('Item was successfully saved')
                );
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('giftvoucher')->__('Unable to find item to save')
        );
        $this->_redirect('*/*/');
    }

    /**
     * Delete Gift template in mass number
     */
    public function massDeleteAction()
    {
        $templateIds = $this->getRequest()->getParam('gifttemplate');
        if (!is_array($templateIds)) {
            $this->_getSession()->addError($this->__('Please select template(s).'));
        } else {
            if (!empty($templateIds)) {
                try {
                    foreach ($templateIds as $tempId) {
                        $template = Mage::getSingleton('giftvoucher/gifttemplate')->load($tempId);
                        $template->delete();
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d record(s) have been deleted.', count($templateIds))
                    );
                } catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Delete item action
     */
    public function deleteAction()
    {

        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('giftvoucher/gifttemplate');

                $model->setId($this->getRequest()->getParam('id'))->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );

                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' =>
                    $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Update template(s) status action
     *
     */
    public function massStatusAction()
    {
        $templateIds = $this->getRequest()->getParam('gifttemplate');
        if (!is_array($templateIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($templateIds as $templateId) {
                    Mage::getSingleton('giftvoucher/gifttemplate')
                        ->load($templateId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($templateIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Export grid item to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'gifttemplate.csv';
        $content = $this->getLayout()->createBlock('giftvoucher/adminhtml_gifttemplate_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export grid item to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'gifttemplate.xml';
        $content = $this->getLayout()->createBlock('giftvoucher/adminhtml_gifttemplate_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Remove Gift template's images
     */
    public function removeimageAction()
    {
        $imageName = $this->getRequest()->getParam('value');
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('giftvoucher/gifttemplate')->load($id);
        $type = $model->getDesignPattern();
        $dirImage = Mage::getBaseDir('media') . DS . 'giftvoucher' . DS . 'template' . DS . 'images' .
            DS . $type . '/' . $imageName;
        if (file_exists($dirImage)) {
            $image = Mage::getBaseUrl('media') . 'giftvoucher/template/images/' . $type . '/' . $imageName;
        }

        $images = explode(',', $model->getImages());
        foreach ($images as $key => $value) {
            if ($value == $imageName) {
                unset($images[$key]);
            }
        }
        $images = implode(',', $images);
        $model->setImages($images)->setId($id);
        try {
            $model->save();
        } catch (Exception $exc) {

        }

        if (Mage::helper('giftvoucher')->deleteImageFile($image)) {
            $this->getResponse()->setBody('success');
        } else {
            $this->getResponse()->setBody('false');
        }
    }

    /**
     * Preview by image
     */
    public function previewimageAction()
    {
        $imageName = $this->getRequest()->getParam('value');
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            $giftTemplate = Mage::getModel('giftvoucher/gifttemplate')->load($id);
            if (!$giftTemplate->getId()) {
                $result = Mage::hepler('giftvoucher')->__('This template no longer exists.');
            } else {
                /* Set image for template when admin reviews each of images */
                if ($imageName) {
                    $giftTemplate->setImages($imageName);
                }
                $result = Mage::getModel('giftvoucher/service_processor')->preview(null, $giftTemplate);
            }
        } else {
            $result = Mage::hepler('giftvoucher')->__('This template no longer exists.');
        }
        return $this->getResponse()->setBody($result);
    }

    /**
     * Preview by fold image
     */
    public function previewfoldimageAction()
    {
        $imageName = $this->getRequest()->getParam('value');
        $templage = $this->getRequest()->getParam('form_data');
        $templage = new Varien_Object(json_decode($templage, true));
        $templage->setImages($imageName);
        Mage::register('template_data', $templage);
        $this->loadLayout()->renderLayout();
    }

    /**
     * View demo pattern
     */
    public function viewdemoAction()
    {
        $pattern = $this->getRequest()->getParam('value');
        Mage::register('pattern', $pattern);
        $this->loadLayout()->renderLayout();
    }

    /**
     * Load Images tab
     */
    public function imagesAction()
    {
        $html = $this->getLayout()
            ->createBlock('giftvoucher/adminhtml_gifttemplate_edit_tab_options', 'admin.product.options')->toHtml();

        $this->getResponse()->setBody($html);
    }

    /**
     * Re-arrays multi files
     *
     * @param array $files
     * @param int $fileCount
     * @return array
     */
    protected function reArrayFiles($files, $fileCount)
    {
        if (!empty($files) && is_array($files)) {
            $fileAry = array();
            $fileKeys = array_keys($files);
            for ($i = 0; $i < $fileCount; $i++) {
                foreach ($fileKeys as $key) {
                    $fileAry[$i][$key] = $files[$key][$i];
                }
            }
            return $fileAry;
        }
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('giftvoucher/gifttemplate');
    }

}
