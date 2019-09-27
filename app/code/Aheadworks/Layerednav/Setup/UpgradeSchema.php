<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Setup;

use Aheadworks\Layerednav\Setup\Updater\Schema;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class UpgradeSchema
 *
 * @package Aheadworks\Layerednav\Setup
 */
class UpgradeSchema implements UpgradeSchemaInterface
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
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->updater->update170($setup);
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->updater->update200($setup);
        }

        $setup->endSetup();
    }
}
