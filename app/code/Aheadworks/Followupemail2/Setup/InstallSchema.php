<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class InstallSchema
 * @package Aheadworks\Followupemail2\Setup
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        /**
         * Create table 'aw_fue2_campaign'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_campaign'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Campaign Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                [],
                'Description'
            )
            ->addColumn(
                'start_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'Start Date'
            )
            ->addColumn(
                'end_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => true],
                'End Date'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Status'
            )
            ->setComment('AW Follow Up Email 2 Campaign');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_event'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_event'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Event Id'
            )
            ->addColumn(
                'campaign_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Campaign Id'
            )
            ->addColumn(
                'event_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Event Type'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'bcc_emails',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => true],
                'BCC Email(s)'
            )
            ->addColumn(
                'newsletter_only',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Send to Newsletter Subscribers Only'
            )
            ->addColumn(
                'failed_emails_mode',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Failed Emails Mode'
            )
            ->addColumn(
                'product_type_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Product Type IDs'
            )
            ->addColumn(
                'cart_conditions',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Cart Conditions'
            )
            ->addColumn(
                'lifetime_conditions',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Lifetime Conditions'
            )
            ->addColumn(
                'customer_groups',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Customer Groups'
            )
            ->addColumn(
                'order_statuses',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Order Statuses'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '1'],
                'Status'
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_event', 'campaign_id', 'aw_fue2_campaign', 'id'),
                'campaign_id',
                $installer->getTable('aw_fue2_campaign'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Email 2 Event');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_event_store'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_event_store'))
            ->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Event ID'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Store ID'
            )
            ->addIndex(
                $installer->getIdxName('aw_fue2_event_store', ['store_id']),
                ['store_id']
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_event_store', 'event_id', 'aw_fue2_event', 'id'),
                'event_id',
                $installer->getTable('aw_fue2_event'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_event_store', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment(
                'AW Follow Up Email 2 Event To Store Linkage Table'
            );
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_event_email'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_event_email'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Mail Id'
            )
            ->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Event Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Name'
            )
            ->addColumn(
                'email_send_days',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Email Send Days'
            )
            ->addColumn(
                'email_send_hours',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Email Send Hours'
            )
            ->addColumn(
                'email_send_minutes',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Email Send Minutes'
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Position'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addColumn(
                'ab_testing_mode',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'AB Testing Mode'
            )
            ->addColumn(
                'primary_email_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Primary Email Content'
            )
            ->addColumn(
                'ab_email_content',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'AB Email Content'
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_event_email', 'event_id', 'aw_fue2_event', 'id'),
                'event_id',
                $installer->getTable('aw_fue2_event'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Email 2 Event Email');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_event_email_content'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_event_email_content'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Content Id'
            )
            ->addColumn(
                'email_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Event Mail Id'
            )
            ->addColumn(
                'sender_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Sender Name'
            )
            ->addColumn(
                'sender_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Sender Email'
            )
            ->addColumn(
                'subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Subject'
            )
            ->addColumn(
                'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '64k',
                ['nullable' => false],
                'Content'
            )
            ->addColumn(
                'header_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Header Template'
            )
            ->addColumn(
                'footer_template',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Footer Template'
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_event_email_content', 'email_id', 'aw_fue2_event_email', 'id'),
                'email_id',
                $installer->getTable('aw_fue2_event_email'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Email 2 Event Mail Content');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_event_history'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_event_history'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Event History Id'
            )
            ->addColumn(
                'reference_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Reference Id'
            )
            ->addColumn(
                'event_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Event Type'
            )
            ->addColumn(
                'event_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Event Data'
            )
            ->addColumn(
                'triggered_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Triggered At'
            )
            ->addColumn(
                'processed',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Is Processed'
            )
            ->setComment('AW Follow Up Email 2 Event History');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_event_queue'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_event_queue'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Event Queue Id'
            )
            ->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Event Id'
            )
            ->addColumn(
                'event_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Event Type'
            )
            ->addColumn(
                'reference_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Reference Id'
            )
            ->addColumn(
                'event_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Event Data'
            )
            ->addColumn(
                'security_code',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Security Code'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_event_queue', 'event_id', 'aw_fue2_event', 'id'),
                'event_id',
                $installer->getTable('aw_fue2_event'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Email 2 Event Queue');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_event_queue_email'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_event_queue_email'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'event_queue_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Event Queue Id'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Status'
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_event_queue_email', 'event_queue_id', 'aw_fue2_event_queue', 'id'),
                'event_queue_id',
                $installer->getTable('aw_fue2_event_queue'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Email 2 Event Queue Email');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_email_queue'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_email_queue'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Mail Queue Id'
            )
            ->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Event Id'
            )
            ->addColumn(
                'event_email_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Event Email Id'
            )
            ->addColumn(
                'email_content_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Email Content Id'
            )
            ->addColumn(
                'event_queue_email_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Event Queue Email Id'
            )
            ->addColumn(
                'event_type',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Event Type'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Status'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'scheduled_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false],
                'Scheduled At'
            )
            ->addColumn(
                'sent_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Sent At'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'sender_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Sender Name'
            )
            ->addColumn(
                'sender_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Sender Email'
            )
            ->addColumn(
                'recipient_name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Recipient Name'
            )
            ->addColumn(
                'recipient_email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Recipient Email'
            )
            ->addColumn(
                'subject',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '255',
                [],
                'Subject'
            )
            ->addColumn(
                'content',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '1M',
                [],
                'Content'
            )
            ->addColumn(
                'content_version',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => true],
                'Content Version'
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_email_queue', 'event_id', 'aw_fue2_event', 'id'),
                'event_id',
                $installer->getTable('aw_fue2_event'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_email_queue', 'event_email_id', 'aw_fue2_event_email', 'id'),
                'event_email_id',
                $installer->getTable('aw_fue2_event_email'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Email 2 Email Queue');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_order_quote'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_order_quote'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Order Id'
            )
            ->addColumn(
                'quote_data',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                '2M',
                ['nullable' => false],
                'Quot Data'
            )
            ->addIndex(
                $installer->getIdxName('aw_fue2_order_quote', ['order_id']),
                ['order_id']
            )
            ->setComment('AW Follow Up Email 2 Order Quote');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_statistics_history'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_statistics_history'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email'
            )
            ->addColumn(
                'email_content_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Email Content Id'
            )
            ->addColumn(
                'sent',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Sent'
            )
            ->addColumn(
                'opened',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Opened'
            )
            ->addColumn(
                'clicked',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Clicked'
            )
            ->addColumn(
                'created_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Created At'
            )
            ->addColumn(
                'updated_at',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE],
                'Updated At'
            )
            ->addForeignKey(
                $installer->getFkName(
                    'aw_fue2_statistics_history',
                    'email_content_id',
                    'aw_fue2_event_email_content',
                    'id'
                ),
                'email_content_id',
                $installer->getTable('aw_fue2_event_email_content'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Email 2 Statistics History');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_statistics'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_statistics'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email'
            )
            ->addColumn(
                'event_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Event Id'
            )
            ->addColumn(
                'event_email_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Event Email Id'
            )
            ->addColumn(
                'email_content_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Email Content Id'
            )
            ->addColumn(
                'sent',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Sent'
            )
            ->addColumn(
                'opened',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Opened'
            )
            ->addColumn(
                'clicked',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false, 'default' => '0'],
                'Clicked'
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_statistics', 'event_id', 'aw_fue2_event', 'id'),
                'event_id',
                $installer->getTable('aw_fue2_event'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_statistics', 'event_email_id', 'aw_fue2_event_email', 'id'),
                'event_email_id',
                $installer->getTable('aw_fue2_event_email'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_statistics', 'email_content_id', 'aw_fue2_event_email_content', 'id'),
                'email_content_id',
                $installer->getTable('aw_fue2_event_email_content'),
                'id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Email 2 Statistics');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_unsubscribe'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_unsubscribe'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Store Id'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Email'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Type'
            )
            ->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => true],
                'Value'
            )
            ->addForeignKey(
                $installer->getFkName('aw_fue2_unsubscribe', 'store_id', 'store', 'store_id'),
                'store_id',
                $installer->getTable('store'),
                'store_id',
                \Magento\Framework\DB\Ddl\Table::ACTION_CASCADE
            )
            ->setComment('AW Follow Up Unsubscribe');
        $installer->getConnection()->createTable($table);

        /**
         * Create table 'aw_fue2_config'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('aw_fue2_config'))
            ->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Parameter Id'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                128,
                ['nullable' => false],
                'Parameter Name'
            )
            ->addColumn(
                'value',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false],
                'Value'
            )
            ->setComment('AW Follow Up Email 2 Config');
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
