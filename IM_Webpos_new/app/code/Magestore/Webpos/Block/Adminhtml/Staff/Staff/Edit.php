<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Block\Adminhtml\Staff\Staff;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry = null;

    protected $posSessionFactory;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\Staff\WebPosSessionFactory $posSessionFactory,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->posSessionFactory = $posSessionFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magestore_Webpos';
        $this->_controller = 'adminhtml_staff_staff';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));
        if ($this->_coreRegistry->registry('current_staff')->getId()) {
            $staffId = $this->_coreRegistry->registry('current_staff')->getId();
            $webposSession = $this->posSessionFactory->create()->load($staffId, 'staff_id');
            if ($webposSession->getId()){
                $this->buttonList->add(
                    'signout',
                    array(
                        'label' => __('Force Sign-out'),
                        'class' => 'signout',
                        'on_click' => '',
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => [
                                    'event' => 'save',
                                    'target' => '#edit_form',
                                    'eventData' => ['action' => ['args' => ['signout' => 1]]],
                                ],
                            ],
                        ]
                    ),
                    -100
                );
            }
        }
        $this->buttonList->add(
            'saveandcontinue',
            array(
                'label' => __('Save and Continue Edit'),
                'class' => 'save',
                'data_attribute' => array(
                    'mage-init' => array('button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'))
                )
            ),
            -100
        );

    }

    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_staff')->getId()) {
            return __("Edit Staff '%1'", $this->escapeHtml($this->_coreRegistry->registry('current_staff')->getData('display_name')));
        } else {
            return __('New Staff');
        }
    }
}
