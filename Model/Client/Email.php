<?php
namespace MageSuite\ErpConnector\Model\Client;

class Email extends \MageSuite\ErpConnector\Model\Client\Client implements ClientInterface
{
    protected \MageSuite\ErpConnector\Helper\Configuration $configuration;
    protected \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation;
    protected \Magento\Framework\Mail\Template\TransportBuilderFactory $transportBuilderFactory;
    protected \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage;

    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \MageSuite\ErpConnector\Helper\Configuration $configuration,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilderFactory $transportBuilderFactory,
        \MageSuite\ErpConnector\Model\Command\LogErrorMessage $logErrorMessage,
        array $data = []
    ) {
        parent::__construct($eventManager, $data);

        $this->configuration = $configuration;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilderFactory = $transportBuilderFactory;
        $this->logErrorMessage = $logErrorMessage;
    }

    public function sendItems($provider, $items)
    {
        $sender = $this->configuration->getEmailSenderInfo();
        $recipients = explode(',', $this->getData('email'));

        foreach ($items as $item) {
            $item['sender'] = $sender;
            $item['recipients'] = $recipients;

            $this->sendItem($provider, $item);
        }

        return $this;
    }

    protected function sendItem($provider, $item)
    {
        $files = $item['files'] ?? null;

        if (empty($files)) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing files data',
                $item
            );
            return false;
        }

        if (!isset($item['order']) || empty($item['order'])) {
            $this->logErrorMessage->execute(
                sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                'Missing order data',
                $item
            );
            return false;
        }

        foreach ($item['recipients'] as $recipient) {
            try {
                $this->sendItemToRecipient($provider, $item, $recipient);
            } catch (\Exception $e) {
                $this->logErrorMessage->execute(
                    sprintf(self::ERROR_MESSAGE_TITLE_FORMAT, $provider->getName()),
                    $e->getMessage(),
                    $item
                );

                throw $e;
            }
        }

        return true;
    }

    protected function sendItemToRecipient($provider, $item, $recipient)
    {
        $emailTemplateVariables = $this->getEmailTemplateVariables($provider, $item);

        try {
            $this->inlineTranslation->suspend();

            $transport = $this->transportBuilderFactory
                ->create()
                ->setTemplateIdentifier($this->getData('template'))
                ->setTemplateOptions(['area' => 'frontend', 'store' => $item['order']->getStoreId()])
                ->setTemplateVars($emailTemplateVariables)
                ->setFromByScope($item['sender'])
                ->addTo($recipient);

            foreach ($item['files'] as $fileName => $content) {
                $transport->addAttachmentFromContent($content, $fileName, \Magento\Framework\Mail\MimeInterface::TYPE_OCTET_STREAM);
            }

            $transport->getTransport()->sendMessage();
        } catch (\Exception $e) {
            $this->logErrorMessage->execute(
                sprintf('Can\'t send %s item to recipient email %s', $provider->getName(), $recipient),
                $e->getMessage(),
                $item
            );
        }

        $this->inlineTranslation->resume();
    }

    public function getEmailTemplateVariables($provider, $item)
    {
        return [
            'file_name' => current(array_keys($item['files'])),
            'provider' => $provider,
            'order' => $item['order']
        ];
    }

    public function validateProcessedFile($fileName)
    {
        throw new \Exception('Not possibile to verify if file exist for Email client.'); //phpcs:ignore
    }
}
