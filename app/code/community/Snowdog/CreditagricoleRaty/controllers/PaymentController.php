<?php
/*
 * Copyright (c) 2020.
 * Author: Mateusz Janota
 * Author URI: https://www.dnacloud.pl/
 * Author email: mateusz@dnacloud.pl
 */

/**
 * PaymentController
 */
class Snowdog_CreditagricoleRaty_PaymentController extends Mage_Core_Controller_Front_Action {

  /**
   * failureAction router dispatch method
   * Called when assignment request failed
   */
  public function failureAction() {
    $this->loadLayout();
    $session = Mage::getModel('snowcreditagricoleraty/session');
    $application = Mage::getModel('snowcreditagricoleraty/application')->load($session->getApplicationId());
    /* @var $application Snowdog_CreditagricoleRaty_Model_Application */
    $block = $this->getLayout()->getBlock("creditagricoleraty.negative");
    $block->setOrderId($application->getIncrementalId());
    $order = Mage::getModel('sales/order')->loadByIncrementId($application->getIncrementalId());
    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Wniosek nie został wysłany do banku');
    $order->save();
    $this->renderLayout();
  }

  /**
   * successAction router dispatch method
   * Called when assignment request successed
   * @throws Exception
   */
  public function successAction() {
    $this->loadLayout();
    $session = Mage::getModel('snowcreditagricoleraty/session');
    $application = Mage::getModel('snowcreditagricoleraty/application')->load($session->getApplicationId());
    /* @var $application Snowdog_CreditagricoleRaty_Model_Application */
    $application->setSubmitted(true);
    $application->setLastModified(date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time())));
    $application->save();
    $block = $this->getLayout()->getBlock("creditagricoleraty.success");
    $block->setOrderId($application->getIncrementalId());
    $order = Mage::getModel('sales/order')->loadByIncrementId($application->getIncrementalId());
    $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true, 'Wniosek został wysłany do banku');
    $order->save();
    $this->renderLayout();
  }

  public function submitAction() {
    $session = Mage::getModel('core/session');
    /* @var $session Mage_Core_Model_Session */
    $application = Mage::getModel('snowcreditagricoleraty/application');
    /* @var $application Snowdog_CreditagricoleRaty_Model_Application */
    list($order, $status) = $application->createByOrderId($this->getRequest()->getParam("order"), Mage::getSingleton('checkout/session')->getLastRealOrderId());
    /* @var $order Mage_Sales_Model_Order */
    if ($status == Snowdog_CreditagricoleRaty_Model_Application::STATUS_NEW || $status == Snowdog_CreditagricoleRaty_Model_Application::STATUS_NOTCONFIRMED) {
      $this->loadLayout();
      $block = $this->getLayout()->getBlock("creditagricoleraty.submit");
      $block->setOrder($order);
      if($status == Snowdog_CreditagricoleRaty_Model_Application::STATUS_NEW) {
        $order->sendNewOrderEmail();
        $order->setEmailSent(true);
        $order->save();
      }
      $block->setApplication($application);
      $ratySession = Mage::getModel('snowcreditagricoleraty/session');
      $ratySession->setApplicationId($application->getId());
      $ratySession->setParamSetup($application->getParamSetup());
      $this->renderLayout();
    } else if ($status == Snowdog_CreditagricoleRaty_Model_Application::STATUS_SUBMITED) {
      $session->addSuccess(Mage::helper('snowcreditagricoleraty')->__("Wniosek dla tego zamówienia został już złozony"));
      return $this->_redirect("");
    } else if ($status == Snowdog_CreditagricoleRaty_Model_Application::STATUS_ERROR) {
      if($this->getRequest()->getParam("order")) {
        $params = array("order"=>$this->getRequest()->getParam("order"));
      } else {
        $params = null;
      }
      return $this->_redirect("*/*/error", $params);
    } else {
      $session->addError(Mage::helper('snowcreditagricoleraty')->__("Nie można złożyć wniosku dla tego zamówienia"));
      return $this->_redirect("");
    }
  }
  public function errorAction() {
    $this->loadLayout();
    $this->renderLayout();
  }
}

