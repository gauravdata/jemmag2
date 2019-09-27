<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Api\Data;

/**
 * Interface FileInterface
 *
 * @package Aheadworks\Layerednav\Api\Data
 */
interface FileInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID            = 'id';
    const NAME          = 'name';
    const FILE_NAME     = 'file_name';
    /**#@-*/

    /**
     * Get id
     *
     * @return string
     */
    public function getId();

    /**
     * Set id
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get file name
     *
     * @return string
     */
    public function getFileName();

    /**
     * Set file name
     *
     * @param string $fileName
     * @return $this
     */
    public function setFileName($fileName);
}
