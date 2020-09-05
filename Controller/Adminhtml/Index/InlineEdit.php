<?php
namespace Mageassist\Customgrid\Controller\Adminhtml\Index;

class InlineEdit extends \Magento\Backend\App\Action
{

    protected $jsonFactory;
    protected $resourceConnection;
    protected $scopeConfig;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->resourceConnection = $resourceConnection;
        $this->scopeConfig = $scopeConfig;
    }

    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            } else {
                foreach ($postItems as $data) {
                    $sql = "UPDATE `sales_shipment_track` SET `track_number` = ".$data['track_number']." WHERE `sales_shipment_track`.`order_id` = ".$data['entity_id']." ";
                    $this->resourceConnection->getConnection()->query($sql);
                    
                    $carrierTitle = $this->getCarrierTitle($data['carrier_code']);
                    
                    $updateCarrierCode = "UPDATE `sales_shipment_track` SET `title` = '".$carrierTitle."', `carrier_code` = '".$data['carrier_code']."'  WHERE `sales_shipment_track`.`order_id` = ".$data['entity_id']." ";
                    $this->resourceConnection->getConnection()->query($updateCarrierCode);
                }
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
    public function getCarrierTitle($carrierCode)
    {
        $carrierTitle = $this->scopeConfig->getValue(
                'carriers/' . $carrierCode . '/title',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        return $carrierTitle;
    }
}