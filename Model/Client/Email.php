<?php
namespace MageSuite\ErpConnector\Model\Client;

class Email extends Client implements ClientInterface
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

    public function __construct(
        \MageSuite\ErpConnector\Model\Command\AddAdminNotification $addAdminNotification,
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilderFactory $transportBuilderFactory,
        \MageSuite\ErpConnector\Logger\Logger $logger,
        array $data = []
    ) {
        parent::__construct($addAdminNotification, $logger, $data);

        $this->configuration = $configuration;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilderFactory = $transportBuilderFactory;
    }

    public function sendItem($provider, $data)
    {
        if (!isset($data['files']) || empty($data['files'])) {
            $this->logErrorMessage($provider->getName() . ' provider ERROR', 'Missing files');
            return $this;
        }

        if (!isset($data['order']) || empty($data['order'])) {
            $this->logErrorMessage($provider->getName() . ' provider ERROR', 'Missing order data');
            return $this;
        }

        $sender = $this->configuration->getEmailSenderInfo();
        $recipients = explode(',', $this->getData('email'));

        foreach ($recipients as $recipient) {
            try {
                $this->sendItemToRecipient($provider, $sender, $recipient, $data);
            } catch (\Exception $e) {
                $this->logErrorMessage($provider->getName() . ' provider ERROR', $e->getMessage());
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
            $this->logErrorMessage('Can\'t send ' . $provider->getName() . ' item to recipient email ' . $recipient, $e->getMessage());
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
