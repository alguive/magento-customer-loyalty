<?php

namespace Loyalty\CustomerLoyalty\Model\ResourceModel\ValidationToken;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
	protected function _construct()
	{
		$this->_init(\Loyalty\CustomerLoyalty\Model\ValidationToken::class, \Loyalty\CustomerLoyalty\Model\ResourceModel\ValidationToken::class);
	}
}
