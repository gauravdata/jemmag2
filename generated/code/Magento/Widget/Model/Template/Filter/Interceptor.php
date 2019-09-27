<?php
namespace Magento\Widget\Model\Template\Filter;

/**
 * Interceptor class for @see \Magento\Widget\Model\Template\Filter
 */
class Interceptor extends \Magento\Widget\Model\Template\Filter implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Stdlib\StringUtils $string, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Escaper $escaper, \Magento\Framework\View\Asset\Repository $assetRepo, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Variable\Model\VariableFactory $coreVariableFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\View\LayoutInterface $layout, \Magento\Framework\View\LayoutFactory $layoutFactory, \Magento\Framework\App\State $appState, \Magento\Framework\UrlInterface $urlModel, \Pelago\Emogrifier $emogrifier, \Magento\Variable\Model\Source\Variables $configVariables, \Magento\Widget\Model\ResourceModel\Widget $widgetResource, \Magento\Widget\Model\Widget $widget)
    {
        $this->___init();
        parent::__construct($string, $logger, $escaper, $assetRepo, $scopeConfig, $coreVariableFactory, $storeManager, $layout, $layoutFactory, $appState, $urlModel, $emogrifier, $configVariables, $widgetResource, $widget);
    }

    /**
     * {@inheritdoc}
     */
    public function generateWidget($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'generateWidget');
        if (!$pluginInfo) {
            return parent::generateWidget($construction);
        } else {
            return $this->___callPlugins('generateWidget', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function widgetDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'widgetDirective');
        if (!$pluginInfo) {
            return parent::widgetDirective($construction);
        } else {
            return $this->___callPlugins('widgetDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function mediaDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'mediaDirective');
        if (!$pluginInfo) {
            return parent::mediaDirective($construction);
        } else {
            return $this->___callPlugins('mediaDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setUseSessionInUrl($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setUseSessionInUrl');
        if (!$pluginInfo) {
            return parent::setUseSessionInUrl($flag);
        } else {
            return $this->___callPlugins('setUseSessionInUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setUseAbsoluteLinks($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setUseAbsoluteLinks');
        if (!$pluginInfo) {
            return parent::setUseAbsoluteLinks($flag);
        } else {
            return $this->___callPlugins('setUseAbsoluteLinks', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPlainTemplateMode($plainTemplateMode)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setPlainTemplateMode');
        if (!$pluginInfo) {
            return parent::setPlainTemplateMode($plainTemplateMode);
        } else {
            return $this->___callPlugins('setPlainTemplateMode', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isPlainTemplateMode()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isPlainTemplateMode');
        if (!$pluginInfo) {
            return parent::isPlainTemplateMode();
        } else {
            return $this->___callPlugins('isPlainTemplateMode', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setIsChildTemplate($isChildTemplate)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setIsChildTemplate');
        if (!$pluginInfo) {
            return parent::setIsChildTemplate($isChildTemplate);
        } else {
            return $this->___callPlugins('setIsChildTemplate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isChildTemplate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isChildTemplate');
        if (!$pluginInfo) {
            return parent::isChildTemplate();
        } else {
            return $this->___callPlugins('isChildTemplate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($storeId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setStoreId');
        if (!$pluginInfo) {
            return parent::setStoreId($storeId);
        } else {
            return $this->___callPlugins('setStoreId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDesignParams(array $designParams)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDesignParams');
        if (!$pluginInfo) {
            return parent::setDesignParams($designParams);
        } else {
            return $this->___callPlugins('setDesignParams', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDesignParams()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDesignParams');
        if (!$pluginInfo) {
            return parent::getDesignParams();
        } else {
            return $this->___callPlugins('getDesignParams', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getStoreId');
        if (!$pluginInfo) {
            return parent::getStoreId();
        } else {
            return $this->___callPlugins('getStoreId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function blockDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'blockDirective');
        if (!$pluginInfo) {
            return parent::blockDirective($construction);
        } else {
            return $this->___callPlugins('blockDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function layoutDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'layoutDirective');
        if (!$pluginInfo) {
            return parent::layoutDirective($construction);
        } else {
            return $this->___callPlugins('layoutDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function emulateAreaCallback()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'emulateAreaCallback');
        if (!$pluginInfo) {
            return parent::emulateAreaCallback();
        } else {
            return $this->___callPlugins('emulateAreaCallback', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function viewDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'viewDirective');
        if (!$pluginInfo) {
            return parent::viewDirective($construction);
        } else {
            return $this->___callPlugins('viewDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function storeDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'storeDirective');
        if (!$pluginInfo) {
            return parent::storeDirective($construction);
        } else {
            return $this->___callPlugins('storeDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setUrlModel(\Magento\Framework\UrlInterface $urlModel)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setUrlModel');
        if (!$pluginInfo) {
            return parent::setUrlModel($urlModel);
        } else {
            return $this->___callPlugins('setUrlModel', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function transDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'transDirective');
        if (!$pluginInfo) {
            return parent::transDirective($construction);
        } else {
            return $this->___callPlugins('transDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function varDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'varDirective');
        if (!$pluginInfo) {
            return parent::varDirective($construction);
        } else {
            return $this->___callPlugins('varDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modifierEscape($value, $type = 'html')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'modifierEscape');
        if (!$pluginInfo) {
            return parent::modifierEscape($value, $type);
        } else {
            return $this->___callPlugins('modifierEscape', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function protocolDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'protocolDirective');
        if (!$pluginInfo) {
            return parent::protocolDirective($construction);
        } else {
            return $this->___callPlugins('protocolDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'configDirective');
        if (!$pluginInfo) {
            return parent::configDirective($construction);
        } else {
            return $this->___callPlugins('configDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function customvarDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'customvarDirective');
        if (!$pluginInfo) {
            return parent::customvarDirective($construction);
        } else {
            return $this->___callPlugins('customvarDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function cssDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'cssDirective');
        if (!$pluginInfo) {
            return parent::cssDirective($construction);
        } else {
            return $this->___callPlugins('cssDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function inlinecssDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'inlinecssDirective');
        if (!$pluginInfo) {
            return parent::inlinecssDirective($construction);
        } else {
            return $this->___callPlugins('inlinecssDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCssFilesContent(array $files)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCssFilesContent');
        if (!$pluginInfo) {
            return parent::getCssFilesContent($files);
        } else {
            return $this->___callPlugins('getCssFilesContent', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function applyInlineCss($html)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'applyInlineCss');
        if (!$pluginInfo) {
            return parent::applyInlineCss($html);
        } else {
            return $this->___callPlugins('applyInlineCss', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function filter($value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'filter');
        if (!$pluginInfo) {
            return parent::filter($value);
        } else {
            return $this->___callPlugins('filter', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setVariables(array $variables)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setVariables');
        if (!$pluginInfo) {
            return parent::setVariables($variables);
        } else {
            return $this->___callPlugins('setVariables', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateProcessor(callable $callback)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTemplateProcessor');
        if (!$pluginInfo) {
            return parent::setTemplateProcessor($callback);
        } else {
            return $this->___callPlugins('setTemplateProcessor', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateProcessor()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTemplateProcessor');
        if (!$pluginInfo) {
            return parent::getTemplateProcessor();
        } else {
            return $this->___callPlugins('getTemplateProcessor', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addAfterFilterCallback(callable $afterFilterCallback)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addAfterFilterCallback');
        if (!$pluginInfo) {
            return parent::addAfterFilterCallback($afterFilterCallback);
        } else {
            return $this->___callPlugins('addAfterFilterCallback', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function templateDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'templateDirective');
        if (!$pluginInfo) {
            return parent::templateDirective($construction);
        } else {
            return $this->___callPlugins('templateDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function dependDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dependDirective');
        if (!$pluginInfo) {
            return parent::dependDirective($construction);
        } else {
            return $this->___callPlugins('dependDirective', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function ifDirective($construction)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'ifDirective');
        if (!$pluginInfo) {
            return parent::ifDirective($construction);
        } else {
            return $this->___callPlugins('ifDirective', func_get_args(), $pluginInfo);
        }
    }
}
