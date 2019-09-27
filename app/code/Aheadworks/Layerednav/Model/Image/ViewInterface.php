<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Model\Image;

use Aheadworks\Layerednav\Api\Data\FileInterface;

/**
 * Interface ViewInterface
 *
 * @package Aheadworks\Layerednav\Model\Image
 */
interface ViewInterface extends FileInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const URL       = 'url';
    const TYPE      = 'type';
    const SIZE      = 'size';
    const TITLE     = 'title';
    /**#@-*/

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl();

    /**
     * Set url
     *
     * @param string $url
     * @return $this
     */
    public function setUrl($url);

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Set type
     *
     * @param string $type
     * @return $this
     */
    public function setType($type);

    /**
     * Get size
     *
     * @return int
     */
    public function getSize();

    /**
     * Set size
     *
     * @param int $size
     * @return $this
     */
    public function setSize($size);

    /**
     * Get title
     *
     * @return int
     */
    public function getTitle();

    /**
     * Set title
     *
     * @param int $title
     * @return $this
     */
    public function setTitle($title);
}
