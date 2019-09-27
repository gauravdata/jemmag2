<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\MassAction;

use Zend\Stdlib\JsonSerializable;
use Magento\Framework\UrlInterface;
use Magento\Config\Model\Config\Source\Yesno as YesnoSource;

/**
 * Class ChangeStatusInSearchOptions
 * @package Aheadworks\Layerednav\Ui\Component\MassAction
 * @codeCoverageIgnore
 */
class ChangeStatusInSearchOptions implements JsonSerializable
{
    /**
     * @var array
     */
    private $options;

    /**
     * Additional options params
     *
     * @var array
     */
    private $data;

    /**
     * Additional params for subactions
     *
     * @var array
     */
    private $additionalData = [];

    /**
     * Base URL for subactions
     *
     * @var string
     */
    private $urlPath;

    /**
     * Param name for subactions
     *
     * @var string
     */
    private $paramName;

    /**
     * @var YesnoSource
     */
    private $yesnoSource;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param YesnoSource $yesnoSource
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        YesnoSource $yesnoSource,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->yesnoSource = $yesnoSource;
        $this->urlBuilder = $urlBuilder;
        $this->data = $data;
        $this->prepareData();
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize()
    {
        if ($this->options === null) {
            $options = $this->yesnoSource->toOptionArray();

            foreach ($options as $option) {
                $this->options[$option['value']] = [
                    'type' => $option['value'],
                    'label' => $option['label'],
                ];

                if ($this->urlPath && $this->paramName) {
                    $this->options[$option['value']]['url'] = $this->urlBuilder->getUrl(
                        $this->urlPath,
                        [$this->paramName => $option['value']]
                    );
                }

                $this->options[$option['value']] = array_merge_recursive(
                    $this->options[$option['value']],
                    $this->additionalData
                );
            }

            $this->options = array_values($this->options);
        }

        return $this->options;
    }

    /**
     * Prepare addition data for subactions
     *
     * @return void
     */
    private function prepareData()
    {
        foreach ($this->data as $key => $value) {
            switch ($key) {
                case 'urlPath':
                    $this->urlPath = $value;
                    break;
                case 'paramName':
                    $this->paramName = $value;
                    break;
                default:
                    $this->additionalData[$key] = $value;
                    break;
            }
        }
    }
}
