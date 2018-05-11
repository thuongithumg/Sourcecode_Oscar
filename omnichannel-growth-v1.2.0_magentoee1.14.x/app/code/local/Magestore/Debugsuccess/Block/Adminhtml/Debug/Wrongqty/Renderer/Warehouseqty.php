<?php
class Magestore_Debugsuccess_Block_Adminhtml_Debug_Wrongqty_Renderer_Warehouseqty extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {
    public function render(Varien_Object $row) {

         $product_id = $row->getProductId();
         $warehouseId = $this->getColumn()->getValue();

         $totalQty = round($row->getData('sum_total_qty_'.$warehouseId),2);
         $avaiqty = round($row->getData('available_qty_'.$warehouseId),2);
         $onholdQty = round($row->getData('sum_qty_to_ship_'.$warehouseId),2);
            $html='';
            $html .= '<span id="total_qty_msg_'.$product_id.'_'.$warehouseId.'" > Total : '.$totalQty.' </span></br>';
            $html .= '<span id="avail_qty_msg_'.$product_id.'_'.$warehouseId.'" > Avail : '.$avaiqty.' </span></br>';
            $html .= '<span id="onhold_qty_msg_'.$product_id.'_'.$warehouseId.'" >Holds : '.$onholdQty.' </span></br>';
        return $html;
    }
}