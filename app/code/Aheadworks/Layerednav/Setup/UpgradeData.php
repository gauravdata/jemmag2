<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Setup;

use Aheadworks\Layerednav\Model\FilterManagement;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Aheadworks\Layerednav\App\Request\AttributeList;

/**
 * Class UpgradeData
 *
 * @package Aheadworks\Layerednav\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var FilterManagement
     */
    private $filterManagement;

    /**
     * @var AttributeList
     */
    private $attributeList;

    /**
     * @param FilterManagement $filterManagement
     * @param AttributeList $attributeList
     */
    public function __construct(
        FilterManagement $filterManagement,
        AttributeList $attributeList
    ) {
        $this->filterManagement = $filterManagement;
        $this->attributeList = $attributeList;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.7.0', '<')) {
            $this->synchronizeFilters();
        }

        if (version_compare($context->getVersion(), '2.0.0', '<')) {
            $this->synchronizeFilters();
            $this->flushFrontendCache();
        }

        $setup->endSetup();
    }

    /**
     * Synchronize filters
     */
    private function synchronizeFilters()
    {
        $this->filterManagement->synchronizeCustomFilters();
        $this->filterManagement->synchronizeAttributeFilters();
    }

    /**
     * Flush cache after modifications in the logic of data saving
     */
    private function flushFrontendCache()
    {
        $this->attributeList->flushAttributesCache();
    }
}
