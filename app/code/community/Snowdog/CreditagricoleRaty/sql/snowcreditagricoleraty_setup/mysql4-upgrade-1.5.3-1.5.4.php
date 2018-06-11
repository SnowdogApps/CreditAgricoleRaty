<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$table = $this->getTable('snowcreditagricoleraty/application');
$installer->run("
    ALTER TABLE {$table}
        MODIFY incremental_id VARCHAR(50) NOT NULL;
    ALTER TABLE {$table}
        ADD INDEX
        `{$installer->getIdxName($table, 'incremental_id')}` (`incremental_id`);
");
$installer->endSetup();
