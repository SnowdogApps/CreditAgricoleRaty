<?php

class Snowdog_CreditagricoleRaty_Model_Image extends Mage_Core_Model_Abstract {

	public $_imageUrl;
	public $_imageDimensions;

	public function _construct() {
		$this->_imageUrl = $this->getImageUrl();
	}

	public function getImageType() {
		$arr = explode('.',$this->_imageUrl);
		return end($arr);
	}

	public function getImageUrl() {
		return Mage::getStoreConfig('payment/snowcreditagricoleraty/image_product');
	}

	public function getImageWidth() {
		return $this->getImageDimensions('width');
	}

	public function getImageHeight() {
		return $this->getImageDimensions('height');
	}

	public function getImageDimensions($dimension) {

		if (empty($this->_imageDimensions)) {
			$matches = array();
			preg_match("/(\d+)x(\d+)/",$this->_imageUrl,$matches);

			if (count($matches)==3) {
				$this->_imageDimensions['width'] = $matches[1];
				$this->_imageDimensions['height'] = $matches[2];
			}
		}

		if (isset($this->_imageDimensions[$dimension]))
			return $this->_imageDimensions[$dimension];

	}

}