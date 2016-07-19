<?php

class Snowdog_CreditagricoleRaty_Block_Abstract extends Mage_Core_Block_Template {

	public function getMinTotal() {
		return Mage::getStoreConfig('payment/snowcreditagricoleraty/min_order_total');
	}
	
	public function getMaxTotal() {
		return Mage::getStoreConfig('payment/snowcreditagricoleraty/max_order_total');
	}
	
	public function renderView() {
		if(Mage::getStoreConfig("payment/snowcreditagricoleraty/active") && Mage::getStoreConfig("payment/snowcreditagricoleraty/psp_id")) {
			return parent::renderView();
		} else {
			return '';
		}
	}

	public function getImage($image) {
		$imageUrl = Mage::getStoreConfig('payment/snowcreditagricoleraty/'.$image);
		return $imageUrl;
	}
	
	
	
}

