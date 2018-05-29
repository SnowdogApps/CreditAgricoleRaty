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
        $selection = $request->getParam('optionIds');

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

        $selectionQtys = array();
        $selection = array_filter(explode(',', $selection));
        foreach ($selection as $item) {
            if (substr_count($item, '-') !== 1) {
                continue;
            }
            list($selectionId, $qty) = explode('-', $item, 2);
            $selectionQtys[(int) $selectionId] = (int) $qty;
        }
        $block->setSelectionQtys($selectionQtys);

        $this->getResponse()->setBody($block->toHtml());
    }
}
