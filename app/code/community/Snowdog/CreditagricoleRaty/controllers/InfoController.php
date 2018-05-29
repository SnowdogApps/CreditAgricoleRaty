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
        $selections = $request->getParam('optionIds');

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
        $selections = array_filter(explode(',', $selections));
        foreach ($selections as $selection) {
            if (substr_count($selection, '-') !== 1) {
                continue;
            }
            list($selectionId, $qty) = explode('-', $selection, 2);
            $selectionQtys[(int) $selectionId] = (int) $qty;
        }
        $block->setSelectionQtys($selectionQtys);

        $this->getResponse()->setBody($block->toHtml());
    }
}
