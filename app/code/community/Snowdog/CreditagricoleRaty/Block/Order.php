<?php

/**
 * Order
 */
class Snowdog_CreditagricoleRaty_Block_Order extends Mage_Payment_Block_Info {

	public function _toHtml() {
		$msg = Mage::helper('snowcreditagricoleraty')->__("Płatnośc ratalna Credit Agricole Raty.");
		if ($this->getInfo()->getOrder()) {
			$info = $this->getInfo();
			$order = $this->getInfo()->getOrder();
			/* @var $order Mage_Sales_Model_Order */
			$id = $order->getIncrementId();
			if (!$order->getCustomerIsGuest()) {
				$msg .= "\n\r<br />";
				$msg .= "<a href='" . $this->getUrl('snowcreditagricoleraty/payment/submit', array('order' => $id)) . "'>";
				$msg .= Mage::helper('snowcreditagricoleraty')->__("Złóż wniosek ratalny.");
				$msg .= "</a>";
			}
		}
		return $msg;
	}

}

