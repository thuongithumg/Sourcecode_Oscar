<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Pos
 * @package Magestore\Webpos\Ui\Component\Listing\Column
 */
class Pos implements OptionSourceInterface
{

    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection
     */
    protected $webposPos;

    /**
     * Pos constructor.
     * @param \Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection $webposPos
     */
    public function __construct(
        \Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection $webposPos
    ) {
        $this->webposPos = $webposPos;
    }

    /**
     * Get product type labels array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        $poss = $this->webposPos->toOptionArray();
        if (count($poss) > 0) {
            foreach ($poss as $pos) {
                $options[$pos['value']] = (string) $pos['label'];
            }
        }
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->getOptions();
    }

    /**
     * Get product type labels array for option element
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->webposPos->toOptionArray();
    }

}
