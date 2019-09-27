<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Model\ResourceModel\Email\Relation\Content;

use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\EmailContentInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

/**
 * Class SaveHandler
 * @package Aheadworks\Followupemail2\Model\ResourceModel\Email\Relation\Content
 * @codeCoverageIgnore
 */
class SaveHandler implements ExtensionInterface
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
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityId = (int)$entity->getId();
        $contentObjects = $entity->getContent();

        /** @var EmailContentInterface $contentObject */
        foreach ($contentObjects as $contentObject) {
            $contentObject->setEmailId($entityId);

            $connection = $this->getConnection();
            $tableName = $this->resourceConnection->getTableName('aw_fue2_event_email_content');

            $contentData = $this->dataObjectProcessor->buildOutputDataArray(
                $contentObject,
                EmailContentInterface::class
            );

            if ($contentObject->getId()) {
                $connection->update($tableName, $contentData, ['id = ?' => (int)$contentObject->getId()]);
            } else {
                $connection->insert($tableName, $contentData);
            }
        }
        return $entity;
    }

    /**
     * Get connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        return $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(EmailInterface::class)->getEntityConnectionName()
        );
    }
}
