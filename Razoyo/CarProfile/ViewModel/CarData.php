<?php
declare(strict_types=1);

namespace Razoyo\CarProfile\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\HTTP\ClientInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Customer\Model\Session;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Pricing\Helper\Data as PricingHelper;

class CarData implements ArgumentInterface
{
    private const API_URL = 'https://exam.razoyo.com/api/cars';
    private const TOKEN_HEADER = 'your-token';
    private const CAR_SELECTION_ATTRIBUTE = 'car_selection';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var Json
     */
    private $jsonSerializer;

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
     * @param ClientInterface $httpClient
     * @param Json $jsonSerializer
     * @param Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param PricingHelper $pricingHelper
     */
    public function __construct(
        ClientInterface $httpClient,
        Json $jsonSerializer,
        Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        PricingHelper $pricingHelper
    ) {
        $this->httpClient = $httpClient;
        $this->jsonSerializer = $jsonSerializer;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->pricingHelper = $pricingHelper;
    }

    /**
     * Get car data from API
     *
     * @return array
     */
    public function getCarData(): array
    {
        try {
            $this->httpClient->get(self::API_URL);
            $response = $this->httpClient->getBody();
            $data = $this->jsonSerializer->unserialize($response);
            
            $headers = $this->httpClient->getHeaders();
            $token = $headers[self::TOKEN_HEADER] ?? null;
            
            $data['token'] = $token;
            return $data;
        } catch (\Exception $e) {
            // Log the error or handle it as per your requirements
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get selected car from customer profile
     *
     * @return string|null
     */
    public function getSelectedCar(): ?string
    {
        $customerId = $this->customerSession->getCustomerId();
        if (!$customerId) {
            return null;
        }

        try {
            $customer = $this->customerRepository->getById($customerId);
            $carSelectionAttribute = $customer->getCustomAttribute(self::CAR_SELECTION_ATTRIBUTE);
            return $carSelectionAttribute ? $carSelectionAttribute->getValue() : null;
        } catch (\Exception $e) {
            // Log the error or handle it as per your requirements
            return null;
        }
    }

    /**
     * Get formatted price
     *
     * @param float $price
     * @return string
     */
    public function getFormattedPrice(float $price): string
    {
        return $this->pricingHelper->currency($price, true, false);
    }
}