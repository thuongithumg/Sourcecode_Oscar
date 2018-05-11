<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Pos;
/**
 * Class Status
 * @package Magestore\Webpos\Model\Pos
 */
class Status implements \Magento\Framework\Data\OptionSourceInterface
{
    const STATUS_ENABLED = 1;
    const STATUS_DISABLED = 2;
    const STATUS_LOCKED = 3;

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Enabled'), 'value' => self::STATUS_ENABLED],
            ['label' => __('Disabled'), 'value' => self::STATUS_DISABLED],
            ['label' => __('Locked'), 'value' => self::STATUS_LOCKED]
        ];
    }

    /**
     * Return array of options as value-label pairs.
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function getStatusArray($isAllow)
    {
        $statuses = [
            ['label' => __('Enabled'), 'value' => self::STATUS_ENABLED],
            ['label' => __('Disabled'), 'value' => self::STATUS_DISABLED]
        ];
        if ($isAllow) {
            $statuses[] = ['label' => __('Locked'), 'value' => self::STATUS_LOCKED];
        }
        return $statuses;
    }

    /**
     * Return array of options as key-value pairs.
     *
     * @return array Format: array('<key>' => '<value>', '<key>' => '<value>', ...)
     */
    public function toOptionHash()
    {
        return [
            self::STATUS_ENABLED => __('Enabled'),
            self::STATUS_DISABLED => __('Disabled'),
            self::STATUS_LOCKED => __('Locked')
        ];
    }
}