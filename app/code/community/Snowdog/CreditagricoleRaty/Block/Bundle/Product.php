<?php

/**
 * Product
 */
class Snowdog_CreditagricoleRaty_Block_Bundle_Product extends Snowdog_CreditagricoleRaty_Block_Abstract {

	protected $_template = 'snowcreditagricoleraty/bundle/product.phtml';

	public function getImageUrl() {
		return $this->getImage('image_product');
	}

	function _beforeToHtml() {
		$productId = $this->getProductId();
		if (!$productId) {
			$product = Mage::registry('current_product');
			if (!$product)
				$product = Mage::registry('product');
			$productId = $product->getId();
		}
		$price = false;
		if ($this->getPrice()===null) {
			$priceModel = $product->getPriceModel();
			list($_minimalPriceInclTax, $_maximalPriceInclTax) = $priceModel->getTotalPrices($product);
			$price = $_minimalPriceInclTax;
		} else {
			$price = $this->getPrice();
		}
		$this->setPrice(number_format($price, 2, '.', ''));
		$optionIds = $this->getOptionIds();
		if(!$optionIds) {
			$optionIds = array();
		}
		$this->setMessage(Mage::helper('snowcreditagricoleraty')->testBundle($productId, $price, $optionIds));
		parent::_beforeToHtml();
	}

}

