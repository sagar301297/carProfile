<?php
namespace Razoyo\CarProfile\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class CarData implements ArgumentInterface
{
    /**
     * @var Curl
     */
    private $curl;

    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var PricingHelper
     */
    private $pricingHelper;

    /**
     * Constructor
     *
     * @param Curl $curl
     * @param JsonHelper $jsonHelper
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param PricingHelper $pricingHelper
     */
    public function __construct(
        Curl $curl,
        JsonHelper $jsonHelper,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        PricingHelper $pricingHelper
    ) {
        $this->curl = $curl;
        $this->jsonHelper = $jsonHelper;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->pricingHelper = $pricingHelper;
    }


    /**
     * Get car data from API
     *
     * @return array
     */
    public function getCarData()
    {
        $url = 'https://exam.razoyo.com/api/cars';
        $this->curl->get($url);
        $response = $this->curl->getBody();
        $data = $this->jsonHelper->jsonDecode($response);

        // Get the token from the response headers
        $headers = $this->curl->getHeaders();
        $token = isset($headers['your-token']) ? $headers['your-token'] : null;

        // Add the token to the data
        $data['token'] = $token;
        return $data;
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
        return $this->jsonHelper->jsonDecode($response);
        

    }

    /**
     * Get selected car from customer profile
     *
     * @return string|null
     */
    public function getSelectedCar()
    {
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $carSelectionAttribute = $customer->getCustomAttribute('car_selection');
            $selectedCarId = $carSelectionAttribute ? $carSelectionAttribute->getValue() : null;
        }
        return $selectedCarId;

    }

     /**
     * Get formatted price
     *
     * @param float $price
     * @return string
     */
    public function getFormattedPrice($price)
    {
        return $this->pricingHelper->currency($price, true, false);
    }
}
