<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Aheadworks\Followupemail2\Api\Data\EventInterface;

/**
 * Class UpgradeSchema
 * @package Aheadworks\Followupemail2\Setup
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '2.1.0', '<')) {
            $this->addConditionsTable($setup);
            $this->addPredictionColumn($setup);
            $this->addIndexes($setup);
            $this->addScheduledEmailsTables($setup);
        }
    }

    /**
     * Add conditions table
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addConditionsTable(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'aw_fue2_event_conditions'
         */
        $conditionsTable = $setup->getConnection()->newTable(
            $setup->getTable('aw_fue2_event_conditions')
        )->addColumn(
            'event_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Event Id'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Conditions Type'
        )->addColumn(
            'value',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            '2M',
            ['nullable' => false],
            'Conditions'
        )->addIndex(
            $setup->getIdxName('aw_fue2_event_conditions', ['event_id', 'type']),
            ['event_id', 'type']
        )->addForeignKey(
            $setup->getFkName('aw_fue2_event_conditions', 'event_id', 'aw_fue2_event', 'id'),
            'event_id',
            $setup->getTable('aw_fue2_event'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        );
        $setup->getConnection()->createTable($conditionsTable);

        $select = $setup->getConnection()->select()
            ->from($setup->getTable('aw_fue2_event'), ['*']);
        $events = $setup->getConnection()->fetchAll($select);

        foreach ($events as $event) {
            if (isset($event[EventInterface::CART_CONDITIONS]) && $event[EventInterface::CART_CONDITIONS] != '') {
                $this->saveCondition(
                    $setup,
                    $event[EventInterface::ID],
                    EventInterface::TYPE_CONDITIONS_CART,
                    $event[EventInterface::CART_CONDITIONS]
                );
            }
            if (isset($event[EventInterface::LIFETIME_CONDITIONS])
                && $event[EventInterface::LIFETIME_CONDITIONS] != ''
            ) {
                $this->saveCondition(
                    $setup,
                    $event[EventInterface::ID],
                    EventInterface::TYPE_CONDITIONS_LIFETIME,
                    $event[EventInterface::LIFETIME_CONDITIONS]
                );
            }
        }

        $setup->getConnection()
            ->dropColumn($setup->getTable('aw_fue2_event'), 'cart_conditions');
        $setup->getConnection()
            ->dropColumn($setup->getTable('aw_fue2_event'), 'lifetime_conditions');

        return $this;
    }

    /**
     * Save condition
     *
     * @param SchemaSetupInterface $setup
     * @param int $eventId
     * @param int $type
     * @param string $value
     */
    private function saveCondition(SchemaSetupInterface $setup, $eventId, $type, $value)
    {
        $setup->getConnection()->insert(
            $setup->getTable('aw_fue2_event_conditions'),
            [
                'event_id'  => $eventId,
                'type'      => $type,
                'value'     => $value,
            ]
        );
    }

    /**
     * Add prediction column
     *
     * @param SchemaSetupInterface $setup
     */
    private function addPredictionColumn(SchemaSetupInterface $setup)
    {
        $setup->getConnection()->addColumn(
            $setup->getTable('aw_fue2_event_email'),
            'when',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                'unsigned' => true,
                'nullable' => false,
                'default' => '0',
                'after' => 'email_send_minutes',
                'comment' => 'When',
            ]
        );
    }

    /**
     * Add additional indexes
     *
     * @param SchemaSetupInterface $setup
     */
    private function addIndexes(SchemaSetupInterface $setup)
    {
        $connection = $setup->getConnection();
        $connection->addIndex(
            $setup->getTable('aw_fue2_campaign'),
            $setup->getIdxName('aw_fue2_campaign', ['start_date']),
            ['start_date']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_campaign'),
            $setup->getIdxName('aw_fue2_campaign', ['end_date']),
            ['end_date']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_campaign'),
            $setup->getIdxName('aw_fue2_campaign', ['status']),
            ['status']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event'),
            $setup->getIdxName('aw_fue2_event', ['event_type']),
            ['event_type']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event'),
            $setup->getIdxName('aw_fue2_event', ['status']),
            ['status']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_email'),
            $setup->getIdxName('aw_fue2_event_email', ['position']),
            ['position']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_email'),
            $setup->getIdxName('aw_fue2_event_email', ['status']),
            ['status']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_history'),
            $setup->getIdxName('aw_fue2_event_history', ['reference_id']),
            ['reference_id']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_history'),
            $setup->getIdxName('aw_fue2_event_history', ['event_type']),
            ['event_type']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_history'),
            $setup->getIdxName('aw_fue2_event_history', ['processed']),
            ['processed']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_queue'),
            $setup->getIdxName('aw_fue2_event_queue', ['reference_id']),
            ['reference_id']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_queue'),
            $setup->getIdxName('aw_fue2_event_queue', ['event_type']),
            ['event_type']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_queue'),
            $setup->getIdxName('aw_fue2_event_queue', ['security_code']),
            ['security_code']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_queue'),
            $setup->getIdxName('aw_fue2_event_queue', ['status']),
            ['status']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_event_queue_email'),
            $setup->getIdxName('aw_fue2_event_queue_email', ['status']),
            ['status']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_email_queue'),
            $setup->getIdxName('aw_fue2_email_queue', ['event_type']),
            ['event_type']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_email_queue'),
            $setup->getIdxName('aw_fue2_email_queue', ['status']),
            ['status']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_email_queue'),
            $setup->getIdxName('aw_fue2_email_queue', ['scheduled_at']),
            ['scheduled_at']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_email_queue'),
            $setup->getIdxName('aw_fue2_email_queue', ['sent_at']),
            ['sent_at']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_unsubscribe'),
            $setup->getIdxName('aw_fue2_unsubscribe', ['store_id', 'email', 'type', 'value']),
            ['store_id', 'email', 'type', 'value']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_unsubscribe'),
            $setup->getIdxName('aw_fue2_unsubscribe', ['store_id', 'email', 'type']),
            ['store_id', 'email', 'type']
        );
        $connection->addIndex(
            $setup->getTable('aw_fue2_unsubscribe'),
            $setup->getIdxName('aw_fue2_unsubscribe', ['store_id', 'email']),
            ['store_id', 'email']
        );
    }

    /**
     * Add scheduled emails tables
     *
     * @param SchemaSetupInterface $setup
     * @return $this
     * @throws \Zend_Db_Exception
     */
    private function addScheduledEmailsTables(SchemaSetupInterface $setup)
    {
        /**
         * Create table 'aw_fue2_scheduled_emails'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('aw_fue2_scheduled_emails')
        )->addColumn(
            'event_queue_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Event Queue Id'
        )->addColumn(
            'campaign_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Campaign Name'
        )->addColumn(
            'event_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Event Name'
        )->addColumn(
            'event_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            ['nullable' => false],
            'Event Type'
        )->addColumn(
            'email_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email Name'
        )->addColumn(
            'ab_testing_mode',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'AB Testing Mode'
        )->addColumn(
            'recipient_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Recipient Name'
        )->addColumn(
            'recipient_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Recipient Email'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'scheduled_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Scheduled To'
        )->addIndex(
            $setup->getIdxName('aw_fue2_scheduled_emails', ['scheduled_to']),
            ['scheduled_to']
        )->addForeignKey(
            $setup->getFkName('aw_fue2_scheduled_emails', 'event_queue_id', 'aw_fue2_event_queue', 'id'),
            'event_queue_id',
            $setup->getTable('aw_fue2_event_queue'),
            'id',
            \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
        )->setComment('AW Follow Up Email 2 Scheduled Emails Indexer');
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_scheduled_emails_idx'
         */
        $table = $setup->getConnection()->newTable(
            $setup->getTable('aw_fue2_scheduled_emails_idx')
        )->addColumn(
            'event_queue_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Event Queue Id'
        )->addColumn(
            'campaign_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Campaign Name'
        )->addColumn(
            'event_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Event Name'
        )->addColumn(
            'event_type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            128,
            ['nullable' => false],
            'Event Type'
        )->addColumn(
            'email_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Email Name'
        )->addColumn(
            'ab_testing_mode',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'default' => '0'],
            'AB Testing Mode'
        )->addColumn(
            'recipient_name',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Recipient Name'
        )->addColumn(
            'recipient_email',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Recipient Email'
        )->addColumn(
            'store_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Store Id'
        )->addColumn(
            'scheduled_to',
            \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
            null,
            [],
            'Scheduled To'
        )->setComment('AW Follow Up Email 2 Scheduled Emails Indexer Idx');
        $setup->getConnection()->createTable($table);
        return $this;
    }
}
