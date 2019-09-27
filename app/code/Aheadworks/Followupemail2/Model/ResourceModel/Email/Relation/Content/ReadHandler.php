<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Model\ResourceModel\Email\Relation\Content;

use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\EmailContentInterface;
use Aheadworks\Followupemail2\Api\Data\EmailContentInterfaceFactory;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Api\DataObjectHelper;

/**
 * Class ReadHandler
 * @package Aheadworks\Followupemail2\Model\ResourceModel\Email\Relation\Content
 * @codeCoverageIgnore
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var EmailContentInterfaceFactory
     */
    private $emailContentFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param EmailContentInterfaceFactory $emailContentFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        EmailContentInterfaceFactory $emailContentFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->emailContentFactory = $emailContentFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId()) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(EmailInterface::class)->getEntityConnectionName()
            );
            $select = $connection->select()
                ->from($this->resourceConnection->getTableName('aw_fue2_event_email_content'), ['*'])
                ->where('email_id = :id')
                ->order('id ASC');
            $allContentData = $connection->fetchAll($select, ['id' => $entityId]);

            $contentDataObjects = [];
            foreach ($allContentData as $contentData) {
                /** @var EmailContentInterface $emailContent */
                $emailContent = $this->emailContentFactory->create();
                $this->dataObjectHelper->populateWithArray(
                    $emailContent,
                    $contentData,
                    EmailContentInterface::class
                );
                $contentDataObjects[] = $emailContent;
            }
            $entity->setContent($contentDataObjects);
        }
        return $entity;
    }
}
