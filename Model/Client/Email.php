<?php
namespace MageSuite\ErpConnector\Model\Client;

class Email extends \Magento\Framework\DataObject implements ClientInterface
{
    /**
     * @var \MageSuite\ErpConnector\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilderFactory
     */
    protected $transportBuilderFactory;

    /**
     * @var \MageSuite\ErpConnector\Model\Command\LogErrorMessage
     */
    protected $logErrorMessage;

    public function __construct(
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilderFactory $transportBuilderFactory,
        \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        array $data = []
    ) {
        parent::__construct($data);

        $this->configuration = $configuration;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilderFactory = $transportBuilderFactory;
        $this->logErrorMessage = $logErrorMessage;
    }

    public function sendItem($provider, $data)
    {
        if (!isset($data['files']) || empty($data['files'])) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing files',
                $data
            );
            return $this;
        }

        if (!isset($data['order']) || empty($data['order'])) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing order data',
                $data
            );
            return $this;
        }

        $sender = $this->configuration->getEmailSenderInfo();
        $recipients = explode(',', $this->getData('email'));

        foreach ($recipients as $recipient) {
            try {
                $this->sendItemToRecipient($provider, $sender, $recipient, $data);
            } catch (\Exception $e) {
                $this->logErrorMessage->execute(
                    sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                    $e->getMessage(),
                    $data
                );
            }
        }

        return $this;
    }

    protected function sendItemToRecipient($provider, $sender, $data, $recipient) //phpcs:ignore
    {
        $emailTemplateVariables = $this->getEmailTemplateVariables($provider, $data);

        try {
            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilderFactory
                ->create()
                ->setTemplateIdentifier($this->getData('template'))
                ->setTemplateOptions(['area' => 'frontend', 'store' => $data['order']->getStoreId()])
                ->setTemplateVars($emailTemplateVariables)
                ->setFromByScope($sender)
                ->addTo($recipient);

            foreach ($data['files'] as $fileName => $content) {
                $transport->addAttachmentFromContent($content, $fileName, \Zend_Mime::TYPE_OCTETSTREAM);
            }

            $transport->getTransport();
            $transport->sendMessage();
        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf('Can\'t send %s item to recipient email %s', $provider->getName(), $recipient),
                $e->getMessage(),
                $data
            );
        }

        $this->inlineTranslation->resume();
    }

    public function getEmailTemplateVariables($provider, $data)
    {
        return [
            'file_name' => current(array_keys($data['files'])),
            'provider' => $provider,
            'order' => $data['order']
        ];
    }
}
