<?php

namespace Loyalty\CustomerLoyalty\Block;

use Magento\Framework\View\Element\Template;
use Loyalty\CustomerLoyalty\Model\ValidationFactory;
use Magento\Framework\View\Element\Template\Context;
use Loyalty\CustomerLoyalty\Model\ResourceModel\Validation\CollectionFactory as ValidationCollection;

class Index extends Template
{

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ValidationFactory
	 */
	protected $validationFactory;

	/**
	 * @var Loyalty\CustomerLoyalty\Model\ResourceModel\Validation\Collection
	 */
	protected $validationCollection;

	public function __construct(Context $context, ValidationCollection $validationCollection, ValidationFactory $validationFactory)
	{
		$this->validationFactory = $validationFactory;
		$this->validationCollection = $validationCollection;
		parent::__construct($context);
	}

	/**
	 * Getting all reviews
	 *
	 * @return Loyalty\CustomerLoyalty\Model\ResourceModel\Validation\CollectionFactory
	 */
	public function getReviewsCollection()
	{
		return $this->validationCollection->create()
						->addFieldToFilter('approved', ['true' => true]);
	}

	/**
	 * Building string stars to show.
	 *
	 * @param  int|integer $rating
	 *
	 * @return string
	 */
	public function getStarsToString(int $rating = 5)
	{
		return sprintf('%s%s', $this->getStarsFilled($rating), $this->getStarsEmpty($rating));
	}

	/**
	 * Generating string stars filled.
	 *
	 * @param  int|integer $rating
	 *
	 * @return string
	 */
	public function getStarsFilled(int $rating = 0)
	{
		$stars = '';

		for ($i = 0; $i < $rating; $i++) {
			$stars .= '<i class="fas fa-star"></i>';
		}

		return $stars;
	}

	/**
	 * Generating string stars not filled.
	 *
	 * @param  int|integer $rating
	 *
	 * @return string
	 */
	public function getStarsEmpty(int $rating = 5)
	{
		$stars = '';

		for ($i = 0; $i < (5 - $rating); $i++) {
			$stars .= '<i class="far fa-star"></i>';
		}

		return $stars;
	}

}
