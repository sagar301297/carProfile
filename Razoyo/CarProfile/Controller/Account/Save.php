<?php
namespace Razoyo\CarProfile\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\HTTP\Client\Curl;
use Razoyo\CarProfile\ViewModel\CarData;

class Save extends Action
{
    /**
     * @var Session
     */
    protected $customerSession;

    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var Curl
     */
    protected $curl;

    /**
     * @var CarData
     */
    protected $carData;


    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerFactory $customerFactory,
        JsonFactory $resultJsonFactory,
        Curl $curl,
        CarData $carData
    ) {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->curl = $curl;
        $this->carData = $carData;
        parent::__construct($context);
    }

    /**
     * Execute method
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $result = $this->resultJsonFactory->create();
        $customerId = $this->customerSession->getCustomerId();
        $carSelection = $this->getRequest()->getParam('car');
        $token = $this->getRequest()->getParam('token');
        $carId = $this->getRequest()->getParam('carId');

        if ($carSelection) {
            try {
                $customer = $this->customerFactory->create()->load($customerId);
                $customerDataModel = $customer->getDataModel();
                $customerDataModel->setCustomAttribute('car_selection', $carSelection);
                $customer->updateData($customerDataModel);
                $customer->save();

                return $result->setData(['success' => true, 'message' => __('Car selection saved successfully.')]);
            } catch (\Exception $e) {
                return $result->setData(['success' => false, 'message' => __('An error occurred while saving your car selection.')]);
            }
        } elseif ($token) {
            
            // Get the response body
            $response = $this->getCarDetails($carId, $token);
            
            if (isset($response['error']) && $response['error'] === 'forbidden') {
                $data = $this->carData->getCarData();
                $response = $this->getCarDetails($carId, $data['token']);
            }
            // If not forbidden, return the original response
            return $result->setData([
                'success' => true,
                'carDetails' => $response
            ]);
        }

        return $result->setData(['success' => false, 'message' => __('Invalid customer or car selection.')]);
    }

    /**
     * Get car detail from API
     *
     * @return array
     */
    public function getCarDetails($carId, $token)
    {
        $url = 'https://exam.razoyo.com/api/cars/' . $carId;
    
        // Set the authorization header
        $this->curl->addHeader('Content-Type', 'application/json');
        $this->curl->addHeader('Authorization', 'Bearer ' . $token);
        
        // Make the GET request
        $this->curl->get($url);
        
        // Get the response body
        $response = $this->curl->getBody();
        
        // Decode the JSON response
        return json_decode($response, true);

    }
}
