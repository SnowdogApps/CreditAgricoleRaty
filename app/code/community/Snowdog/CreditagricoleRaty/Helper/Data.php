<?php

class Snowdog_CreditagricoleRaty_Helper_Data extends Mage_Core_Helper_Data {
	const PAYMENT_OK = 0;
	const PAYMENT_LOW_TOTAL = 1;
	const PAYMENT_HIGH_TOTAL = 2;
	const PAYMENT_EXCLUDED_PRODUCTS = 3;
	const PAYMENT_EXCLUDED_SUBPRODUCTS = 4;
	const PARAM_TYPE = 'RAT';
	const POST_ATTR = 1;
	const CREDIT_PERIOD = 12; //creditInfo.creditPeriod
	const PARAM_AUTH = 2; //1=SHA or 2=MD5

	/**
	 * @param $productId
	 * @param $price
	 * @param array $optionIds
	 * @return int
	 */
	public function testBundle($productId, $price, array $optionIds) {
		$product = Mage::getModel('catalog/product')->load($productId);
		if (!Mage::helper('snowcreditagricoleraty')->isProductEligible($product))
			return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_EXCLUDED_PRODUCTS;
		foreach ($optionIds as $optionId) {
			$selection = Mage::getModel('bundle/selection')->load($optionId);
			/* @var $selection Mage_Bundle_Model_Selection */
			$productId = $selection->getProductId();
			$product = Mage::getModel('catalog/product')->load($productId);
			if (!Mage::helper('snowcreditagricoleraty')->isProductEligible($product))
				return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_EXCLUDED_SUBPRODUCTS;
		}
		if ($price < Mage::getStoreConfig('payment/snowcreditagricoleraty/min_order_total'))
			return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_LOW_TOTAL;
		if ($price > Mage::getStoreConfig('payment/snowcreditagricoleraty/max_order_total'))
			return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_HIGH_TOTAL;
	}

	/**
	 * @return bool|int
	 */
	public function testProduct() {
		$product = Mage::registry('current_product');
		if (!$product)
			$product = Mage::registry('product');
		if (!$product)
			return false;
		$grandTotal = $product->getFinalPrice();
		if (!Mage::helper('snowcreditagricoleraty')->isProductEligible($product))
			return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_EXCLUDED_PRODUCTS;
		if ($grandTotal < Mage::getStoreConfig('payment/snowcreditagricoleraty/min_order_total'))
			return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_LOW_TOTAL;
		if ($grandTotal > Mage::getStoreConfig('payment/snowcreditagricoleraty/max_order_total'))
			return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_HIGH_TOTAL;
		return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_OK;
	}

	/**
	 * @return int
	 */
	public function testQuote() {
		$session = Mage::getModel('checkout/session');
		/* @var $session Mage_Checkout_Model_Session */
		$quote = $session->getQuote();
		/* @var $quote Mage_Sales_Model_Quote */
		$totals = $quote->getTotals();
		$grandTotal = $totals['grand_total']->getValue();
		foreach ($quote->getItemsCollection() as $item) {
			$product = Mage::getModel('catalog/product')->load($item->getProductId());
			/* @var $product Mage_Calaog_Model_Product */
			if (!Mage::helper('snowcreditagricoleraty')->isProductEligible($product))
				return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_EXCLUDED_PRODUCTS;
		}
		if ($grandTotal < Mage::getStoreConfig('payment/snowcreditagricoleraty/min_order_total'))
			return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_LOW_TOTAL;
		if ($grandTotal > Mage::getStoreConfig('payment/snowcreditagricoleraty/max_order_total'))
			return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_HIGH_TOTAL;

		return Snowdog_CreditagricoleRaty_Helper_Data::PAYMENT_OK;
	}

	/**
	 * @param Mage_Sales_Model_Order $order
	 * @return string
	 */
	public function buildOrderInformation(Mage_Sales_Model_Order $order) {
		$xml = new XMLWriter();
		$xml->openMemory();
		$xml->setIndent(true);
		$xml->startElement("form");
		$xml->startElement("block");
		$xml->writeAttribute("id", "cart");
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "cart.shopName.value");
		$xml->writeCdata(Mage::getStoreConfig("general/store_information/name"));
		$xml->endElement();
		$i = 0;
		foreach ($order->getAllItems() as $item) {
			/* @var $item Mage_Sales_Model_Order_Item */
			if($item->getParentItemId() && $item->getPriceInclTax() == 0.0) {
				continue;
			}
			++$i;
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemName" . $i . ".value");
			$xml->writeCdata(mb_substr($item->getName(), 0, 40));
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemQty" . $i . ".value");
			$xml->writeRaw(number_format($item->getQtyOrdered(), 0));
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemPrice" . $i . ".value");
			$xml->writeRaw($this->getItemPrice($item));
			$xml->endElement();
		}
		if ($order->getDiscountAmount() != 0.00) {
			++$i;
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemName" . $i . ".value");
			$xml->writeRaw(Mage::helper('snowcreditagricoleraty')->__("Zniżka %s", $order->getDiscountDescription()));
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemQty" . $i . ".value");
			$xml->writeRaw(1);
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemPrice" . $i . ".value");
			$xml->writeRaw(number_format($order->getDiscountAmount() * (-1.0), 2, '.', ''));
			$xml->endElement();
		}
		if ($order->getShippingInclTax() > 0) {
			++$i;
			$shipping = $order->getShippingMethod(true);
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemName" . $i . ".value");
			if ($shipping->getCarrierName()) {
				$xml->writeRaw($shipping->getCarrierName());
			} else {
				$xml->writeRaw("Przesyłka");
			}
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemQty" . $i . ".value");
			$xml->writeRaw(1);
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "cart.itemPrice" . $i . ".value");
			$xml->writeRaw(number_format($order->getShippingInclTax(), 2, '.', ''));
			$xml->endElement();
		}
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "cart.orderNumber.value");
		$xml->writeRaw($order->getIncrementId());
		$xml->endElement();
		$xml->endElement();
		$xml->startElement("block");
		$xml->writeAttribute("id", "creditInfo");
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "creditInfo.creditAmount.value");
		$xml->writeRaw(number_format($order->getGrandTotal(), 2, '.', ''));
		$xml->endElement();
		$xml->endElement();
		$xml->startElement("block");
		$xml->writeAttribute("id", "email");
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "email.address.value");
		$xml->writeRaw($order->getCustomerEmail());
		$xml->endElement();
		$xml->endElement();
		$xml->startElement("block");
		$xml->writeAttribute("id", "agreements");
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "pastCreditDataAgr.agreement.value");
		$xml->writeRaw("true");
		$xml->endElement();
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "marketingAgr.agreement.value");
		$xml->writeRaw("true");
		$xml->endElement();
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "emailAgr.agreement.value");
		$xml->writeRaw("true");
		$xml->endElement();
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "verificationAgr.agreement.value");
		$xml->writeRaw("true");
		$xml->endElement();
		$xml->startElement("element");
		$xml->writeAttribute("importAs", "robinsonLBAgr.agreement.value");
		$xml->writeRaw("true");
		$xml->endElement();
		$xml->endElement();
		if (Mage::app()->getStore()->isCurrentlySecure()) {
			$xml->startElement("block");
			$xml->writeAttribute("id", "personalData");
			$xml->startElement("element");
			$xml->writeAttribute("id", "applicantFirstName");
			$xml->writeAttribute("importAs", "personalData.firstName.value");
			$xml->writeCdata(mb_substr($order->getCustomerFirstname(), 0, 20));
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("id", "applicantLastName");
			$xml->writeAttribute("importAs", "personalData.lastName.value");
			$xml->writeCdata(mb_substr($order->getCustomerLastname(), 0, 40));
			$xml->endElement();
			$billingAddres = $order->getBillingAddress();
			/* @var $billingAddres Mage_Sales_Model_Order_Address */
			$xml->endElement();
			$xml->startElement("block");
			$xml->writeAttribute("id", "permanentAddress");
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "permanentAddress.streetName.value");
			$xml->writeCdata($billingAddres->getStreetFull());
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "permanentAddress.city.value");
			$xml->writeCdata($billingAddres->getCity());
			$xml->endElement();
			$postcode = $billingAddres->getPostcode();
			$postcode = explode('-', $postcode);
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "permanentAddress.postalCodeHead.value");
			$xml->writeRaw($postcode[0]);
			$xml->endElement();
			$xml->startElement("element");
			$xml->writeAttribute("importAs", "permanentAddress.postalCodeTail.value");
			$xml->writeRaw($postcode[1]);
			$xml->endElement();
			$xml->endElement();
		}
		$xml->endElement();
		$string_xml = $xml->outputMemory();
		return $string_xml;
	}

	/**
	 * @param Mage_Sales_Model_Order $order
	 * @return array
	 */
	public function buildSubmitData(Mage_Sales_Model_Order $order) {

		$creditAmount = number_format($order->getGrandTotal(), 2, '.', '');
		$randomizer = $this->getRandomString(15);

		//prepare cart items to submit
		$i = 0;
		foreach ($order->getAllItems() as $item) {
			if($item->getParentItemId() && $item->getPriceInclTax() == 0.0) {
				continue;
			}
			$i++;

			$items["cart.itemName$i"] 	= mb_substr($item->getName(), 0, 40);
			$items["cart.itemQty$i"]	= number_format($item->getQtyOrdered(), 0);
			$items["cart.itemPrice$i"]	= $this->getItemPrice($item);
		}

		//add shipping cost as item
		if ($order->getShippingInclTax() > 0) {
			$i++;
			$items["cart.itemName$i"] 	= 'Przesyłka';
			$items["cart.itemQty$i"]	= 1;
			$items["cart.itemPrice$i"]	= number_format($order->getShippingInclTax(), 2, '.', '');
		}

		//prepare submit data (order and auth)
		$orderData = array (
			'PARAM_TYPE' 	=> Snowdog_CreditagricoleRaty_Helper_Data::PARAM_TYPE,
			'PARAM_PROFILE'	=> Mage::getStoreConfig("payment/snowcreditagricoleraty/psp_id"),
			'POST_ATTR'		=> Snowdog_CreditagricoleRaty_Helper_Data::POST_ATTR,
			'PARAM_CREDIT_AMOUNT'	=> $creditAmount,
			'PARAM_HASH'	=> $this->getParamHash(
				Mage::getStoreConfig("payment/snowcreditagricoleraty/psp_id"),
				$creditAmount,
				$items['cart.itemName1'],
				$items['cart.itemPrice1'],
				$randomizer),
			'PARAM_AUTH'	=> Snowdog_CreditagricoleRaty_Helper_Data::PARAM_AUTH,
			'creditInfo.creditPeriod' => Snowdog_CreditagricoleRaty_Helper_Data::CREDIT_PERIOD,
			'creditInfo.creditAmount' => $creditAmount,
			'cart.shopName'	=> Mage::getStoreConfig("general/store_information/name"),
			'email.address'	=> $order->getCustomerEmail(),
			'cart.orderNumber'	=> $order->getIncrementId(),
			'randomizer'	=> $randomizer,
			'cartItems'	=> $items
		);

		return $orderData;
	}

	/**
	 * @param $paramProfile
	 * @param $creditAmount
	 * @param $itemName1
	 * @param $itemPrice1
	 * @param $randomizer
	 * @return null|string
	 */
	public function getParamHash($paramProfile, $creditAmount, $itemName1, $itemPrice1, $randomizer) {
		if (!$password = Mage::helper('core')->decrypt(Mage::getStoreConfig("payment/snowcreditagricoleraty/auth_password")))
			return null;

		$_hashData = array(
			'PARAM_PROFILE' => $paramProfile,
			'PARAM_TYPE' => Snowdog_CreditagricoleRaty_Helper_Data::PARAM_TYPE,
			'PARAM_AUTH' => Snowdog_CreditagricoleRaty_Helper_Data::PARAM_AUTH,
			'creditInfo.creditAmount' => $creditAmount,
			'cart.itemName1' => $itemName1,
			'cart.itemPrice1' => $itemPrice1,
			'randomizer' => $randomizer,
			'password' =>  Mage::helper('core')->decrypt(Mage::getStoreConfig("payment/snowcreditagricoleraty/auth_password"))
		);

		$_paramHash = implode('', $_hashData);

		if (Snowdog_CreditagricoleRaty_Helper_Data::PARAM_AUTH == 1)
			return sha1($_paramHash);
		else
			return md5($_paramHash);
	}

	public function isProductEligible($product) {

		$minConfigPrice = Mage::getStoreConfig('payment/snowcreditagricoleraty/minimum_product_price');
		$productPrice = Mage::helper('tax')->getPrice($product, $product->getFinalPrice());

		if (Mage::getStoreConfigFlag('payment/snowcreditagricoleraty/all_products_eligible')
			&& $productPrice>=$minConfigPrice)
			return true;

		if ($product->getCreditagricoleratyEligible())
			return true;

		return false;
	}

    /**
     * @param Mage_Sales_Model_Order_Item $item
     * @return string
     */
    public function getItemPrice(Mage_Sales_Model_Order_Item $item): string
    {
        $total = $item->getPriceInclTax() - $item->getDiscountAmount();
        return number_format($total, 2, '.', '');
    }

}