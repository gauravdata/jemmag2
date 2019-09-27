<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Search\Search;

use Aheadworks\Layerednav\Model\Search\Search\RequestBuilder\Converter;
use Magento\Framework\Phrase;
use Magento\Framework\Search\Request\Binder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Search\Request\Cleaner;
use Magento\Framework\Search\Request\Config;
use Magento\Framework\Search\Request\NonExistingRequestNameException;
use Magento\Framework\Search\RequestInterface;

/**
 * Class RequestBuilder
 * @package Aheadworks\Layerednav\Model\Search\Search
 */
class RequestBuilder
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var Binder
     */
    private $binder;

    /**
     * @var Cleaner
     */
    private $cleaner;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @var array
     */
    private $data = [
        'dimensions' => [],
        'placeholder' => [],
    ];

    /**
     * @var string[]
     */
    private $allowedAggregations = [];

    /**
     * @param Config $config
     * @param Binder $binder
     * @param Cleaner $cleaner
     * @param Converter $converter
     */
    public function __construct(
        Config $config,
        Binder $binder,
        Cleaner $cleaner,
        Converter $converter
    ) {
        $this->config = $config;
        $this->binder = $binder;
        $this->cleaner = $cleaner;
        $this->converter = $converter;
    }

    /**
     * Set request name
     *
     * @param string $requestName
     * @return $this
     */
    public function setRequestName($requestName)
    {
        $this->data['requestName'] = $requestName;
        return $this;
    }

    /**
     * Set size
     *
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        $this->data['size'] = $size;
        return $this;
    }

    /**
     * Set from
     *
     * @param int $from
     * @return $this
     */
    public function setFrom($from)
    {
        $this->data['from'] = $from;
        return $this;
    }

    /**
     * Set sort.
     *
     * @param array $sort
     * @return $this
     */
    public function setSort($sort)
    {
        $this->data['sort'] = $sort;
        return $this;
    }

    /**
     * Bind dimension data by name
     *
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function bindDimension($name, $value)
    {
        $this->data['dimensions'][$name] = $value;
        return $this;
    }

    /**
     * Set allowed aggregations list
     *
     * @param string[] $allowedAggregations
     * @return $this
     */
    public function setAllowedAggregations($allowedAggregations)
    {
        $this->allowedAggregations = $allowedAggregations;
        return $this;
    }

    /**
     * Bind data to placeholder
     *
     * @param string $placeholder
     * @param mixed $value
     * @return $this
     */
    public function bind($placeholder, $value)
    {
        $this->data['placeholder']['$' . $placeholder . '$'] = $value;
        return $this;
    }

    /**
     * Create request object
     *
     * @return RequestInterface
     * @throws \Magento\Framework\Exception\StateException
     */
    public function create()
    {
        if (!isset($this->data['requestName'])) {
            throw new \InvalidArgumentException("Request name not defined.");
        }
        $requestName = $this->data['requestName'];
        /** @var array $data */
        $data = $this->config->get($requestName);
        if ($data === null) {
            throw new NonExistingRequestNameException(
                new Phrase("Request name '%1' doesn't exist.", [$requestName])
            );
        }
        if (!empty($this->allowedAggregations)) {
            foreach ($data['aggregations'] as $name => $value) {
                if (!in_array($name, $this->allowedAggregations)) {
                    unset($data['aggregations'][$name]);
                }
            }
        }

        $data = $this->binder->bind($data, $this->data);

        if (isset($this->data['sort'])) {
            $data['sort'] = $this->prepareSorts($this->data['sort']);
        }

        $data = $this->cleaner->clean($data);

        $this->clear();

        return $this->converter->convert($data);
    }

    /**
     * Prepare sort data for request.
     *
     * @param array $sorts
     * @return array
     */
    private function prepareSorts(array $sorts)
    {
        $sortData = [];
        foreach ($sorts as $sortField => $direction) {
            $sortData[] = [
                'field' => $sortField,
                'direction' => $direction,
            ];
        }

        return $sortData;
    }

    /**
     * Clear data
     *
     * @return void
     */
    private function clear()
    {
        $this->data = [
            'dimensions' => [],
            'placeholder' => []
        ];
        $this->allowedAggregations = [];
    }

    /**
     * Apply field filter to filter
     *
     * @param string $field
     * @param string|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if (!is_array($condition) || !in_array(key($condition), ['from', 'to'], true)) {
            $this->bind($field, $condition);
        } else {
            if (!empty($condition['from'])) {
                $this->bind("{$field}.from", $condition['from']);
            }
            if (!empty($condition['to'])) {
                $this->bind("{$field}.to", $condition['to']);
            }
        }

        return $this;
    }
}
