<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Integration\Response;

/**
 * Class Storecredit
 * @package Magestore\Webpos\Model\Integration\Data
 */
class Storecredit extends \Magento\Framework\Model\AbstractExtensibleModel implements
    \Magestore\Webpos\Api\Integration\Response\StorecreditInterface
{
    /**
     * Get balance
     *
     * @api
     * @return string
     */
    public function getBalance()
    {
        return $this->getData(self::BALANCE);
    }

    /**
     * Set balance
     *
     * @api
     * @param string $balance
     * @return $this
     */
    public function setBalance($balance)
    {
        return $this->setData(self::BALANCE, $balance);
    }
}
