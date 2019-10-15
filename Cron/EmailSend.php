<?php

namespace Loyalty\CustomerLoyalty\Cron;

use Psr\Log\LoggerInterface;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Loyalty\CustomerLoyalty\Model\ValidationTokenFactory;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderFactory;
use Loyalty\CustomerLoyalty\Model\ResourceModel\Validation\CollectionFactory as ValidationFactory;
use Loyalty\CustomerLoyalty\Model\ResourceModel\ValidationToken\CollectionFactory as ValidationTokenCollectionFactory;

class EmailSend
{

	const EMAIL_SUBJECT_TEXT = 'Order valoration';

	const STORE_CONFIG_SECTION = 'customerloyalty/customer_loyalty/delay_days';

	/**
	 * @var Psr\Log\LoggerInterface
	 */
	protected $logger;

	/**
	 * @var Magento\Framework\App\Config\ScopeConfigInterface
	 */
	protected $scopeConfig;

	/**
	 * @var Magento\Sales\Model\ResourceModel\Order\CollectionFactory
	 */
	protected $orderFactory;

	/**
	 * @var Magento\Store\Model\StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ResourceModel\Validation\CollectionFactory
	 */
	protected $validationFactory;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ValidationTokenFactory
	 */
	protected $validationTokenFactory;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ResourceModel\ValidationToken\CollectionFactory
	 */
	protected $validationTokenCollectionFactory;

	/**
	 * Class Constructor
	 *
	 * @param LoggerInterface                  $logger
	 * @param OrderFactory                     $orderFactory
	 * @param ScopeConfigInterface             $scopeConfig
	 * @param StoreManagerInterface            $_storeManager
	 * @param ValidationFactory                $validationFactory
	 * @param ValidationTokenFactory           $validationTokenFactory
	 * @param ValidationTokenCollectionFactory $validationTokenCollectionFactory
	 */
	public function __construct(
		LoggerInterface $logger,
		OrderFactory $orderFactory,
		ScopeConfigInterface $scopeConfig,
		StoreManagerInterface $_storeManager,
		ValidationFactory $validationFactory,
		ValidationTokenFactory $validationTokenFactory,
		ValidationTokenCollectionFactory $validationTokenCollectionFactory
	) {
		$this->logger = $logger;
		$this->scopeConfig = $scopeConfig;
		$this->orderFactory = $orderFactory;
		$this->_storeManager = $_storeManager;
		$this->validationFactory = $validationFactory;
		$this->validationTokenFactory = $validationTokenFactory;
		$this->validationTokenCollectionFactory = $validationTokenCollectionFactory;
	}

	/**
	 * @TODO CATCH EXCEPTIONS!
	 */
	public function execute()
	{
		$orderIds = $this->getOrderIds();
		$dateLimit = $this->getDateLimit($this->getStoreConfigDays());
		$this->processOrderCollection($this->getOrderCollection($dateLimit, $orderIds));
	}

	/**
	 * Processing Order Collection
	 *
	 * @param  Magento\Sales\Model\ResourceModel\Order\Collection $collection
	 */
	private function processOrderCollection($collection)
	{
		foreach ($collection as $order) {
			$customerToken = $this->customerTokenGenerator();

			if ($this->sendEmail($order->getCustomerEmail(), $customerToken, $order->getIncrementId())) {
				if (!$this->tableTokenPersistData($customerToken, $order->getId()) instanceof ValidationToken) {
					$this->logger->error(sprintf('Error sending email from cron to order %s - %s', $order->getId(), $order->getIncrementId()));
				}
			}
		}
	}

	/**
	 * Persisting data into `customerloyalty_sent_token`
	 *
	 * @param  string $token
	 * @param  string $orderId
	 *
	 * @return int
	 */
	private function tableTokenPersistData(string $token, string $orderId)
	{
		return $this->validationTokenFactory->create()->setData([
					'order_id' => $orderId,
					'token' => $token
				])->save();
	}

	/**
	 * Sending email to customer.
	 *
	 * @param  string $customerEmail
	 *
	 * @return bool
	 */
	private function sendEmail(string $customerEmail, string $token, $orderNumber)
	{
		return mail($customerEmail, self::EMAIL_SUBJECT_TEXT, sprintf('Estimado cliente, podrá realizar una valoración del pedido Nº%s en la siguiente dirección %s.', $orderNumber, $this->getUrlReview($token)));
	}

	/**
	 * Builds URL for customer review.
	 *
	 * @param  string $token
	 *
	 * @return string
	 */
	private function getUrlReview(string $token)
	{
		return sprintf('%sreview/new/%s', $this->_storeManager->getStore()->getBaseUrl(), $token);
	}

	/**
	 * Generate token for customer
	 *
	 * @return string
	 */
	private function customerTokenGenerator()
	{
		return substr(md5(time()), 0, 10);
	}

	/**
	 * Getting IDs from Orders
	 *
	 * @return array
	 */
	private function getOrderIds()
	{
		$orderIds = [];
		$validationTokenCollectionFactory = $this->validationTokenCollectionFactory->create();

		foreach ($validationTokenCollectionFactory as $validation) {
			array_push($orderIds, $validation->getOrderId());
		}

		return $orderIds;
	}

	/**
	 * Getting Order Collection
	 *
	 * @param  string $date
	 * @param  array  $orderIds
	 *
	 * @return Magento\Sales\Model\ResourceModel\Order\Collection
	 */
	/** @TODO ELIMINAR COMENTARIOS */
	private function getOrderCollection(string $date, array $orderIds)
	{
		$orderCollection = $this->orderFactory->create()
								->addAttributeToSelect('*')
								->addFieldToFilter('status', ['eq' => 'closed'])
								// ->addFieldToFilter('status', ['eq' => 'complete'])
								->addFieldToFilter('created_at', ['lteq' => $date])
								->addFieldToFilter('entity_id', ['nin' => implode(',', $this->getOrderIds())]);

		return $orderCollection;
	}

	/**
	 * Getting the days from admin to build DateLimit.
	 *
	 * @return string
	 */
	private function getStoreConfigDays()
	{
		return $this->scopeConfig->getValue(self::STORE_CONFIG_SECTION, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * Builds the limit date to get orders.
	 *
	 * @param  string|null $storeConfigDays
	 *
	 * @return string
	 */
	private function getDateLimit(string $storeConfigDays = null)
	{
		if (null === $storeConfigDays) {
            $this->logger->error('Days to send e-mail not setted on admin.');
            throw new \InvalidArgumentException('Days to send e-mail not setted on admin.');
		}

		return date('Y-m-d 23:59:59',(strtotime(sprintf('-%s day', $storeConfigDays) , strtotime(date('Y-m-d')))));
	}

}
