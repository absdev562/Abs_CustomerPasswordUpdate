<?php
namespace Abs\CustomerPasswordUpdate\Controller\Adminhtml\Customer;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Request\Http;
use Psr\Log\LoggerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\CustomerRegistry;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Json\Helper\Data;

/**
 * Password Update Class
 */

class Update extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var Data
     */
    protected $jsonHelper;

    /**
     * @var Http
     */
    protected $request;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * @var EncryptorInterface
     */
    protected $encryptor;

    /**
     * Constructor
     *
     * @param Context                     $context
     * @param PageFactory                 $resultPageFactory
     * @param Data                        $jsonHelper
     * @param Http                        $request
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerRegistry            $customerRegistry
     * @param EncryptorInterface          $encryptor
     * @param LoggerInterface             $logger
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        Data $jsonHelper,
        Http $request,
        CustomerRepositoryInterface $customerRepository,
        CustomerRegistry $customerRegistry,
        EncryptorInterface $encryptor,
        LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        $this->request = $request;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->encryptor = $encryptor;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $postData = $this->request->getParams();
            $customerId = $postData['valId'];
            $customerNewPassword = $postData['textValue'];

            $customer = $this->customerRepository->getById($customerId);
            $customerSecureRegistry = $this->customerRegistry->retrieveSecureData($customerId);
            $customerSecureRegistry->setRpToken(null);
            $customerSecureRegistry->setRpTokenCreatedAt(null);
            $customerSecureRegistry->setPasswordHash($this->createPasswordHash($customerNewPassword));
            $this->customerRepository->save($customer, $this->createPasswordHash($customerNewPassword));

            return $this->jsonResponse('1');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse('0');
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse('0');
        }
    }

    /**
     * Create json response
     *
     * @param string $response
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

    /**
     * Create password hash
     *
     * @param string $password
     * @return string
     */
    protected function createPasswordHash(string $password)
    {
        return $this->encryptor->getHash($password, true);
    }
}
