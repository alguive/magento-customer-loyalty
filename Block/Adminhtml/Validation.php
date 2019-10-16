<?php

namespace Loyalty\CustomerLoyalty\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Validation extends Container
{
	protected function _construct()
	{
		$this->_controller = 'adminhtml_validation';
		$this->_blockGroup = 'Loyalty_CustomerLoyalty';
		$this->_headerText = __('Validación');
		parent::_construct();
	}
}
