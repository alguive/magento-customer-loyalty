<?php

namespace Loyalty\CustomerLoyalty\Controller\Adminhtml\Validation;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\Action;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
	protected $pageFactory;

	public function __construct(Context $context, PageFactory $pageFactory)
	{
		$this->pageFactory = $pageFactory;
		parent::__construct($context);
	}

	public function execute()
	{
		$resultPage = $this->pageFactory->create();
		$resultPage->getConfig()->getTitle()->prepend((__('Validación')));

		return $resultPage;
	}
}
