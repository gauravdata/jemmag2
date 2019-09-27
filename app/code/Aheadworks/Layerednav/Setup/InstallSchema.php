<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Setup;

use Aheadworks\Layerednav\Setup\Updater\Schema;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Aheadworks\Layerednav\Setup
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * @var Schema
     */
    private $updater;

    /**
     * @param Schema $updater
     */
    public function __construct(
        Schema $updater
    ) {
        $this->updater = $updater;
    }

    /**
     * @inheritDoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this->updater->update170($setup);
        $this->updater->update200($setup);

        $installer->endSetup();
    }
}
