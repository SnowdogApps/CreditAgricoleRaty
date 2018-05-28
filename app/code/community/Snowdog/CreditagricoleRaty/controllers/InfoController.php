<?php

/**
 * ExchangeController
 */
class Snowdog_CreditagricoleRaty_InfoController extends Mage_Core_Controller_Front_Action
{
    public function bundleUpdateAction()
    {
        $request = $this->getRequest();
        $productId = $request->getParam('id');
        $price = $request->getParam('price');
        $optionIds = $request->getParam('optionIds');

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || $product->getTypeId() !== Mage_Catalog_Model_Product_Type::TYPE_BUNDLE) {
            $this->getResponse()
                ->setHttpResponseCode(400)
                ->setBody(
                    Mage::helper('snowcreditagricoleraty')->__('Invalid bundle product')
                );
            return;
        }
        Mage::register('current_product', $product);

        $block = Mage::getBlockSingleton('snowcreditagricoleraty/bundle_product');
        $block->setPrice($price);
        $block->setProductId($productId);
        $block->setOptionIds(array_filter(explode(',', $optionIds)));
        $this->getResponse()->setBody($block->toHtml());
    }
}
