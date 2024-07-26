<?php
namespace Razoyo\CarProfile\Controller\Account;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\Result\RedirectFactory;

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
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Session $customerSession
     * @param CustomerFactory $customerFactory
     * @param RedirectFactory $resultRedirectFactory
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        CustomerFactory $customerFactory,
        RedirectFactory $resultRedirectFactory
    ) {
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    /**
     * Execute method
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $customerId = $this->customerSession->getCustomerId();
        $carSelection = $this->getRequest()->getParam('car-selection');
        if ($customerId && $carSelection) {
            $customer = $this->customerFactory->create()->load($customerId);
            $customerDataModel = $customer->getDataModel();
            $customerDataModel->setCustomAttribute('car_selection', $carSelection);
            $customer->updateData($customerDataModel);
            $customer->save();
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('carprofile');
        return $resultRedirect;
    }
}
