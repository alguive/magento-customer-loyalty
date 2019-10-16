<?php

namespace Loyalty\CustomerLoyalty\Controller\Review;

use Magento\Framework\App\Request\Http;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Loyalty\CustomerLoyalty\Model\ValidationFactory;
use Loyalty\CustomerLoyalty\Model\ValidationTokenFactory;

class Create extends Action
{

	/**
	 * @var Magento\Framework\App\Request\Http
	 */
	protected $request;

	/**
	 * @var Magento\Framework\View\Result\PageFactory
	 */
	protected $pageFactory;

	/**
	 * @var Magento\Framework\Controller\ResultFactory
	 */
	protected $resultFactory;

	/**
	 * @var Magento\Framework\Message\ManagerInterface
	 */
	protected $messageManager;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ValidationFactory
	 */
	protected $validationFactory;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ValidationTokenFactory
	 */
	protected $validationTokenFactory;

	/**
	 * Class Builder
	 *
	 * @param Http                   $request
	 * @param Context                $context
	 * @param PageFactory            $pageFactory
	 * @param ResultFactory          $resultFactory
	 * @param ManagerInterface       $messageManager
	 * @param ValidationFactory      $validationFactory
	 * @param ValidationTokenFactory $validationTokenFactory
	 */
	public function __construct
	(
		Http $request,
		Context $context,
		PageFactory $pageFactory,
		ResultFactory $resultFactory,
		ManagerInterface $messageManager,
		ValidationFactory $validationFactory,
		ValidationTokenFactory $validationTokenFactory
	)
	{
		$this->request = $request;
		$this->pageFactory = $pageFactory;
		$this->resultFactory = $resultFactory;
		$this->messageManager = $messageManager;
		$this->validationFactory = $validationFactory;
		$this->validationTokenFactory = $validationTokenFactory;
		parent::__construct($context);
	}

	public function execute()
	{
		$this->messageManager->addError(__('Página actualmente no disponible.'));

		$redirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$redirect->setPath('reviews/index/index');

		return $redirect;
	}

	/**
	 * Check if token exists in database
	 *
	 * @param  string  $token
	 *
	 * @return boolean
	 */
	private function isTokenReal(string $token)
	{
		$tokenData = $this->validationTokenFactory->create()
						->getCollection()
						->addFieldToFilter('token', ['eq' => $token])
						->getFirstItem();

		return empty($tokenData->getData()) ? null : $tokenData;
	}

	/**
	 * Check if token has been used.
	 *
	 * @param  Loyalty\CustomerLoyalty\Model\ValidationToken  $validationToken
	 *
	 * @return boolean
	 */
	private function isTokenUsed($validationToken = null)
	{
		if (null === $validationToken) {
			return true;
		}

		$valoration = $this->validationFactory->create()
						->getCollection()
						->addFieldToFilter('customerloyalty_sent_entity_id', ['eq' => $validationToken->getEntityId()])
						->getFirstItem();

		return empty($valoration->getData());
	}
}
