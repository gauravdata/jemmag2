<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.0.53
 * @copyright Copyright (C) 2019 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\Rma\Service\Message\MessageManagement;

use \Mirasvit\Rma\Api\Data\MessageInterface;
use \Mirasvit\Rma\Service\Performer\UserStrategy;

/**
 *  We put here only methods directly connected with Message properties
 */
class Add implements \Mirasvit\Rma\Api\Service\Message\MessageManagement\AddInterface
{
    public function __construct(
        \Mirasvit\Rma\Api\Repository\RmaRepositoryInterface $rmaRepository,
        \Mirasvit\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Mirasvit\Rma\Api\Service\Attachment\AttachmentManagementInterface $attachmentManagement,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Email\Model\Template\FilterFactory $filterFactory,
        \Mirasvit\Rma\Helper\Mail $rmaMail,
        \Magento\Framework\UrlInterface $urlModel
    ) {
        $this->rmaRepository         = $rmaRepository;
        $this->messageRepository     = $messageRepository;
        $this->attachmentManagement  = $attachmentManagement;
        $this->eventManager          = $eventManager;
        $this->filterFactory         = $filterFactory;
        $this->rmaMail               = $rmaMail;
        $this->urlModel              = $urlModel;
    }

    /**
     * {@inheritdoc}
     */
    public function addMessage(
        \Mirasvit\Rma\Api\Service\Performer\PerformerInterface $performer,
        \Mirasvit\Rma\Api\Data\RmaInterface $rma,
        $messageText,
        $params = []
    ) {
        if (!$messageText && ! $this->attachmentManagement->hasAttachments()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please, enter a message'));
        }

        /** @var MessageInterface $message */
        $message = $this->messageRepository->create();
        $message
            ->setRmaId($rma->getId())
            ->setText($this->processVariables($messageText, $rma), false);

        $performer->setMessageAttributesBeforeAdd($message, $params);
        $this->messageRepository->save($message);

        $rma->setLastReplyName($performer->getName())
            ->setIsAdminRead($performer instanceof UserStrategy);

        $this->rmaRepository->save($rma);

        $this->eventManager->dispatch(
            'rma_add_message_after',
            ['rma'=> $rma, 'message' => $message, 'performer' => $performer, 'params' => $params]
        );
        return $message;
    }

    /**
     * Translates transactional variables in message
     *
     * @param string $text
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return string
     */
    protected function processVariables($text, $rma)
    {
        $templateFilter = $this->filterFactory->create();
        $templateFilter->setUseAbsoluteLinks(true)
            ->setStoreId($rma->getStoreId())
            ->setUrlModel($this->urlModel)
            ->setPlainTemplateMode(true)
            ->setVariables($this->rmaMail->getEmailVariables($rma))
        ;

        return $templateFilter->filter($text);
    }
}