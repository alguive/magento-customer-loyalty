<?php

namespace Loyalty\CustomerLoyalty\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Validation extends AbstractDb
{
	protected function _construct()
	{
		$this->_init('customer_loyalty_ratings', 'valoration_id');
	}
}
