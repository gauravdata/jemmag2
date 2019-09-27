<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
See LICENSE.txt for license details.
 */

namespace Aheadworks\Layerednav\Setup\Updater;

use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Layerednav\Model\ResourceModel\Filter as FilterResourceModel;
use Aheadworks\Layerednav\Model\ResourceModel\Image as ImageResourceModel;
use Aheadworks\Layerednav\Model\ResourceModel\Filter\Swatch as FilterSwatchResourceModel;
use Aheadworks\Layerednav\Model\Source\Filter\SwatchesMode as FilterSwatchesModeSource;

/**
 * Class Schema
 *
 * @package Aheadworks\Layerednav\Setup\Updater
 */
class Schema
{
    /**
     * Update to 1.7.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function update170(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'aw_layerednav_filter'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter'))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'default_title',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Default Title'
            )
            ->addColumn(
                'code',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Code'
            )
            ->addColumn(
                'type',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Type'
            )
            ->addColumn(
                'is_filterable',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Filterable'
            )
            ->addColumn(
                'is_filterable_in_search',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Filterable In Search'
            )
            ->addColumn(
                'position',
                Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => '0'],
                'Position'
            )
            ->addColumn(
                'category_mode',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Display On Category Mode'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['code']),
                ['code']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['type']),
                ['type']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['is_filterable']),
                ['is_filterable']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['is_filterable_in_search']),
                ['is_filterable_in_search']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter', ['position']),
                ['position']
            )
            ->setComment('AW Layered Navigation Filter Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_title'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_title'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_title', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_title', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_title', ['value']),
                ['value']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_title', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_title', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Title Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_display_state'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_display_state'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false,],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_display_state', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_display_state', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_display_state', ['value']),
                ['value']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_display_state', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_display_state', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Display State Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_sort_order'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_sort_order'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_sort_order', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_sort_order', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_sort_order', ['value']),
                ['value']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_sort_order', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_sort_order', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Sort Order Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_exclude_category'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_exclude_category'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'category_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Category Id'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_exclude_category', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_exclude_category', ['category_id']),
                ['category_id']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_exclude_category', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName(
                    'aw_layerednav_filter_exclude_category',
                    'category_id',
                    'catalog_category_entity',
                    'entity_id'
                ),
                'category_id',
                $setup->getTable('catalog_category_entity'),
                'entity_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Exclude Category Table');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_layerednav_filter_category'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('aw_layerednav_filter_category'))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'param_name',
                Table::TYPE_TEXT,
                255,
                ['unsigned' => true, 'nullable' => false,],
                'Param Name'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Value'
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_category', ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_category', ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_category', ['param_name']),
                ['value']
            )
            ->addIndex(
                $setup->getIdxName('aw_layerednav_filter_category', ['value']),
                ['value']
            )
            ->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_category', 'filter_id', 'aw_layerednav_filter', 'id'),
                'filter_id',
                $setup->getTable('aw_layerednav_filter'),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $setup->getFkName('aw_layerednav_filter_category', 'store_id', 'store', 'store_id'),
                'store_id',
                $setup->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Category Table');
        $setup->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Update to 2.0.0 version
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    public function update200(SchemaSetupInterface $setup)
    {
        $this->addFilterModeTable($setup);
        $this->addImageTable($setup);
        $this->addFilterImageTable($setup);
        $this->addFilterImageTitleTable($setup);
        $this->addSwatchesColumnsToFilterTable($setup);
        $this->addFilterSwatchTable($setup);
        $this->addFilterSwatchImageTable($setup);
        $this->addFilterSwatchTitleTable($setup);

        return $this;
    }

    /**
     * Add filter mode table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addFilterModeTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(FilterResourceModel::FILTER_MODE_TABLE_NAME))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                ['nullable' => false,],
                'Value'
            )
            ->addIndex(
                $installer->getIdxName(FilterResourceModel::FILTER_MODE_TABLE_NAME, ['filter_id']),
                ['filter_id']
            )
            ->addIndex(
                $installer->getIdxName(FilterResourceModel::FILTER_MODE_TABLE_NAME, ['store_id']),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName(FilterResourceModel::FILTER_MODE_TABLE_NAME, ['value']),
                ['value']
            )
            ->addForeignKey(
                $installer->getFkName(
                    FilterResourceModel::FILTER_MODE_TABLE_NAME,
                    'filter_id',
                    FilterResourceModel::FILTER_TABLE_NAME,
                    'id'
                ),
                'filter_id',
                $installer->getTable(FilterResourceModel::FILTER_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(FilterResourceModel::FILTER_MODE_TABLE_NAME, 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Mode Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add image table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addImageTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(ImageResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Image Id'
            )->addColumn(
                'name',
                Table::TYPE_TEXT,
                Table::DEFAULT_TEXT_SIZE,
                [
                    'nullable' => false
                ],
                'Name'
            )->addColumn(
                'file_name',
                Table::TYPE_TEXT,
                Table::DEFAULT_TEXT_SIZE,
                [
                    'nullable' => false
                ],
                'File Name On The Server'
            )->setComment('AW Layered Navigation Image Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add filter image table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addFilterImageTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(FilterResourceModel::FILTER_IMAGE_TABLE_NAME))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Filter Id'
            )->addColumn(
                'image_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Image Id'
            )->addForeignKey(
                $installer->getFkName(
                    FilterResourceModel::FILTER_IMAGE_TABLE_NAME,
                    'filter_id',
                    FilterResourceModel::FILTER_TABLE_NAME,
                    'id'
                ),
                'filter_id',
                $installer->getTable(FilterResourceModel::FILTER_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    FilterResourceModel::FILTER_IMAGE_TABLE_NAME,
                    'image_id',
                    ImageResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'image_id',
                $installer->getTable(ImageResourceModel::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->setComment('AW Layered Navigation Filter Image Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add filter image title table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addFilterImageTitleTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(FilterResourceModel::FILTER_IMAGE_TITLE_TABLE_NAME))
            ->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Filter Id'
            )
            ->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Store ID'
            )
            ->addColumn(
                'value',
                Table::TYPE_TEXT,
                255,
                [
                    'nullable' => false,
                ],
                'Value'
            )
            ->addIndex(
                $installer->getIdxName(
                    FilterResourceModel::FILTER_IMAGE_TITLE_TABLE_NAME,
                    ['filter_id']
                ),
                ['filter_id']
            )
            ->addIndex(
                $installer->getIdxName(
                    FilterResourceModel::FILTER_IMAGE_TITLE_TABLE_NAME,
                    ['store_id']
                ),
                ['store_id']
            )
            ->addIndex(
                $installer->getIdxName(
                    FilterResourceModel::FILTER_IMAGE_TITLE_TABLE_NAME,
                    ['value']
                ),
                ['value']
            )
            ->addForeignKey(
                $installer->getFkName(
                    FilterResourceModel::FILTER_IMAGE_TITLE_TABLE_NAME,
                    'filter_id',
                    FilterResourceModel::FILTER_TABLE_NAME,
                    'id'
                ),
                'filter_id',
                $installer->getTable(FilterResourceModel::FILTER_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    FilterResourceModel::FILTER_IMAGE_TITLE_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )
            ->setComment('AW Layered Navigation Filter Image Title Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add swatches columns to the filter table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     */
    private function addSwatchesColumnsToFilterTable(SchemaSetupInterface $installer)
    {
        $tableName = $installer->getTable(FilterResourceModel::FILTER_TABLE_NAME);
        $connection = $installer->getConnection();

        if ($connection->isTableExists($tableName)) {
            $connection->addColumn(
                $tableName,
                'swatches_view_mode',
                [
                    'type' => Table::TYPE_SMALLINT,
                    'nullable' => true,
                    'comment' => 'Swatches view mode',
                    'default' => FilterSwatchesModeSource::TITLE_ONLY,
                ]
            );
        }

        return $this;
    }

    /**
     * Add filter swatch table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addFilterSwatchTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(FilterSwatchResourceModel::MAIN_TABLE_NAME))
            ->addColumn(
                'id',
                Table::TYPE_INTEGER,
                null,
                [
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true
                ],
                'Swatch Id'
            )->addColumn(
                'filter_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                ],
                'Filter Id'
            )->addColumn(
                'is_default',
                Table::TYPE_BOOLEAN,
                null,
                [
                    'nullable' => false,
                    'default' => '0',
                    'unsigned' => true,
                ],
                'Is Default'
            )->addColumn(
                'sort_order',
                Table::TYPE_SMALLINT,
                null,
                [
                    'nullable' => false
                ],
                'Sort Order'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                128,
                [
                    'nullable' => true
                ],
                'Value'
            )->addColumn(
                'option_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => true,
                    'unsigned' => true,
                ],
                'Option id'
            )->addForeignKey(
                $installer->getFkName(
                    FilterSwatchResourceModel::MAIN_TABLE_NAME,
                    'filter_id',
                    FilterResourceModel::FILTER_TABLE_NAME,
                    'id'
                ),
                'filter_id',
                $installer->getTable(FilterResourceModel::FILTER_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    FilterSwatchResourceModel::MAIN_TABLE_NAME,
                    'option_id',
                    'eav_attribute_option',
                    'option_id'
                ),
                'option_id',
                $installer->getTable('eav_attribute_option'),
                'option_id',
                Table::ACTION_SET_NULL
            )->setComment('AW Layered Navigation Filter Swatch Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add filter swatch image table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addFilterSwatchImageTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(FilterSwatchResourceModel::SWATCH_IMAGE_TABLE_NAME))
            ->addColumn(
                'swatch_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Swatch Id'
            )->addColumn(
                'image_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Image Id'
            )->addForeignKey(
                $installer->getFkName(
                    FilterSwatchResourceModel::SWATCH_IMAGE_TABLE_NAME,
                    'swatch_id',
                    FilterSwatchResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'swatch_id',
                $installer->getTable(FilterSwatchResourceModel::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    FilterSwatchResourceModel::SWATCH_IMAGE_TABLE_NAME,
                    'image_id',
                    ImageResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'image_id',
                $installer->getTable(ImageResourceModel::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->setComment('AW Layered Navigation Filter Swatch Image Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }

    /**
     * Add filter swatch title table
     *
     * @param SchemaSetupInterface $installer
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addFilterSwatchTitleTable(SchemaSetupInterface $installer)
    {
        $table = $installer->getConnection()
            ->newTable($installer->getTable(FilterSwatchResourceModel::SWATCH_TITLE_TABLE_NAME))
            ->addColumn(
                'swatch_id',
                Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Swatch Id'
            )->addColumn(
                'store_id',
                Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                ],
                'Store ID'
            )->addColumn(
                'value',
                Table::TYPE_TEXT,
                null,
                [
                    'nullable' => true
                ],
                'Value'
            )->addForeignKey(
                $installer->getFkName(
                    FilterSwatchResourceModel::SWATCH_TITLE_TABLE_NAME,
                    'swatch_id',
                    FilterSwatchResourceModel::MAIN_TABLE_NAME,
                    'id'
                ),
                'swatch_id',
                $installer->getTable(FilterSwatchResourceModel::MAIN_TABLE_NAME),
                'id',
                Table::ACTION_CASCADE
            )->addForeignKey(
                $installer->getFkName(
                    FilterSwatchResourceModel::SWATCH_TITLE_TABLE_NAME,
                    'store_id',
                    'store',
                    'store_id'
                ),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                Table::ACTION_CASCADE
            )->setComment('AW Layered Navigation Filter Swatch Title Table');
        $installer->getConnection()->createTable($table);

        return $this;
    }
}
