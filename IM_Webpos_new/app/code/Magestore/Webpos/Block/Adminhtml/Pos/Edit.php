<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Block\Adminhtml\Pos;

/**
 * Class Edit
 * @package Magestore\Webpos\Block\Adminhtml\Pos
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * @var \Magento\Framework\Registry|null
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'Magestore_Webpos';
        $this->_controller = 'adminhtml_pos';

        parent::_construct();

        $this->buttonList->update('save', 'label', __('Save'));
        $this->buttonList->update('delete', 'label', __('Delete'));
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


        if ($this->_coreRegistry->registry('current_pos')->getId()) {
            $currentPos = $this->_coreRegistry->registry('current_pos');
            if ($currentPos->getData('is_allow_to_lock')
                && $currentPos->getData('status') != \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED && $this->_isAllowed()) {
                $this->buttonList->add(
                    'lock',
                    array(
                        'label' => __('Lock'),
                        'class' => 'lock',
                        'on_click' => '',
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => [
                                    'event' => 'save',
                                    'target' => '#edit_form',
                                    'eventData' => ['action' => ['args' => ['lock' => \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED]]],
                                ],
                            ],
                        ]
                    ),
                    -100
                );
            }
            if ($currentPos->getData('is_allow_to_lock')
                && $currentPos->getData('status') == \Magestore\Webpos\Model\Pos\Status::STATUS_LOCKED && $this->_isAllowed()) {
                $this->buttonList->add(
                    'unlock',
                    array(
                        'label' => __('Unlock'),
                        'class' => 'Unlock',
                        'on_click' => '',
                        'data_attribute' => [
                            'mage-init' => [
                                'button' => [
                                    'event' => 'save',
                                    'target' => '#edit_form',
                                    'eventData' => ['action' => ['args' => ['unlock' => \Magestore\Webpos\Model\Pos\Status::STATUS_ENABLED]]],
                                ],
                            ],
                        ]
                    ),
                    -100
                );
            }
        }
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('current_pos')->getId()) {
            return __("Edit POS '%1'", $this->escapeHtml($this->_coreRegistry->registry('current_pos')->getData('display_name')));
        } else {
            return __('New POS');
        }
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magestore_Webpos::lock_register');
    }
}