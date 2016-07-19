<?php

/**
 * ExchangeController
 *
 * @author mamut
 */
class Snowdog_CreditagricoleRaty_InfoController extends Mage_Core_Controller_Front_Action {
	public function bundleUpdateAction() {
		$block = Mage::getBlockSingleton('snowcreditagricoleraty/bundle_product');
		$id = $this->getRequest()->getParam('id');
		$price = $this->getRequest()->getParam('price', 0.00);
		$optionIds = explode(",", $this->getRequest()->getParam('optionIds'));
		$block->setPrice($price);
		$block->setProductId($id);
		$block->setOptionIds($optionIds);
		echo $block->toHtml();
	}
}

