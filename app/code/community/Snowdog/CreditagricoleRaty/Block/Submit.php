<?php

/**
 * Submit
 *
 * @author mamut
 */
class Snowdog_CreditagricoleRaty_Block_Submit extends Mage_Core_Block_Template {

	private $_order;

	public function getOrder() {
		return $this->_order;
	}
	
	public function setOrder($order) {
		$this->_order = $order;
		return $this;
	}

	public function getSubmitData() {
		$submitData = Mage::helper('snowcreditagricoleraty')->buildSubmitData($this->_order);
		return $submitData;
	}

	public function getParamSetup() {
		$ratySession = Mage::getModel('snowcreditagricoleraty/session');
		return $ratySession->getParamSetup();
	}

	public function getStoreId() {
		return Mage::getStoreConfig("payment/snowcreditagricoleraty/psp_id");
	}
}