<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Config\Source\FilterMode;

use Magento\Config\Model\Config\CommentInterface;
use Magento\Framework\UrlInterface;

/**
 * Class Comment
 * @package Aheadworks\Layerednav\Model\Config\Source\FilterMode
 */
class Comment implements CommentInterface
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Retrieve element comment by element value
     * @param string $elementValue
     * @return string
     */
    public function getCommentText($elementValue)
    {
        return (string)__(
            'Applies to the attributes of Dropdown and Multiple Select types. ' .
            'Can be overridden on a <a href="%1">filter level</a>.',
            $this->urlBuilder->getUrl('aw_layerednav/filter/index')
        );
    }
}
