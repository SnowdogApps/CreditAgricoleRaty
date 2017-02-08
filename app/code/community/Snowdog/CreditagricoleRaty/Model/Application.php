<?php

/**
 * Class Snowdog_CreditagricoleRaty_Model_Application
 */
class Snowdog_CreditagricoleRaty_Model_Application extends Mage_Core_Model_Abstract {
	const STATUS_NEW = 1;
	const STATUS_NOTCONFIRMED = 2;
	const STATUS_SUBMITED = 3;
	const STATUS_INVALID = 4;
	const STATUS_ERROR = 5;

	protected function _construct() {
		$this->_init("snowcreditagricoleraty/application");
		parent::_construct();
	}

	/**
	 * @param $paramId
	 * @param $sessionId
	 * @return array
	 */
	public function createByOrderId($paramId, $sessionId) {
		$collection = $this->getCollection();
		/* @var $collection Snowdog_CreditagricoleRaty_Model_Resource_Application_Collection */
		$order = Mage::getModel('sales/order');
		/* @var $order Mage_Sales_Model_Order */
		$order->loadByIncrementId($paramId);
		if ($order->isObjectNew()) {
			$order->loadByIncrementId($sessionId);
		}
		if ($order->isObjectNew()) {
			return array(null, self::STATUS_INVALID);
		}
		$customer = Mage::getModel('customer/session');
		/* @var $customer Mage_Customer_Model_Session */
		if ($customer->getCustomerId() == $order->getCustomerId()) {
			if ($order->getCustomerIsGuest()) {
				if ($order->getIncrementId() != $sessionId) {
					return array(null, self::STATUS_INVALID);
				}
			}
			$collection->addFieldToFilter("incremental_id", $order->getIncrementId());
			$app = $collection->getFirstItem();
			$this->load($app->getId());
			if (!$this->isObjectNew() && $this->getSubmitted()) {
				return array($order, self::STATUS_SUBMITED);
			} else if (!$this->isObjectNew() && !$this->getSubmitted()) {
				return array($order, self::STATUS_NOTCONFIRMED);
			} else {
				$xml = Mage::helper('snowcreditagricoleraty')->buildOrderInformation($order);
				$soap = new SoapClient(null, array(
							'location' => 'https://ewniosek.credit-agricole.pl/eWniosek/services/FormSetup',
							'uri' => 'https://ewniosek.credit-agricole.pl/eWniosek/services/FormSetup',
							'soap_version' => SOAP_1_1,
							'encoding' => 'UTF-8',
							'style' => 'rpc',
							'exceptions' => true,
						));
				try {
					$paramSetup = $soap->put($xml);
					Mage::helper('snowcreditagricoleraty/log')->log($xml, 'Request data for paramSetup: '. $paramSetup);
					if ($paramSetup) {
						$this->setParamSetup($paramSetup);
						$this->setIncrementalId($order->getIncrementId());
						$this->setLastModified(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
						$this->save();
						return array($order, self::STATUS_NEW);
					} else {
						return array(null, self::STATUS_ERROR);
					}
				} catch (SoapFault $e) {
					return array(null, self::STATUS_ERROR);
				}
			}
		} else {
			return array(null, self::STATUS_INVALID);
		}
	}

}

