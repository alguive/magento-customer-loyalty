<?php

namespace Loyalty\CustomerLoyalty\Model;

use Magento\Framework\Model\AbstractModel;

class ValidationToken extends AbstractModel
{
	protected function _construct()
	{
		$this->_init(\Loyalty\CustomerLoyalty\Model\ResourceModel\ValidationToken::class);
	}
}
