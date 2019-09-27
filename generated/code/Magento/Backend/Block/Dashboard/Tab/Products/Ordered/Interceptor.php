<?php
namespace Magento\Backend\Block\Dashboard\Tab\Products\Ordered;

/**
 * Interceptor class for @see \Magento\Backend\Block\Dashboard\Tab\Products\Ordered
 */
class Interceptor extends \Magento\Backend\Block\Dashboard\Tab\Products\Ordered implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Backend\Helper\Data $backendHelper, \Magento\Framework\Module\Manager $moduleManager, \Magento\Sales\Model\ResourceModel\Report\Bestsellers\CollectionFactory $collectionFactory, array $data = [])
    {
        $this->___init();
        parent::__construct($context, $backendHelper, $moduleManager, $collectionFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getRowUrl($row)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRowUrl');
        if (!$pluginInfo) {
            return parent::getRowUrl($row);
        } else {
            return $this->___callPlugins('getRowUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnSet()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getColumnSet');
        if (!$pluginInfo) {
            return parent::getColumnSet();
        } else {
            return $this->___callPlugins('getColumnSet', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExportButtonHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExportButtonHtml');
        if (!$pluginInfo) {
            return parent::getExportButtonHtml();
        } else {
            return $this->___callPlugins('getExportButtonHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addExportType($url, $label)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addExportType');
        if (!$pluginInfo) {
            return parent::addExportType($url, $label);
        } else {
            return $this->___callPlugins('addExportType', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addColumn($columnId, $column)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addColumn');
        if (!$pluginInfo) {
            return parent::addColumn($columnId, $column);
        } else {
            return $this->___callPlugins('addColumn', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeColumn($columnId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'removeColumn');
        if (!$pluginInfo) {
            return parent::removeColumn($columnId);
        } else {
            return $this->___callPlugins('removeColumn', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addColumnAfter($columnId, $column, $after)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addColumnAfter');
        if (!$pluginInfo) {
            return parent::addColumnAfter($columnId, $column, $after);
        } else {
            return $this->___callPlugins('addColumnAfter', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addColumnsOrder($columnId, $after)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addColumnsOrder');
        if (!$pluginInfo) {
            return parent::addColumnsOrder($columnId, $after);
        } else {
            return $this->___callPlugins('addColumnsOrder', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnsOrder()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getColumnsOrder');
        if (!$pluginInfo) {
            return parent::getColumnsOrder();
        } else {
            return $this->___callPlugins('getColumnsOrder', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function sortColumnsByOrder()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'sortColumnsByOrder');
        if (!$pluginInfo) {
            return parent::sortColumnsByOrder();
        } else {
            return $this->___callPlugins('sortColumnsByOrder', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLastColumnId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLastColumnId');
        if (!$pluginInfo) {
            return parent::getLastColumnId();
        } else {
            return $this->___callPlugins('getLastColumnId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHtml');
        if (!$pluginInfo) {
            return parent::getHtml();
        } else {
            return $this->___callPlugins('getHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMassactionIdField()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMassactionIdField');
        if (!$pluginInfo) {
            return parent::getMassactionIdField();
        } else {
            return $this->___callPlugins('getMassactionIdField', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMassactionIdField($idField)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setMassactionIdField');
        if (!$pluginInfo) {
            return parent::setMassactionIdField($idField);
        } else {
            return $this->___callPlugins('setMassactionIdField', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMassactionIdFilter()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMassactionIdFilter');
        if (!$pluginInfo) {
            return parent::getMassactionIdFilter();
        } else {
            return $this->___callPlugins('getMassactionIdFilter', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMassactionIdFilter($idFilter)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setMassactionIdFilter');
        if (!$pluginInfo) {
            return parent::setMassactionIdFilter($idFilter);
        } else {
            return $this->___callPlugins('setMassactionIdFilter', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMassactionBlockName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMassactionBlockName');
        if (!$pluginInfo) {
            return parent::getMassactionBlockName();
        } else {
            return $this->___callPlugins('getMassactionBlockName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMassactionBlockName($blockName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setMassactionBlockName');
        if (!$pluginInfo) {
            return parent::setMassactionBlockName($blockName);
        } else {
            return $this->___callPlugins('setMassactionBlockName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMassactionBlock()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMassactionBlock');
        if (!$pluginInfo) {
            return parent::getMassactionBlock();
        } else {
            return $this->___callPlugins('getMassactionBlock', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMassactionBlockHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMassactionBlockHtml');
        if (!$pluginInfo) {
            return parent::getMassactionBlockHtml();
        } else {
            return $this->___callPlugins('getMassactionBlockHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubTotalColumns()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSubTotalColumns');
        if (!$pluginInfo) {
            return parent::getSubTotalColumns();
        } else {
            return $this->___callPlugins('getSubTotalColumns', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRenderCell($item, $column)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'shouldRenderCell');
        if (!$pluginInfo) {
            return parent::shouldRenderCell($item, $column);
        } else {
            return $this->___callPlugins('shouldRenderCell', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyCellLabel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEmptyCellLabel');
        if (!$pluginInfo) {
            return parent::getEmptyCellLabel();
        } else {
            return $this->___callPlugins('getEmptyCellLabel', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setEmptyCellLabel($label)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setEmptyCellLabel');
        if (!$pluginInfo) {
            return parent::setEmptyCellLabel($label);
        } else {
            return $this->___callPlugins('setEmptyCellLabel', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleRows($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMultipleRows');
        if (!$pluginInfo) {
            return parent::getMultipleRows($item);
        } else {
            return $this->___callPlugins('getMultipleRows', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMultipleRowColumns()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMultipleRowColumns');
        if (!$pluginInfo) {
            return parent::getMultipleRowColumns();
        } else {
            return $this->___callPlugins('getMultipleRowColumns', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRenderSubTotal($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'shouldRenderSubTotal');
        if (!$pluginInfo) {
            return parent::shouldRenderSubTotal($item);
        } else {
            return $this->___callPlugins('shouldRenderSubTotal', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRowspan($item, $column)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRowspan');
        if (!$pluginInfo) {
            return parent::getRowspan($item, $column);
        } else {
            return $this->___callPlugins('getRowspan', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isColumnGrouped($column, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isColumnGrouped');
        if (!$pluginInfo) {
            return parent::isColumnGrouped($column, $value);
        } else {
            return $this->___callPlugins('isColumnGrouped', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function shouldRenderEmptyCell($item, $column)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'shouldRenderEmptyCell');
        if (!$pluginInfo) {
            return parent::shouldRenderEmptyCell($item, $column);
        } else {
            return $this->___callPlugins('shouldRenderEmptyCell', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyCellColspan()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEmptyCellColspan');
        if (!$pluginInfo) {
            return parent::getEmptyCellColspan();
        } else {
            return $this->___callPlugins('getEmptyCellColspan', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubTotalItem($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSubTotalItem');
        if (!$pluginInfo) {
            return parent::getSubTotalItem($item);
        } else {
            return $this->___callPlugins('getSubTotalItem', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnCount()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getColumnCount');
        if (!$pluginInfo) {
            return parent::getColumnCount();
        } else {
            return $this->___callPlugins('getColumnCount', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setHeadersVisibility($visible = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setHeadersVisibility');
        if (!$pluginInfo) {
            return parent::setHeadersVisibility($visible);
        } else {
            return $this->___callPlugins('setHeadersVisibility', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHeadersVisibility()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHeadersVisibility');
        if (!$pluginInfo) {
            return parent::getHeadersVisibility();
        } else {
            return $this->___callPlugins('getHeadersVisibility', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setFilterVisibility($visible = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setFilterVisibility');
        if (!$pluginInfo) {
            return parent::setFilterVisibility($visible);
        } else {
            return $this->___callPlugins('setFilterVisibility', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFilterVisibility()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFilterVisibility');
        if (!$pluginInfo) {
            return parent::getFilterVisibility();
        } else {
            return $this->___callPlugins('getFilterVisibility', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setEmptyText($text)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setEmptyText');
        if (!$pluginInfo) {
            return parent::setEmptyText($text);
        } else {
            return $this->___callPlugins('setEmptyText', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyText()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEmptyText');
        if (!$pluginInfo) {
            return parent::getEmptyText();
        } else {
            return $this->___callPlugins('getEmptyText', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setEmptyTextClass($cssClass)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setEmptyTextClass');
        if (!$pluginInfo) {
            return parent::setEmptyTextClass($cssClass);
        } else {
            return $this->___callPlugins('setEmptyTextClass', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getEmptyTextClass()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getEmptyTextClass');
        if (!$pluginInfo) {
            return parent::getEmptyTextClass();
        } else {
            return $this->___callPlugins('getEmptyTextClass', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setIsCollapsed($isCollapsed)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setIsCollapsed');
        if (!$pluginInfo) {
            return parent::setIsCollapsed($isCollapsed);
        } else {
            return $this->___callPlugins('setIsCollapsed', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getIsCollapsed()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getIsCollapsed');
        if (!$pluginInfo) {
            return parent::getIsCollapsed();
        } else {
            return $this->___callPlugins('getIsCollapsed', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function _exportIterateCollection($callback, array $args)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '_exportIterateCollection');
        if (!$pluginInfo) {
            return parent::_exportIterateCollection($callback, $args);
        } else {
            return $this->___callPlugins('_exportIterateCollection', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCsvFile()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCsvFile');
        if (!$pluginInfo) {
            return parent::getCsvFile();
        } else {
            return $this->___callPlugins('getCsvFile', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCsv()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCsv');
        if (!$pluginInfo) {
            return parent::getCsv();
        } else {
            return $this->___callPlugins('getCsv', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getXml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getXml');
        if (!$pluginInfo) {
            return parent::getXml();
        } else {
            return $this->___callPlugins('getXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRowRecord(\Magento\Framework\DataObject $data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRowRecord');
        if (!$pluginInfo) {
            return parent::getRowRecord($data);
        } else {
            return $this->___callPlugins('getRowRecord', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExcelFile($sheetName = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExcelFile');
        if (!$pluginInfo) {
            return parent::getExcelFile($sheetName);
        } else {
            return $this->___callPlugins('getExcelFile', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExcel()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExcel');
        if (!$pluginInfo) {
            return parent::getExcel();
        } else {
            return $this->___callPlugins('getExcel', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExportTypes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExportTypes');
        if (!$pluginInfo) {
            return parent::getExportTypes();
        } else {
            return $this->___callPlugins('getExportTypes', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCollection($collection)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCollection');
        if (!$pluginInfo) {
            return parent::setCollection($collection);
        } else {
            return $this->___callPlugins('setCollection', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCollection()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCollection');
        if (!$pluginInfo) {
            return parent::getCollection();
        } else {
            return $this->___callPlugins('getCollection', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCountSubTotals($flag = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCountSubTotals');
        if (!$pluginInfo) {
            return parent::setCountSubTotals($flag);
        } else {
            return $this->___callPlugins('setCountSubTotals', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCountSubTotals()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCountSubTotals');
        if (!$pluginInfo) {
            return parent::getCountSubTotals();
        } else {
            return $this->___callPlugins('getCountSubTotals', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setSubTotals(array $items)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setSubTotals');
        if (!$pluginInfo) {
            return parent::setSubTotals($items);
        } else {
            return $this->___callPlugins('setSubTotals', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubTotals()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSubTotals');
        if (!$pluginInfo) {
            return parent::getSubTotals();
        } else {
            return $this->___callPlugins('getSubTotals', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMainButtonsHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMainButtonsHtml');
        if (!$pluginInfo) {
            return parent::getMainButtonsHtml();
        } else {
            return $this->___callPlugins('getMainButtonsHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getExportBlock()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getExportBlock');
        if (!$pluginInfo) {
            return parent::getExportBlock();
        } else {
            return $this->___callPlugins('getExportBlock', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getColumns()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getColumns');
        if (!$pluginInfo) {
            return parent::getColumns();
        } else {
            return $this->___callPlugins('getColumns', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getColumn($columnId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getColumn');
        if (!$pluginInfo) {
            return parent::getColumn($columnId);
        } else {
            return $this->___callPlugins('getColumn', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPreparedCollection()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPreparedCollection');
        if (!$pluginInfo) {
            return parent::getPreparedCollection();
        } else {
            return $this->___callPlugins('getPreparedCollection', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVarNameLimit()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVarNameLimit');
        if (!$pluginInfo) {
            return parent::getVarNameLimit();
        } else {
            return $this->___callPlugins('getVarNameLimit', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVarNamePage()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVarNamePage');
        if (!$pluginInfo) {
            return parent::getVarNamePage();
        } else {
            return $this->___callPlugins('getVarNamePage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVarNameSort()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVarNameSort');
        if (!$pluginInfo) {
            return parent::getVarNameSort();
        } else {
            return $this->___callPlugins('getVarNameSort', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVarNameDir()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVarNameDir');
        if (!$pluginInfo) {
            return parent::getVarNameDir();
        } else {
            return $this->___callPlugins('getVarNameDir', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVarNameFilter()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVarNameFilter');
        if (!$pluginInfo) {
            return parent::getVarNameFilter();
        } else {
            return $this->___callPlugins('getVarNameFilter', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setVarNameLimit($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setVarNameLimit');
        if (!$pluginInfo) {
            return parent::setVarNameLimit($name);
        } else {
            return $this->___callPlugins('setVarNameLimit', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setVarNamePage($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setVarNamePage');
        if (!$pluginInfo) {
            return parent::setVarNamePage($name);
        } else {
            return $this->___callPlugins('setVarNamePage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setVarNameSort($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setVarNameSort');
        if (!$pluginInfo) {
            return parent::setVarNameSort($name);
        } else {
            return $this->___callPlugins('setVarNameSort', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setVarNameDir($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setVarNameDir');
        if (!$pluginInfo) {
            return parent::setVarNameDir($name);
        } else {
            return $this->___callPlugins('setVarNameDir', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setVarNameFilter($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setVarNameFilter');
        if (!$pluginInfo) {
            return parent::setVarNameFilter($name);
        } else {
            return $this->___callPlugins('setVarNameFilter', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setPagerVisibility($visible = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setPagerVisibility');
        if (!$pluginInfo) {
            return parent::setPagerVisibility($visible);
        } else {
            return $this->___callPlugins('setPagerVisibility', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPagerVisibility()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getPagerVisibility');
        if (!$pluginInfo) {
            return parent::getPagerVisibility();
        } else {
            return $this->___callPlugins('getPagerVisibility', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setMessageBlockVisibility($visible = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setMessageBlockVisibility');
        if (!$pluginInfo) {
            return parent::setMessageBlockVisibility($visible);
        } else {
            return $this->___callPlugins('setMessageBlockVisibility', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageBlockVisibility()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getMessageBlockVisibility');
        if (!$pluginInfo) {
            return parent::getMessageBlockVisibility();
        } else {
            return $this->___callPlugins('getMessageBlockVisibility', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultLimit($limit)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDefaultLimit');
        if (!$pluginInfo) {
            return parent::setDefaultLimit($limit);
        } else {
            return $this->___callPlugins('setDefaultLimit', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultPage($page)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDefaultPage');
        if (!$pluginInfo) {
            return parent::setDefaultPage($page);
        } else {
            return $this->___callPlugins('setDefaultPage', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultSort($sort)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDefaultSort');
        if (!$pluginInfo) {
            return parent::setDefaultSort($sort);
        } else {
            return $this->___callPlugins('setDefaultSort', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultDir($dir)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDefaultDir');
        if (!$pluginInfo) {
            return parent::setDefaultDir($dir);
        } else {
            return $this->___callPlugins('setDefaultDir', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultFilter($filter)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDefaultFilter');
        if (!$pluginInfo) {
            return parent::setDefaultFilter($filter);
        } else {
            return $this->___callPlugins('setDefaultFilter', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canDisplayContainer()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canDisplayContainer');
        if (!$pluginInfo) {
            return parent::canDisplayContainer();
        } else {
            return $this->___callPlugins('canDisplayContainer', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getGridUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getGridUrl');
        if (!$pluginInfo) {
            return parent::getGridUrl();
        } else {
            return $this->___callPlugins('getGridUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAbsoluteGridUrl($params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAbsoluteGridUrl');
        if (!$pluginInfo) {
            return parent::getAbsoluteGridUrl($params);
        } else {
            return $this->___callPlugins('getAbsoluteGridUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParam($paramName, $default = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getParam');
        if (!$pluginInfo) {
            return parent::getParam($paramName, $default);
        } else {
            return $this->___callPlugins('getParam', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setSaveParametersInSession($flag)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setSaveParametersInSession');
        if (!$pluginInfo) {
            return parent::setSaveParametersInSession($flag);
        } else {
            return $this->___callPlugins('setSaveParametersInSession', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getJsObjectName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getJsObjectName');
        if (!$pluginInfo) {
            return parent::getJsObjectName();
        } else {
            return $this->___callPlugins('getJsObjectName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setCountTotals($count = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCountTotals');
        if (!$pluginInfo) {
            return parent::setCountTotals($count);
        } else {
            return $this->___callPlugins('setCountTotals', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCountTotals()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCountTotals');
        if (!$pluginInfo) {
            return parent::getCountTotals();
        } else {
            return $this->___callPlugins('getCountTotals', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTotals(\Magento\Framework\DataObject $totals)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTotals');
        if (!$pluginInfo) {
            return parent::setTotals($totals);
        } else {
            return $this->___callPlugins('setTotals', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTotals()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTotals');
        if (!$pluginInfo) {
            return parent::getTotals();
        } else {
            return $this->___callPlugins('getTotals', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getResetFilterButtonHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getResetFilterButtonHtml');
        if (!$pluginInfo) {
            return parent::getResetFilterButtonHtml();
        } else {
            return $this->___callPlugins('getResetFilterButtonHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchButtonHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSearchButtonHtml');
        if (!$pluginInfo) {
            return parent::getSearchButtonHtml();
        } else {
            return $this->___callPlugins('getSearchButtonHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getId');
        if (!$pluginInfo) {
            return parent::getId();
        } else {
            return $this->___callPlugins('getId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSuffixId($suffix)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getSuffixId');
        if (!$pluginInfo) {
            return parent::getSuffixId($suffix);
        } else {
            return $this->___callPlugins('getSuffixId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHtmlId()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHtmlId');
        if (!$pluginInfo) {
            return parent::getHtmlId();
        } else {
            return $this->___callPlugins('getHtmlId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentUrl($params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCurrentUrl');
        if (!$pluginInfo) {
            return parent::getCurrentUrl($params);
        } else {
            return $this->___callPlugins('getCurrentUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonHtml($label, $onclick, $class = '', $buttonId = null, $dataAttr = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getButtonHtml');
        if (!$pluginInfo) {
            return parent::getButtonHtml($label, $onclick, $class, $buttonId, $dataAttr);
        } else {
            return $this->___callPlugins('getButtonHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFormKey()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFormKey');
        if (!$pluginInfo) {
            return parent::getFormKey();
        } else {
            return $this->___callPlugins('getFormKey', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isOutputEnabled($moduleName = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isOutputEnabled');
        if (!$pluginInfo) {
            return parent::isOutputEnabled($moduleName);
        } else {
            return $this->___callPlugins('isOutputEnabled', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorization()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getAuthorization');
        if (!$pluginInfo) {
            return parent::getAuthorization();
        } else {
            return $this->___callPlugins('getAuthorization', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getToolbar()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getToolbar');
        if (!$pluginInfo) {
            return parent::getToolbar();
        } else {
            return $this->___callPlugins('getToolbar', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplateContext($templateContext)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTemplateContext');
        if (!$pluginInfo) {
            return parent::setTemplateContext($templateContext);
        } else {
            return $this->___callPlugins('setTemplateContext', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTemplate');
        if (!$pluginInfo) {
            return parent::getTemplate();
        } else {
            return $this->___callPlugins('getTemplate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setTemplate($template)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setTemplate');
        if (!$pluginInfo) {
            return parent::setTemplate($template);
        } else {
            return $this->___callPlugins('setTemplate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTemplateFile($template = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getTemplateFile');
        if (!$pluginInfo) {
            return parent::getTemplateFile($template);
        } else {
            return $this->___callPlugins('getTemplateFile', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getArea()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getArea');
        if (!$pluginInfo) {
            return parent::getArea();
        } else {
            return $this->___callPlugins('getArea', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function assign($key, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'assign');
        if (!$pluginInfo) {
            return parent::assign($key, $value);
        } else {
            return $this->___callPlugins('assign', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetchView($fileName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'fetchView');
        if (!$pluginInfo) {
            return parent::fetchView($fileName);
        } else {
            return $this->___callPlugins('fetchView', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBaseUrl()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBaseUrl');
        if (!$pluginInfo) {
            return parent::getBaseUrl();
        } else {
            return $this->___callPlugins('getBaseUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectData(\Magento\Framework\DataObject $object, $key)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getObjectData');
        if (!$pluginInfo) {
            return parent::getObjectData($object, $key);
        } else {
            return $this->___callPlugins('getObjectData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKeyInfo()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCacheKeyInfo');
        if (!$pluginInfo) {
            return parent::getCacheKeyInfo();
        } else {
            return $this->___callPlugins('getCacheKeyInfo', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getJsLayout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getJsLayout');
        if (!$pluginInfo) {
            return parent::getJsLayout();
        } else {
            return $this->___callPlugins('getJsLayout', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getRequest');
        if (!$pluginInfo) {
            return parent::getRequest();
        } else {
            return $this->___callPlugins('getRequest', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParentBlock()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getParentBlock');
        if (!$pluginInfo) {
            return parent::getParentBlock();
        } else {
            return $this->___callPlugins('getParentBlock', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setLayout(\Magento\Framework\View\LayoutInterface $layout)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setLayout');
        if (!$pluginInfo) {
            return parent::setLayout($layout);
        } else {
            return $this->___callPlugins('setLayout', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLayout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getLayout');
        if (!$pluginInfo) {
            return parent::getLayout();
        } else {
            return $this->___callPlugins('getLayout', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setNameInLayout($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setNameInLayout');
        if (!$pluginInfo) {
            return parent::setNameInLayout($name);
        } else {
            return $this->___callPlugins('setNameInLayout', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildNames()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getChildNames');
        if (!$pluginInfo) {
            return parent::getChildNames();
        } else {
            return $this->___callPlugins('getChildNames', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setAttribute($name, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setAttribute');
        if (!$pluginInfo) {
            return parent::setAttribute($name, $value);
        } else {
            return $this->___callPlugins('setAttribute', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setChild($alias, $block)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setChild');
        if (!$pluginInfo) {
            return parent::setChild($alias, $block);
        } else {
            return $this->___callPlugins('setChild', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addChild($alias, $block, $data = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addChild');
        if (!$pluginInfo) {
            return parent::addChild($alias, $block, $data);
        } else {
            return $this->___callPlugins('addChild', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetChild($alias)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetChild');
        if (!$pluginInfo) {
            return parent::unsetChild($alias);
        } else {
            return $this->___callPlugins('unsetChild', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetCallChild($alias, $callback, $result, $params)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetCallChild');
        if (!$pluginInfo) {
            return parent::unsetCallChild($alias, $callback, $result, $params);
        } else {
            return $this->___callPlugins('unsetCallChild', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetChildren()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetChildren');
        if (!$pluginInfo) {
            return parent::unsetChildren();
        } else {
            return $this->___callPlugins('unsetChildren', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildBlock($alias)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getChildBlock');
        if (!$pluginInfo) {
            return parent::getChildBlock($alias);
        } else {
            return $this->___callPlugins('getChildBlock', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildHtml($alias = '', $useCache = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getChildHtml');
        if (!$pluginInfo) {
            return parent::getChildHtml($alias, $useCache);
        } else {
            return $this->___callPlugins('getChildHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildChildHtml($alias, $childChildAlias = '', $useCache = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getChildChildHtml');
        if (!$pluginInfo) {
            return parent::getChildChildHtml($alias, $childChildAlias, $useCache);
        } else {
            return $this->___callPlugins('getChildChildHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockHtml($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getBlockHtml');
        if (!$pluginInfo) {
            return parent::getBlockHtml($name);
        } else {
            return $this->___callPlugins('getBlockHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function insert($element, $siblingName = 0, $after = true, $alias = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'insert');
        if (!$pluginInfo) {
            return parent::insert($element, $siblingName, $after, $alias);
        } else {
            return $this->___callPlugins('insert', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function append($element, $alias = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'append');
        if (!$pluginInfo) {
            return parent::append($element, $alias);
        } else {
            return $this->___callPlugins('append', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getGroupChildNames($groupName)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getGroupChildNames');
        if (!$pluginInfo) {
            return parent::getGroupChildNames($groupName);
        } else {
            return $this->___callPlugins('getGroupChildNames', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getChildData($alias, $key = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getChildData');
        if (!$pluginInfo) {
            return parent::getChildData($alias, $key);
        } else {
            return $this->___callPlugins('getChildData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toHtml');
        if (!$pluginInfo) {
            return parent::toHtml();
        } else {
            return $this->___callPlugins('toHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUiId($arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUiId');
        if (!$pluginInfo) {
            return parent::getUiId($arg1, $arg2, $arg3, $arg4, $arg5);
        } else {
            return $this->___callPlugins('getUiId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getJsId($arg1 = null, $arg2 = null, $arg3 = null, $arg4 = null, $arg5 = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getJsId');
        if (!$pluginInfo) {
            return parent::getJsId($arg1, $arg2, $arg3, $arg4, $arg5);
        } else {
            return $this->___callPlugins('getJsId', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUrl($route = '', $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getUrl');
        if (!$pluginInfo) {
            return parent::getUrl($route, $params);
        } else {
            return $this->___callPlugins('getUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getViewFileUrl($fileId, array $params = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getViewFileUrl');
        if (!$pluginInfo) {
            return parent::getViewFileUrl($fileId, $params);
        } else {
            return $this->___callPlugins('getViewFileUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function formatDate($date = null, $format = 3, $showTime = false, $timezone = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'formatDate');
        if (!$pluginInfo) {
            return parent::formatDate($date, $format, $showTime, $timezone);
        } else {
            return $this->___callPlugins('formatDate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function formatTime($time = null, $format = 3, $showDate = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'formatTime');
        if (!$pluginInfo) {
            return parent::formatTime($time, $format, $showDate);
        } else {
            return $this->___callPlugins('formatTime', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getModuleName()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getModuleName');
        if (!$pluginInfo) {
            return parent::getModuleName();
        } else {
            return $this->___callPlugins('getModuleName', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtml');
        if (!$pluginInfo) {
            return parent::escapeHtml($data, $allowedTags);
        } else {
            return $this->___callPlugins('escapeHtml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeJs($string)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeJs');
        if (!$pluginInfo) {
            return parent::escapeJs($string);
        } else {
            return $this->___callPlugins('escapeJs', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeHtmlAttr($string, $escapeSingleQuote = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeHtmlAttr');
        if (!$pluginInfo) {
            return parent::escapeHtmlAttr($string, $escapeSingleQuote);
        } else {
            return $this->___callPlugins('escapeHtmlAttr', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeCss($string)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeCss');
        if (!$pluginInfo) {
            return parent::escapeCss($string);
        } else {
            return $this->___callPlugins('escapeCss', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function stripTags($data, $allowableTags = null, $allowHtmlEntities = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'stripTags');
        if (!$pluginInfo) {
            return parent::stripTags($data, $allowableTags, $allowHtmlEntities);
        } else {
            return $this->___callPlugins('stripTags', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeUrl($string)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeUrl');
        if (!$pluginInfo) {
            return parent::escapeUrl($string);
        } else {
            return $this->___callPlugins('escapeUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeXssInUrl($data)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeXssInUrl');
        if (!$pluginInfo) {
            return parent::escapeXssInUrl($data);
        } else {
            return $this->___callPlugins('escapeXssInUrl', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeQuote($data, $addSlashes = false)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeQuote');
        if (!$pluginInfo) {
            return parent::escapeQuote($data, $addSlashes);
        } else {
            return $this->___callPlugins('escapeQuote', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function escapeJsQuote($data, $quote = '\'')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'escapeJsQuote');
        if (!$pluginInfo) {
            return parent::escapeJsQuote($data, $quote);
        } else {
            return $this->___callPlugins('escapeJsQuote', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getNameInLayout()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getNameInLayout');
        if (!$pluginInfo) {
            return parent::getNameInLayout();
        } else {
            return $this->___callPlugins('getNameInLayout', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCacheKey');
        if (!$pluginInfo) {
            return parent::getCacheKey();
        } else {
            return $this->___callPlugins('getCacheKey', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getVar($name, $module = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getVar');
        if (!$pluginInfo) {
            return parent::getVar($name, $module);
        } else {
            return $this->___callPlugins('getVar', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isScopePrivate()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isScopePrivate');
        if (!$pluginInfo) {
            return parent::isScopePrivate();
        } else {
            return $this->___callPlugins('isScopePrivate', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addData(array $arr)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'addData');
        if (!$pluginInfo) {
            return parent::addData($arr);
        } else {
            return $this->___callPlugins('addData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setData($key, $value = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setData');
        if (!$pluginInfo) {
            return parent::setData($key, $value);
        } else {
            return $this->___callPlugins('setData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unsetData($key = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'unsetData');
        if (!$pluginInfo) {
            return parent::unsetData($key);
        } else {
            return $this->___callPlugins('unsetData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getData($key = '', $index = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        if (!$pluginInfo) {
            return parent::getData($key, $index);
        } else {
            return $this->___callPlugins('getData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByPath($path)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByPath');
        if (!$pluginInfo) {
            return parent::getDataByPath($path);
        } else {
            return $this->___callPlugins('getDataByPath', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataByKey($key)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataByKey');
        if (!$pluginInfo) {
            return parent::getDataByKey($key);
        } else {
            return $this->___callPlugins('getDataByKey', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDataUsingMethod($key, $args = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setDataUsingMethod');
        if (!$pluginInfo) {
            return parent::setDataUsingMethod($key, $args);
        } else {
            return $this->___callPlugins('setDataUsingMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDataUsingMethod($key, $args = null)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDataUsingMethod');
        if (!$pluginInfo) {
            return parent::getDataUsingMethod($key, $args);
        } else {
            return $this->___callPlugins('getDataUsingMethod', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasData($key = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'hasData');
        if (!$pluginInfo) {
            return parent::hasData($key);
        } else {
            return $this->___callPlugins('hasData', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toArray');
        if (!$pluginInfo) {
            return parent::toArray($keys);
        } else {
            return $this->___callPlugins('toArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToArray(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToArray');
        if (!$pluginInfo) {
            return parent::convertToArray($keys);
        } else {
            return $this->___callPlugins('convertToArray', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toXml(array $keys = [], $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toXml');
        if (!$pluginInfo) {
            return parent::toXml($keys, $rootName, $addOpenTag, $addCdata);
        } else {
            return $this->___callPlugins('toXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToXml(array $arrAttributes = [], $rootName = 'item', $addOpenTag = false, $addCdata = true)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToXml');
        if (!$pluginInfo) {
            return parent::convertToXml($arrAttributes, $rootName, $addOpenTag, $addCdata);
        } else {
            return $this->___callPlugins('convertToXml', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toJson(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toJson');
        if (!$pluginInfo) {
            return parent::toJson($keys);
        } else {
            return $this->___callPlugins('toJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function convertToJson(array $keys = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'convertToJson');
        if (!$pluginInfo) {
            return parent::convertToJson($keys);
        } else {
            return $this->___callPlugins('convertToJson', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function toString($format = '')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toString');
        if (!$pluginInfo) {
            return parent::toString($format);
        } else {
            return $this->___callPlugins('toString', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $args)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, '__call');
        if (!$pluginInfo) {
            return parent::__call($method, $args);
        } else {
            return $this->___callPlugins('__call', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isEmpty');
        if (!$pluginInfo) {
            return parent::isEmpty();
        } else {
            return $this->___callPlugins('isEmpty', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize($keys = [], $valueSeparator = '=', $fieldSeparator = ' ', $quote = '"')
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'serialize');
        if (!$pluginInfo) {
            return parent::serialize($keys, $valueSeparator, $fieldSeparator, $quote);
        } else {
            return $this->___callPlugins('serialize', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function debug($data = null, &$objects = [])
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'debug');
        if (!$pluginInfo) {
            return parent::debug($data, $objects);
        } else {
            return $this->___callPlugins('debug', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetSet');
        if (!$pluginInfo) {
            return parent::offsetSet($offset, $value);
        } else {
            return $this->___callPlugins('offsetSet', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetExists');
        if (!$pluginInfo) {
            return parent::offsetExists($offset);
        } else {
            return $this->___callPlugins('offsetExists', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetUnset');
        if (!$pluginInfo) {
            return parent::offsetUnset($offset);
        } else {
            return $this->___callPlugins('offsetUnset', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'offsetGet');
        if (!$pluginInfo) {
            return parent::offsetGet($offset);
        } else {
            return $this->___callPlugins('offsetGet', func_get_args(), $pluginInfo);
        }
    }
}
