<?php

class Snowdog_CreditagricoleRaty_Helper_Log
    extends Mage_Core_Helper_Abstract
{

    /**
     * @param $message
     * @param null $header
     */
    public function log($message, $header = null) {
        if (Mage::getStoreConfigFlag('payment/snowcreditagricoleraty/save_logs') === false)
            return;

        if ($header)
            $header = "------". $header ."------";
        Mage::log($header, Zend_log::DEBUG,'credit-agricole.log',true);
        Mage::log($message, Zend_log::DEBUG,'credit-agricole.log',true);
    }

}