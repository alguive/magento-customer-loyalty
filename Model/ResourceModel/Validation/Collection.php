<?php

namespace Loyalty\CustomerLoyalty\Model\ResourceModel\Validation;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
	protected function _construct()
	{
		$this->_init(\Loyalty\CustomerLoyalty\Model\Validation::class, \Loyalty\CustomerLoyalty\Model\ResourceModel\Validation::class);
	}
}
