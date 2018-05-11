<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Ui\Component\MassAction\Status;

use Magento\Framework\UrlInterface;
use Zend\Stdlib\JsonSerializable;

/**
 * class \Magestore\Webpos\Ui\Component\MassAction\Status\Options
 * 
 * Web POS MassAction Status Options
 * Methods:
 *  jsonSerialize
 *  prepareData
 * 
 * @category    Magestore
 * @package     Magestore\Webpos\Ui\Component\MassAction\Status
 * @module      Webpos
 * @author      Magestore Developer
 */
class Options implements JsonSerializable
{
    /**
     * @var array
     */
    protected $_options;

    /**
     * Additional options params
     *
     * @var array
     */
    protected $_data;

    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Base URL for subactions
     *
     * @var string
     */
    protected $_urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    protected $_paramName;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    protected $_additionalData = [];

    /**
     * 
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->_data = $data;
        $this->_urlBuilder = $urlBuilder;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->_options === null) {
            $options = array(
                array(
                    "value" => "1",
                    "label" => ('Enabled'),
                ),
                array(
                    "value" => "2",
                    "label" => ('Disabled'),
                )
            );
            $this->prepareData();
            foreach ($options as $optionCode) {
                $this->_options[$optionCode['value']] = [
                    'type' => 'status_' . $optionCode['value'],
                    'label' => $optionCode['label'],
                ];

                if ($this->_urlPath && $this->_paramName) {
                    $this->_options[$optionCode['value']]['url'] = $this->_urlBuilder->getUrl(
                        $this->_urlPath,
                        [$this->_paramName => $optionCode['value']]
                    );
                }

                $this->_options[$optionCode['value']] = array_merge_recursive(
                    $this->_options[$optionCode['value']],
                    $this->_additionalData
                );
            }
            $this->_options = array_values($this->_options);
        }
        return $this->_options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    protected function prepareData()
    {
        foreach ($this->_data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->_urlPath = $value;
                    break;
                case 'paramName':
                    $this->_paramName = $value;
                    break;
                default:
                    $this->_additionalData[$key] = $value;
                    break;
            }
        }
    }
}
