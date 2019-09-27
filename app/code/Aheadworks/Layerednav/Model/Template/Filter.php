<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Template;

use Magento\Framework\Filter\Template as TemplateFilter;

/**
 * Class Filter
 * @package Aheadworks\Layerednav\Model\Template
 */
class Filter extends TemplateFilter
{
    /**
     * Construction 'for' pattern
     */
    const CONSTRUCTION_FOR_PATTERN = '/{{for\s*(.*?)\s*in\s*(.*?)}}(.*?){{\\/for\s*}}/si';

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        try {
            foreach ([self::CONSTRUCTION_FOR_PATTERN => 'forDirective'] as $pattern => $directive) {
                if (preg_match_all($pattern, $value, $constructions, PREG_SET_ORDER)) {
                    foreach ($constructions as $construction) {
                        $callback = [$this, $directive];
                        if (is_callable($callback)) {
                            try {
                                $replacedValue = call_user_func($callback, $construction);
                            } catch (\Exception $e) {
                                throw $e;
                            }
                            $value = str_replace($construction[0], $replacedValue, $value);
                        }
                    }
                }
            }
            return parent::filter($value);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * Cycle 'for' directive
     *
     * @param string[] $construction
     * @return string
     */
    public function forDirective($construction)
    {
        if (!is_array($this->templateVars) || !count($this->templateVars)) {
            return $construction[0];
        }
        $arrayExpression = $this->getVariable($construction[2], '');
        if (is_array($arrayExpression)) {
            $replacedValue = '';
            foreach ($arrayExpression as $arrayValue) {
                $this->setVariables([$construction[1] => $arrayValue]);
                $replacedValue .= $this->filter($construction[3]);
            }
            $replacedValue = trim($replacedValue);
            $replacedValue = trim($replacedValue, ',');
            return $replacedValue;
        }
        return '';
    }
}
