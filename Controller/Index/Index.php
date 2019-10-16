<?php

namespace Loyalty\CustomerLoyalty\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{

	/**
	 * @var Magento\Framework\View\Result\PageFactory
	 */
	protected $pageFactory;

	public function __construct(Context $context, PageFactory $pageFactory)
	{
		$this->pageFactory = $pageFactory;
		parent::__construct($context);
	}

	public function execute()
	{
		return $this->pageFactory->create();
	}
}
