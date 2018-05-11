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
 * Adminhtml Giftvoucher Generategiftcard controller
 *
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
 */
class Magestore_Giftvoucher_Adminhtml_Giftvoucher_GenerategiftcardController extends Mage_Adminhtml_Controller_Action
{

    /**
     * Export to CSV type
     */
    public function exportCsvAction()
    {
        $fileName = 'generatetemplate_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.csv';
        $content = $this->getLayout()->createBlock('giftvoucher/adminhtml_generategiftcard_grid')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export to XML type
     */
    public function exportXmlAction()
    {
        $fileName = 'generatetemplate_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.xml';
        $content = $this->getLayout()->createBlock('giftvoucher/adminhtml_generategiftcard_grid')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export item(s) to CSV type
     */
    public function exportGiftCodeCsvAction()
    {
        $fileName = 'giftcode_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.csv';
        $content = $this->getLayout()
            ->createBlock('giftvoucher/adminhtml_generategiftcard_edit_tab_giftcodelist')->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export item(s) to XML type
     */
    public function exportGiftCodeXmlAction()
    {
        $fileName = 'giftcode_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.xml';
        $content = $this->getLayout()
            ->createBlock('giftvoucher/adminhtml_generategiftcard_edit_tab_giftcodelist')->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }

    /**
     * Export item(s) to PDF type
     */
    public function exportGiftCodePdfAction()
    {
        $fileName = 'giftcode_' . Mage::getSingleton('core/date')->date('Y-m-d_H-i-s') . '.pdf';
        $pdf = $this->getLayout()
            ->createBlock('giftvoucher/adminhtml_generategiftcard_edit_tab_giftcodelist')->getPdf();
        $this->_prepareDownloadResponse($fileName, $pdf->render(), 'application/pdf');
    }

    /**
     * Initialize action
     *
     * @return Magestore_Giftvoucher_Adminhtml_GenerategiftcardController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('giftvoucher/giftvoucher')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Generate Manager'),
                Mage::helper('adminhtml')->__('Template Manager'));

        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Template'))
            ->_title($this->__('Manage Template'));
        $this->_initAction()
            ->renderLayout();
    }

    /**
     * Edit action
     */
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('giftvoucher/template')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }


            $this->_title($this->__('Edit Template'));
            if ($model->getId()) {
                $this->_title($model->getGiftCode());
            } else {
                $this->_title($this->__('New Template'));
            }
            $model->getConditions()->setJsFormObject('giftvoucher_conditions_fieldset');
            Mage::register('template_data', $model);
            $this->loadLayout();
            $this->_setActiveMenu('giftvoucher/giftvoucher');

            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Template Manager'),
                Mage::helper('adminhtml')->__('Template Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Template News'),
                Mage::helper('adminhtml')->__('Template News'));

            $this->getLayout()->getBlock('head')
                ->setCanLoadExtJs(true)
                ->setCanLoadRulesJs(true);

            $this->_addContent($this->getLayout()->createBlock('giftvoucher/adminhtml_generategiftcard_edit'))
                ->_addLeft($this->getLayout()->createBlock('giftvoucher/adminhtml_generategiftcard_edit_tabs'));

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('giftvoucher')->__('Template does not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Create action
     */
    public function newAction()
    {
        $this->_forward('edit');
    }

    /**
     * Save action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('giftvoucher/template');

            $data = $this->_filterDates($data, array('expired_at'));
            if (!$data['expired_at']) {
                $data['expired_at'] = null;
            }

            if (isset($data['rule'])) {
                $rules = $data['rule'];
                if (isset($rules['conditions'])) {
                    $data['conditions'] = $rules['conditions'];
                }
                unset($data['rule']);
            }

            if (!Mage::helper('giftvoucher')->isExpression($data['pattern'])) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftvoucher')->__('Invalid pattern'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            $model->addData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                $model->loadPost($data);
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('giftvoucher')->__('The pattern has been saved successfully.'));
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
            Mage::helper('giftvoucher')->__('Unable to find Template to save'));
        $this->_redirect('*/*/');
    }

    /**
     * Generate action
     */
    public function generateAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('giftvoucher/template');
            if ($model->getIsGenerated()) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('giftvoucher')->__('Each template only generate a time'));
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
            $data = $this->_filterDates($data, array('expired_at'));
            if (!$data['expired_at']) {
                $data['expired_at'] = null;
            }

            if (isset($data['rule'])) {
                $rules = $data['rule'];
                if (isset($rules['conditions'])) {
                    $condition = $rules['conditions'];
                }
                $data['conditions'] = $condition;
                unset($data['rule']);
            }

            if (!Mage::helper('giftvoucher')->isExpression($data['pattern'])) {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('giftvoucher')->__('Invalid pattern'));
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                $model->loadPost($data);
                $model->setIsGenerated(1)->save();
                Mage::getSingleton('adminhtml/session')->setFormData(false);
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            try {
                $data = $model->getData();
                $data['conditions'] = $condition;
                $data['gift_code'] = $data['pattern'];
                $data['template_id'] = $model->getId();
                $data['amount'] = $data['balance'];
                $data['status'] = Magestore_Giftvoucher_Model_Status::STATUS_ACTIVE;
                $data['extra_content'] = Mage::helper('giftvoucher')->__('Created by %s',
                    Mage::getSingleton('admin/session')->getUser()->getUsername());
                $amount = $model->getAmount();
                for ($i = 1; $i <= $amount; $i++) {
                    Mage::getModel('giftvoucher/giftvoucher')
                        ->setData($data)
                        ->loadPost($data)
                        ->setIncludeHistory(true)
                        ->save();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('giftvoucher')->__('The pattern has been saved and generated successfully.'));
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                $model->setIsGenerated(0)->save();
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('giftvoucher')->__('Unable to find Template to save'));
        $this->_redirect('*/*/');
    }

    /**
     * Duplicate action
     */
    public function duplicateAction()
    {
        if ($this->getRequest()->getParam('id')) {
            $model = Mage::getModel('giftvoucher/template');

            $data = $model->load($this->getRequest()->getParam('id'))->getData();
            $data['is_generated'] = 0;
            unset($data['template_id']);

            $model->setData($data);

            try {
                $model->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('giftvoucher')->__('The pattern has been duplicated successfully.'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(
            Mage::helper('giftvoucher')->__('Unable to find Template to duplicate'));
        $this->_redirect('*/*/');
    }

    /**
     * Delete action
     */
    public function deleteAction()
    {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $model = Mage::getModel('giftvoucher/template');

                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('The pattern has been deleted successfully.'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete in mass number
     */
    public function massDeleteAction()
    {
        $templateIds = $this->getRequest()->getParam('template');
        if (!is_array($templateIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('adminhtml')->__('Please select Template(s)'));
        } else {
            try {
                foreach ($templateIds as $templateId) {
                    $template = Mage::getModel('giftvoucher/template')->load($templateId);
                    $template->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($templateIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Show gift code list
     */
    public function GiftcodelistAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('giftvoucher/adminhtml_generategiftcard_edit_tab_giftcodelist')->toHtml()
        );
    }

    /**
     * Load gift code's image
     */
    public function giftimagesAction()
    {
        $templateId = $this->getRequest()
            ->getParam('gift_template_id');
        $currentImage = $this->getRequest()
            ->getParam('current_image');
        if (!$templateId) {
            $this->getResponse()->setBody('');
            return;
        }
        $template = Mage::getModel('giftvoucher/gifttemplate')->load($templateId);
        $images = $template->getImages();
        $str = '';
        if ($images) {
            $str .= '<div class="carousel" id="gift-image-carosel">
                            <a href="javascript:" class="carousel-control next" rel="next">›</a>
                            <a href="javascript:" class="carousel-control prev" rel="prev">‹</a>
                            <div class="gift-middle" id="carousel-wrapper">
                                <div class="inner" style="width: 3000px;">
                  ';

            $images = explode(',', $images);
            $count = 0;
            foreach ($images as $image) {
                $str .= '<div id="div-image-for-' . $templateId . '-' . $count .
                    '" style="position:relative; float: left;border: 2px solid white;">';
                $str .= '<img src="' . Mage::getBaseUrl("media") . 'giftvoucher/template/images/' . $template->getDesignPattern() . '/' . $image .
                    '" alt="" style="width:80px;height:80px"
                    onclick="changeSelectImages(' . $count . ',\'' . $image . '\')">';
                $str .= '<div class="egcSwatch-arrow" style="display:none"></div>';
                $str .= '</div>';
                if ($image == $currentImage) {
                    $selectImage = $count;
                }
                $count++;
            }
            if ($currentImage) {
                $str .= '<input type="hidden" id="current_image" value=' . $currentImage . '>';

                $str .= '<input type="hidden" id="selected_image" value=' . $selectImage . '>';
            } else {
                $str .= '<input type="hidden" id="current_image" value=' . $images[0] . '>';

                $str .= '<input type="hidden" id="selected_image" value="0">';
            }
            $str .= '</div>
                </div>
               </div>';
        }
        $this->getResponse()->setBody($str);
        return;
    }

    /**
     * @return mixed
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('giftvoucher/generategiftcard');
    }

}
