<?php

/**
 * BundleWrapper
 */
class Snowdog_CreditagricoleRaty_Block_Bundle_Wrapper extends Snowdog_CreditagricoleRaty_Block_Abstract
{
    protected $_template = 'snowcreditagricoleraty/bundle/wrapper.phtml';

    public function getImageUrl()
    {
        return $this->getImage('image_product');
    }

    public function getJsonConfig()
    {
        $config = array(
            'url' => $this->getUrl('snowcreditagricoleraty/info/bundleUpdate')
        );
        return json_encode($config);
    }
}

