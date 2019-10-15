<?php

namespace Loyalty\CustomerLoyalty\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ValidationToken extends AbstractDb
{
	protected function _construct()
	{
		$this->_init('customerloyalty_sent_token', 'entity_id');
	}
}
