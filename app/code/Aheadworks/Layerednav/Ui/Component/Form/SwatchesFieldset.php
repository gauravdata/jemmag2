<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Ui\Component\Form;

/**
 * Class SwatchesFieldset
 *
 * @package Aheadworks\Layerednav\Ui\Component\Form
 */
class SwatchesFieldset extends GlobalScopeFieldset
{
    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        if ($this->contextChecker->isNotGlobal($this->getContext())) {
            $config = $this->getConfig();
            if (isset($config['imports']) && isset($config['imports']['visible'])) {
                unset($config['imports']['visible']);
            }
            $this->setConfig($config);
        }

        parent::prepare();
    }
}
