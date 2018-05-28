<?php

/**
 * Product
 */
class Snowdog_CreditagricoleRaty_Block_Bundle_Product extends Snowdog_CreditagricoleRaty_Block_Abstract
{
    protected $_template = 'snowcreditagricoleraty/bundle/product.phtml';

    public function getImageUrl()
    {
        return $this->getImage('image_product');
    }

    protected function _beforeToHtml()
    {
        $product = Mage::registry('current_product');
        $selectionIds = $this->getOptionIds();
        if (!is_array($selectionIds)) {
            $selectionIds = array();
        }
        $finalPrice = $this->_getBundleFinalPrice($product, $selectionIds);

        $this->setPrice(number_format($finalPrice, 2, '.', ''));
        $this->setMessage(
            Mage::helper('snowcreditagricoleraty')->testBundle(
                $product->getId(),
                $finalPrice,
                $selectionIds
            )
        );

        parent::_beforeToHtml();
    }

    /**
     * @param Mage_Catalog_Model_Product $product
     * @param array $selectionIds
     * @param bool $includeTax
     * @return float
     */
    protected function _getBundleFinalPrice(
        Mage_Catalog_Model_Product $product,
        array $selectionIds,
        $includeTax = true
    ) {
        /** @var Mage_Bundle_Model_Product_Price $priceModel */
        $priceModel = $product->getPriceModel();

        if ($selectionIds) {
            /** @var Mage_Tax_Helper_Data $taxHelper */
            $taxHelper = Mage::helper('tax');
            /** @var Mage_Bundle_Model_Product_Type $typeInstance */
            $typeInstance = $product->getTypeInstance();
            $isPriceFixedType = $product->getPriceType() == Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED;
            $selections = $typeInstance->getSelectionsByIds($selectionIds);
            $finalPrice = 0;
            /** @var Mage_Catalog_Model_Product $selection */
            foreach ($selections as $selection) {
                if (!$selection->isSalable()) {
                    continue;
                }
                $item = $isPriceFixedType ? $product : $selection;
                $selectionPrice = $priceModel->getSelectionFinalTotalPrice(
                    $product,
                    $selection,
                    1,
                    null
                );
                $finalPrice += $taxHelper->getPrice($item, $selectionPrice, $includeTax);
            }
        } else {
            $finalPrice = $priceModel->getTotalPrices($product, 'min', $includeTax);
        }

        return $finalPrice;
    }
}
