<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Listing\Columns\Filter;

/**
 * Class DefaultTitle
 * @package Aheadworks\ReviewReminder\Ui\Component\Listing\Columns\Filter
 * @codeCoverageIgnore
 */
class DefaultTitle extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Prepare data source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        foreach ($dataSource['data']['items'] as &$item) {
            $item['default_title_url'] = $this->getLink($item['id']);
        }

        return $dataSource;
    }

    /**
     * Get link for name
     *
     * @param int $entityId
     * @return string
     */
    private function getLink($entityId)
    {
        return $this->context->getUrl('aw_layerednav/filter/edit', ['id' => $entityId]);
    }
}
