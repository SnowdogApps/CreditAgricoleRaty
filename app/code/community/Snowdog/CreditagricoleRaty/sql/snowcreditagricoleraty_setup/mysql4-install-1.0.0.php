<?php

$installer = $this;

$installer->startSetup();

$installer->run("CREATE TABLE {$this->getTable('snowcreditagricoleraty/application')} (
    `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `incremental_id` INT NOT NULL,
    `param_setup` VARCHAR(40) NOT NULL,
    `submitted` TINYINT(1) NOT NULL,
    `status_c_a` VARCHAR(5) NULL,
    `status_c_a_info` VARCHAR(255) NULL,
    `appl_number_c_a` VARCHAR(16) NULL,
    `appl_number_ext` VARCHAR(255) NULL,
    `last_modified` TIMESTAMP
)");

$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$setup->addAttribute('catalog_product', 'creditagricoleraty_eligible', array(
    'label' => 'Podlega sprzedaży ratalnej Credit Agricole Raty',
    'type' => 'varchar',
    'input' => 'select',
    'source' => 'eav/entity_attribute_source_boolean',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
    'visible' => true,
    'required' => true,
    'user_defined' => true,
    'searchable' => false,
    'filterable' => true,
    'comparable' => false,
    'default' => false,
    'visible_on_front' => false,
    'visible_in_advanced_search' => false,
    'unique' => false
));

$sets = $setup->getAllAttributeSetIds('catalog_product');
foreach($sets as $setId) {
    $set = $setup->getAttributeSet('catalog_product', $setId);
    $setup->addAttributeToSet('catalog_product', $setId, $setup->getDefaultAttributeGroupId('catalog_product', $setId), 'creditagricoleraty_eligible');
}


$installer->endSetup();
