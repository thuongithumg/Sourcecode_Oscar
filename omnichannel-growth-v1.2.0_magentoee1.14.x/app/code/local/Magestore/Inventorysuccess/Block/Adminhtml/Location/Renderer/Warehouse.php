<?php

class Magestore_Inventorysuccess_Block_Adminhtml_Location_Renderer_Warehouse
     extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {


    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $warehouses = Magestore_Coresuccess_Model_Service::locationService()->getWarehouseOptionArray();

        $html = '<select name="warehouse_id" id="location_'.$row->getLocationId() .'" 
                  onclick="event.preventDefault(); event.stopPropagation();"'
                . '>';
        foreach ($warehouses as $key => $value){
            if($key == $row->getWarehouseId()) {
                $html .= '<option value="' . $key . '" selected="selected">' . $value . '</option>';
            }else{
                $html .= '<option value="' . $key . '">' . $value . '</option>';
            }
        }
        $html .= '</select>';
        
        return $html;
    }
}

