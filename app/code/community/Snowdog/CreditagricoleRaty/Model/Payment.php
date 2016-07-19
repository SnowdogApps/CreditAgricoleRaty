<?php

/**
 * Payment
 *
 * @author mamut
 */
class Snowdog_CreditagricoleRaty_Model_Payment extends Mage_Payment_Model_Method_Abstract {

	protected $_code = 'snowcreditagricoleraty';
	protected $_formBlockType = 'snowcreditagricoleraty/payment';
	protected $_isGateway = false;
	protected $_canAuthorize = false;
	protected $_canCapture = true;
	protected $_canCapturePartial = false;
	protected $_canRefund = false;
	protected $_canVoid = false;
	protected $_canUseInternal = true;
	protected $_canUseCheckout = true;
	protected $_canUseForMultishipping = false;
	protected $_canSaveCc = false;

	public function getOrderPlaceRedirectUrl() {

        return Mage::getUrl('snowcreditagricoleraty/payment/submit');

	}
	
	public function canUseCheckout() {
		if(!Mage::getStoreConfig("payment/snowcreditagricoleraty/psp_id"))
			return false;	
		$helper = Mage::helper('snowcreditagricoleraty');
		/* @var $helper Snowdog_CreditagricoleRaty_Helper_Data */
		if ($helper->testQuote() == Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_OK)
			return true;
		return false;
	}
	
	protected $_infoBlockType = 'snowcreditagricoleraty/order';

}

