<?php
/**
 * ServiceController
 *
 * @author maciek
 */

class Snowdog_CreditagricoleRaty_ServiceController extends Mage_Core_Controller_Front_Action {

  public function calculateInstallmentAction() {
    $this->loadLayout();
    try {
      $result = Mage::getModel('snowcreditagricoleraty/service')->calculateInstallment($this->getRequest()->getParam('creditAmount'));
      $this->getLayout()->getBlock('root')->setInstallment($result);
    } catch (Snowdog_CreditagricoleRaty_Exception $e) {
      $this->getLayout()->getBlock('root')->setError($e->getMessage());
    } catch (Exception $e) {
      $this->getLayout()->getBlock('root')->setError($e->getMessage());
    }
    $this->renderLayout();
  }

} // end class
