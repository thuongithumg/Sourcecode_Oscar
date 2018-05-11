<?php

class Magestore_Storepickup_Helper_Region extends Mage_Core_Helper_Abstract
{
    /**
     * error state code
     */
    const STATE_ERROR = -1;
    /**
     * @param $country_id
     * @param $state_name
     * @return int
     */
    public function validateState($country_id, $state_name){
        $collection = Mage::getResourceModel('directory/region_collection');
        $collection->addCountryFilter($country_id);

        if($state_name == ''){
            return self::STATE_ERROR;
        }
        
        if(sizeof($collection) > 0){
            $region_id = self::STATE_ERROR;
            foreach ($collection as $region){
                if(strcasecmp($state_name,$region->getData('name')) == 0){
                    $region_id = $region->getId();
                    break;
                }
            }
            return $region_id;
        } else {
            return 0;
        }
    }

    /**
     * @return mixed value
     */
    public function getStateRequireConfig(){
        return Mage::getStoreConfig('general/region/display_all');
    }

    /**
     * @return mixed
     */
    public function getCountriesConfig(){
        return Mage::getStoreConfig('general/region/state_required');
    }
}
