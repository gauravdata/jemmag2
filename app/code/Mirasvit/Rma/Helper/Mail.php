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



namespace Mirasvit\Rma\Helper;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mail extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $rmaAdapter;
    private $emailTemplateFactory;
    private $transportBuilder;
    private $rmaManagement;
    private $rmaSearchManagement;
    private $messageManagement;
    private $rmaUrlHelper;
    private $notificationConfig;
    private $helpdeskConfig;
    private $storeManager;
    private $storeInfo;
    private $context;
    private $inlineTranslation;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Mirasvit\Rma\Service\Rma\RmaAdapter $rmaAdapter,
        \Mirasvit\Rma\Model\Mail\Template\TransportBuilder $transportBuilder,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SearchInterface $rmaSearchManagement,
        \Mirasvit\Rma\Api\Service\Message\MessageManagementInterface $messageManagement,
        \Mirasvit\Rma\Helper\Rma\Url $rmaUrlHelper,
        \Mirasvit\Rma\Api\Config\NotificationConfigInterface $notificationConfig,
        \Mirasvit\Rma\Api\Config\HelpdeskConfigInterface $helpdeskConfig,
        \Magento\Email\Model\TemplateFactory $emailTemplateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Store\Model\Information $storeInfo,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
    ) {
        $this->rmaAdapter           = $rmaAdapter;
        $this->emailTemplateFactory = $emailTemplateFactory;
        $this->transportBuilder     = $transportBuilder;
        $this->rmaManagement        = $rmaManagement;
        $this->rmaSearchManagement  = $rmaSearchManagement;
        $this->messageManagement    = $messageManagement;
        $this->rmaUrlHelper         = $rmaUrlHelper;
        $this->notificationConfig   = $notificationConfig;
        $this->helpdeskConfig       = $helpdeskConfig;
        $this->storeManager         = $storeManager;
        $this->storeInfo            = $storeInfo;
        $this->context              = $context;
        $this->inlineTranslation    = $inlineTranslation;

        parent::__construct($context);
    }

    /**
     * @var array
     */
    public $emails = [];

    /**
     * @return \Mirasvit\Rma\Api\Config\NotificationConfigInterface
     */
    protected function getNotificationConfig()
    {
        return $this->notificationConfig;
    }

    /**
     * @return string
     */
    protected function getSender()
    {
        return $this->getNotificationConfig()->getSenderEmail();
    }

    /**
     * @param string $templateName
     * @param string $senderName
     * @param string $senderEmail
     * @param string $recipientEmail
     * @param string $recipientName
     * @param array  $variables
     * @param int    $storeId
     * @param string $code
     * @param array  $attachments
     *
     * @return bool
     */
    protected function send(
        $templateName,
        $senderName,
        $senderEmail,
        $recipientEmail,
        $recipientName,
        $variables,
        $storeId,
        $code,
        $attachments
    ) {
        if (!$senderEmail || !$recipientEmail || $templateName == 'none') {
            return false;
        }
        $this->plainSend(
            $templateName,
            $senderName,
            $senderEmail,
            $recipientEmail,
            $recipientName,
            $variables,
            $storeId,
            $code,
            $attachments
        );

        // Add blind carbon copy of all emails if such exists
        $bcc = $this->getNotificationConfig()->getSendEmailBcc();
        if ($bcc != "") {
            $bcc = explode(',', $bcc);
            // we sent it as separate emails, because if customer uses 3rd party modules, they may not support bcc correctly
            foreach ($bcc as $email) {
                $email = trim($email);
                $this->plainSend(
                    $templateName,
                    $senderName,
                    $senderEmail,
                    $email,
                    $recipientName,
                    $variables,
                    $storeId,
                    $code,
                    $attachments
                );
            }
        }
        return true;
    }

    /**
     * @param string $templateName
     * @param string $senderName
     * @param string $senderEmail
     * @param string $recipientEmail
     * @param string $recipientName
     * @param array  $variables
     * @param int    $storeId
     * @param string $code
     * @param array  $attachments
     *
     * @return bool
     */
    protected function plainSend(
        $templateName,
        $senderName,
        $senderEmail,
        $recipientEmail,
        $recipientName,
        $variables,
        $storeId,
        $code,
        $attachments
    ) {
        /** @var \Mirasvit\Rma\Api\Data\AttachmentInterface $attachment */
        foreach ($attachments as $attachment) {
            $this->transportBuilder->addAttachment(
                $attachment->getBody(),
                $attachment->getType(),
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                $attachment->getName()
            );
        }

        $hiddenCode = $hiddenSeparator = '';
        $isActiveHelpdesk = $this->helpdeskConfig->isHelpdeskActive();
        if ($isActiveHelpdesk) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Mirasvit\Helpdesk\Helper\Email $mailHelper */
            $mailHelper = $objectManager->get('\Mirasvit\Helpdesk\Helper\Email');

            $hiddenCode      = $mailHelper->getHiddenCode($code);
            $hiddenSeparator = $mailHelper->getHiddenSeparator();
        }

        $variables = array_merge($variables, [
            'hidden_separator' => $hiddenSeparator,
            'hidden_code'      => $hiddenCode,
        ]);

        $this->inlineTranslation->suspend();
        $this->transportBuilder
            ->setTemplateIdentifier($templateName)
            ->setTemplateOptions(
                [
                    'area'  => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $storeId ? $storeId : $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($variables);

        try {
            $this->transportBuilder
                ->setFrom(
                    [
                        'name'  => $senderName,
                        'email' => $senderEmail,
                    ]
                )
                ->addTo($recipientEmail, $recipientName)
                ->setReplyTo($senderEmail);

            $transport = $this->transportBuilder->getTransport();

            /* @var \Magento\Framework\Mail\Transport $transport */
            $transport->sendMessage();
        } catch (\Exception $e) {
            return false;
        }

        $this->inlineTranslation->resume();
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface            $rma
     * @param \Mirasvit\Rma\Api\Data\MessageInterface|string $message
     * @param boolean                                        $isAllowParseVariables
     * @return void
     */
    public function sendNotificationCustomerEmail($rma, $message, $isAllowParseVariables = false)
    {
        $customer = $this->rmaManagement->getCustomer($rma);
        if (empty($rma->getEmail()) && empty($customer->getEmail())) {
            return;
        }
        $attachments = [];
        if (is_object($message)) {
            $attachments = $this->messageManagement->getAttachments($message);
            $message     = $this->messageManagement->getTextHtml($message);
        }
        if ($isAllowParseVariables) {
            $message = $this->parseVariables($message, $rma);
        }
        $storeId = $rma->getStoreId();
        $templateName = $this->getNotificationConfig()->getCustomerEmailTemplate($storeId);

        $recipientEmail = $rma->getEmail() ? $rma->getEmail() : $customer->getEmail();
        $recipientName  = $this->rmaManagement->getFullName($rma);
        $variables = $this->getEmailVariables($rma);
        $message = $this->processVariable($message, $variables, $storeId);
        $variables['message'] = $message;

        $senderName = $this->context->getScopeConfig()->getValue(
            "trans_email/ident_{$this->getSender()}/name",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $senderEmail = $this->context->getScopeConfig()->getValue(
            "trans_email/ident_{$this->getSender()}/email",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $this->send(
            $templateName,
            $senderName,
            $senderEmail,
            $recipientEmail,
            $recipientName,
            $variables,
            $storeId,
            $rma->getCode(),
            $attachments
        );
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface            $rma
     * @param \Mirasvit\Rma\Api\Data\MessageInterface|string $message
     * @param boolean                                        $isAllowParseVariables
     * @return void
     */
    public function sendNotificationAdminEmail($rma, $message, $isAllowParseVariables = false)
    {
        if ($isAllowParseVariables) {
            $message = $this->parseVariables($message, $rma);
        }

        $attachments = [];
        if (is_object($message)) {
            $attachments = $this->messageManagement->getAttachments($message);
            $message     = $this->messageManagement->getTextHtml($message);
        }
        $storeId = $rma->getStoreId();
        $templateName = $this->getNotificationConfig()->getAdminEmailTemplate($storeId);
        if ($user = $this->rmaManagement->getUser($rma)) {
            $recipientEmail = $user->getEmail();
        } else {
            return;
        }

        $recipientName = '';

        $variables = $this->getEmailVariables($rma);
        $message = $this->processVariable($message, $variables, $storeId);
        $variables['message'] = $message;

        $senderName = $this->context->getScopeConfig()->getValue(
            "trans_email/ident_{$this->getSender()}/name",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $senderEmail = $this->context->getScopeConfig()->getValue(
            "trans_email/ident_{$this->getSender()}/email",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $this->send(
            $templateName,
            $senderName,
            $senderEmail,
            $recipientEmail,
            $recipientName,
            $variables,
            $storeId,
            $this->rmaManagement->getCode($rma),
            $attachments
        );
    }

    /**
     * @param string                              $recipientEmail
     * @param string                              $recipientName
     * @param \Mirasvit\Rma\Model\Rule            $rule
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return void
     */
    public function sendNotificationRule($recipientEmail, $recipientName, $rule, $rma)
    {
        $attachments = [];

        $text = '';
        if ($message = $this->rmaSearchManagement->getLastMessage($rma)) {
            if ($rule->getIsSendAttachment()) {
                $attachments = $this->messageManagement->getAttachments($message);
            }

            $text = $this->messageManagement->getTextHtml($message);
        }

        $storeId = $rma->getStoreId();
        $templateName = $this->getNotificationConfig()->getRuleTemplate($rma->getStoreId());

        $variables = $this->getEmailVariables($rma);
        $variables['email_subject'] = $this->processVariable($rule->getEmailSubject(), $variables, $storeId);
        $variables['email_body'] = $this->processVariable($rule->getEmailBody(), $variables, $storeId);
        $text = $this->processVariable($text, $variables, $storeId);
        $variables['message'] = $text;
        $senderName = $this->context->getScopeConfig()->getValue(
            "trans_email/ident_{$this->getSender()}/name",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $senderEmail = $this->context->getScopeConfig()->getValue(
            "trans_email/ident_{$this->getSender()}/email",
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $this->send(
            $templateName,
            $senderName,
            $senderEmail,
            $recipientEmail,
            $recipientName,
            $variables,
            $storeId,
            $this->rmaManagement->getCode($rma),
            $attachments
        );
    }

    /**
     * Creates additional transactional variables for messages and templates
     *
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     *
     * @return array
     */
    public function getEmailVariables($rma)
    {
        $this->rmaAdapter->setData($rma->getData());
        $this->rmaAdapter->setData('url', $this->rmaUrlHelper->getGuestUrl($rma));
        $this->rmaAdapter->setData('backend_url', $this->rmaUrlHelper->getBackendUrl($rma));
        $store = $this->storeManager->getStore($rma->getStoreId());
        $user = $this->rmaManagement->getUser($rma);
        $status = $this->rmaManagement->getStatus($rma);

        return [
            'rma'       => $this->rmaAdapter,
            'store'     => $store,
            'storeInfo' => $this->storeInfo->getStoreInformationObject($store),
            'user'      => $user,
//            'order'     => $this->rmaManagement->getOrder($rma),
            'status'    => $status,
            'customer'  => $this->rmaManagement->getCustomer($rma),
            'rma_user_name'         => $user->getName(),
            'rma_status'            => $status->getName(),
            'rma_createdAtFormated' => $this->rmaManagement->getCreatedAtFormated($rma),
            'rma_updatedAtFormated' => $this->rmaManagement->getUpdatedAtFormated($rma),
            'rmaUrl'   => $this->rmaAdapter->getUrl(),
        ];
    }

    /**
     * Can parse template and return ready text.
     *
     * @param string $text  Text with variables like {{var customer.name}}.
     * @param array  $variables Array of variables.
     * @param int    $storeId
     *
     * @return string - ready text
     */
    protected function processVariable($text, $variables, $storeId)
    {
        $template = $this->emailTemplateFactory->create();
        $template->setDesignConfig([
            'area'  => 'frontend',
            'store' => $storeId,
        ]);
        $template->setTemplateText($text);
        $html = $template->getProcessedTemplate($variables);

        return $html;
    }

    /**
     * @param string                              $text
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     *
     * @return string
     */
    public function parseVariables($text, $rma)
    {
        //$this->storeManager->setCurrentStore($rma->getStoreId()); @todo check this for emails

        $text = $this->processVariable($text, $this->getEmailVariables($rma), $rma->getStoreId());

        return $text;
    }
}
