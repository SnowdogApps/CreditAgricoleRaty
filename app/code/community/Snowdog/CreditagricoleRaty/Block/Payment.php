<?php

/**
 * Payment
 *
 * @author mamut
 */
class Snowdog_CreditagricoleRaty_Block_Payment extends Mage_Payment_Block_Form {
	protected function _construct() {
		parent::_construct();
		$session = Mage::getSingleton('checkout/session');
		/* @var $session Mage_Checkout_Model_Session */
	}
	
}

