<?php

/**
 * RedirectController
 */
class Snowdog_CreditagricoleRaty_RedirectController extends Mage_Core_Controller_Front_Action {
	protected $_storeId;
	
	public function _construct() {
		$this->_storeId = Mage::getStoreConfig("payment/snowcreditagricoleraty/psp_id");
	}
	
	public function infoAction() {
		return $this->_redirectUrl("https://ewniosek.credit-agricole.pl/eWniosek/procedure.jsp?PARAM_TYPE=RAT&PARAM_PROFILE=".$this->_storeId);
	}
	
	public function calculateAction() {
		$amount = (double)$this->getRequest()->getParam("amount");
		return $this->_redirectUrl("https://ewniosek.credit-agricole.pl/eWniosek/simulator.jsp?PARAM_TYPE=RAT&PARAM_PROFILE=".$this->_storeId."&PARAM_CREDIT_AMOUNT=".number_format($amount,2,'.',''));
	}
	
	public function submitAction() {
		$session = Mage::getModel('core/session');
		/* @var $session Mage_Core_Model_Session */
		$id = $this->getRequest()->getParam("application");
		$application = Mage::getModel('snowcreditagricoleraty/application')->load($id);
		/* @var $application Snowdog_CreditagricoleRaty_Model_Application */
		if($application->isObjectNew()) {
			$session->addError(Mage::helper('snowcreditagricoleraty')->__("Niepoprawny numer wniosku ratalnego."));
			return $this->_redirectUrl($this->getRequest()->getHeader("Referer"));
		} 
		$customer = Mage::getModel('customer/session');
		/* @var $customer Mage_Customer_Model_Session */
		$order = Mage::getModel('sales/order');
		/* @var $order Mage_Sales_Model_Order */
		$order->loadByIncrementId($application->getIncrementalId());
		if ($customer->getCustomerId() == $order->getCustomerId()) {
			if ($order->getCustomerIsGuest()) {
				if ($order->getIncrementId() != Mage::getSingleton('checkout/session')->getLastRealOrderId()) {
					$session->addError(Mage::helper('snowcreditagricoleraty')->__("Niepoprawny numer wniosku ratalnego."));
					return $this->_redirectUrl($this->getRequest()->getHeader("Referer"));
				}
			}
			Mage::getModel('snowcreditagricoleraty/session')->setApplicationId($id);
			return $this->_redirectUrl("https://ewniosek.credit-agricole.pl/eWniosek/simulator.jsp?PARAM_TYPE=RAT&PARAM_PROFILE=".$this->_storeId."&PARAM_SETUP=".$application->getParamSetup());
		}
		$session->addError(Mage::helper('snowcreditagricoleraty')->__("Niepoprawny numer wniosku ratalnego."));
		return $this->_redirectUrl($this->getRequest()->getHeader("Referer"));
	}
}

