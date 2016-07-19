<?php

/**
 * Session
 * 
 * @method int getApplicationId()
 * @method Snowdog_CreditagricoleRaty_Model_Session setApplicationId(int $id)
 *
 * @author mamut
 */
class Snowdog_CreditagricoleRaty_Model_Session extends Mage_Core_Model_Session_Abstract {
	protected function _construct() {
		parent::_construct();
		$this->init('snowcreditagricoleraty');
	}
}

