<?php

/**
 * Hint
 *
 * @author mamut
 */
class Snowdog_CreditagricoleRaty_Block_Hint extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface {

	protected $_template = 'snowcreditagricoleraty/hint.phtml';

	public function render(Varien_Data_Form_Element_Abstract $element) {
		return $this->toHtml();
	}

	protected function _getUrlModelClass() {
		return 'core/url';
	}
	
	public function getUrl($route = '', $params = array()) {
		$res = parent::getUrl($route, $params);
		return str_replace("index.php/", "", $res);
	}

	public function getImageURL() {
		$imageUrl = 'https://ewniosek.credit-agricole.pl/eWniosek/res/CA_grafika/raty_140x51_duckblue.png';
		return $imageUrl;
	}

}

