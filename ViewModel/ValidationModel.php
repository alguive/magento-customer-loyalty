<?php

namespace Loyalty\CustomerLoyalty\ViewModel;

use Loyalty\CustomerLoyalty\Model\ResourceModel\Validation as ValidationResource;
use Loyalty\CustomerLoyalty\Model\ResourceModel\Validation\CollectionFactory;
use Loyalty\CustomerLoyalty\Model\ValidationFactory;
use Loyalty\CustomerLoyalty\Model\ValidationTokenFactory;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\OrderFactory;

class ValidationModel implements ArgumentInterface
{

	/**
	 * @var Magento\Sales\Model\OrderFactory
	 */
	protected $orderFactory;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ResourceModel\Validation\CollectionFactory
	 */
	protected $collectionFactory;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ValidationFactory
	 */
	protected $validationFactory;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ResourceModel\Validation
	 */
	protected $validationResource;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ValidationTokenFactory
	 */
	protected $validationTokenFactory;


	public function __construct
	(
		OrderFactory $orderFactory,
		ValidationFactory $validationFactory,
		ValidationResource $validationResource,
		CollectionFactory $collectionFactory,
		ValidationTokenFactory $validationTokenFactory
	)
	{
		$this->orderFactory = $orderFactory;
		$this->collectionFactory = $collectionFactory;
		$this->validationFactory = $validationFactory;
		$this->validationResource = $validationResource;
		$this->validationTokenFactory = $validationTokenFactory;
	}

	/**
	 * Getting Customer Name By ID From Token Table
	 *
	 * @param  int    $id
	 *
	 * @return string
	 */
	public function getCustomerFirstNameByValidationId(int $id)
	{
		$validationToken = $this->validationTokenFactory->create()->load($id);

		return $this->getOrderById($validationToken->getOrderId())->getCustomerFirstName();
	}

	/**
	 * Getting Order By Id
	 *
	 * @param  int    $id
	 *
	 * @return Magento\Sales\Model\OrderFactory
	 */
	protected function getOrderById(int $id)
	{
		return $this->orderFactory->create()->load($id);
	}
}
