<?php

/**
 * Product
 *
 * @author mamut
 */
class Snowdog_CreditagricoleRaty_Block_Product extends Snowdog_CreditagricoleRaty_Block_Abstract {

	protected $imageModel;

	public function _construct() {
		$this->_imageModel = Mage::getModel('snowcreditagricoleraty/image');
	}

	public function getCreditAmount() {
		$_proId  = Mage::registry('current_product')->getId();
		$_product= Mage::getModel('catalog/product')->load($_proId);
		return number_format(Mage::helper('tax')->getPrice($_product, $_product->getFinalPrice(), true),2,'.','');
	}
	public function getPSP() {
		return Mage::getStoreConfig('payment/snowcreditagricoleraty/psp_id');
	}
	public function getClickUrl() {
		return 'https://ewniosek.credit-agricole.pl/eWniosek/simulator.jsp';
	}
	public function getRateUrl() {
		return 'https://ewniosek.credit-agricole.pl/eWniosek/comm/getInstallment';
	}

	public function getImageUrl() {
		return $this->_imageModel->getImageUrl();
	}

	public function getImageType() {
		return $this->_imageModel->getImageType();
	}

	public function getImageWidth() {
		return $this->_imageModel->getImageWidth();
	}

	public function getImageHeight() {
		return $this->_imageModel->getImageHeight();
	}

	public function _toHtml() {

		$product = Mage::registry('current_product');
		if (Mage::helper('snowcreditagricoleraty')->isProductEligible($product))
			return parent::_toHtml();

		return;
	}

}

