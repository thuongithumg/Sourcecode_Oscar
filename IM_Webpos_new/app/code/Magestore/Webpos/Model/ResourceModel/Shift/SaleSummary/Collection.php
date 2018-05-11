<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Created by PhpStorm.
 * User: steve
 * Date: 06/06/2016
 * Time: 14:06
 */

namespace Magestore\Webpos\Model\ResourceModel\Shift\SaleSummary;


class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{


    const STATUS_COMPLETED = "compeleted";

    /** @var  string $sales_order_table table name for sales_order */
    protected $sales_order_table;

    /** @var  string $sales_order_payment_table table name for sales_order_payment */
    protected $sales_order_payment_table;

    /** @var  string $sales_order_status_table table name for sales_order_status */
    protected $sales_order_status_table;


    protected $shift_id;

    protected function _construct()
    {
        $this->_init('Magestore\Webpos\Model\Shift\SaleSummary', 'Magento\Sales\Model\ResourceModel\Order');
    }

    /**
     * @param int $shift_id
     */
    public function setShiftId($shift_id)
    {
        $this->shift_id = $shift_id;
    }


    /**
     * get sales summary for a shift
     * Sales is group by payment method
     * fields to show data: payment method name, sales, refunds, net
     *
     * @param int $shift_id
     * @return mixed
     */
   

    protected function _beforeLoad()
    {
        $data = [];

        $this->sales_order_table = "main_table";
        $this->webpos_order_payment_table = $this->getTable("webpos_order_payment");
        $this->sales_order_status_table = $this->getTable("sales_order_status");

        $this->getSelect()
            ->join(array('payment' => $this->webpos_order_payment_table), $this->sales_order_table . '.entity_id= payment.order_id',
                array('payment_method' => 'payment.method',
                    'method_title' => 'payment.method_title',
                    'sum(base_payment_amount)' => 'payment.base_payment_amount',
                    'sum(payment_amount)' => 'payment.payment_amount',
                    'order_id' => $this->sales_order_table.'.entity_id'
                    )
                );

        $this->getSelect()->join(array('order_status' => $this->sales_order_status_table),
            $this->sales_order_table.'.status = order_status.status',
            array('label' => 'order_status.label')
        );
        $this->getSelect()->group("payment_method");
        $this->getSelect()->where($this->sales_order_table .'.shift_id='.$this->shift_id);
    }

    


}