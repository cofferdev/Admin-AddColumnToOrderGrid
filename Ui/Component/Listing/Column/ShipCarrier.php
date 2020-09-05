<?php

namespace Mageassist\Customgrid\Ui\Component\Listing\Column;

class ShipCarrier implements \Magento\Framework\Option\ArrayInterface {

    protected $_storeManager; 
    protected $_shippingConfig;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,   
        \Magento\Shipping\Model\Config $shippingConfig
         
    ) {
        $this->_storeManager = $storeManager;
        $this->_shippingConfig = $shippingConfig;
        
    }

    public function toOptionArray() {

        $carriers = [];
        $carrierInstances = $this->_getCarriersInstances();
        foreach ($carrierInstances as $code => $carrier) {
            if ($carrier->isTrackingAvailable()) {
                $title = $carrier->getConfigData('title');
              $carriers[] = array('value' =>$code, 'label' => $title);
            }
        }
        array_push($carriers, array("value"=>"custom","label"=>"Custom Value"));
        return $carriers;
    }
    
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }
    protected function _getCarriersInstances()
    {
        return $this->_shippingConfig->getAllCarriers($this->getStoreId());
    }

}
