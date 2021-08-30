<?php
namespace MageSuite\ErpConnector\Setup\Patch\Data;

class UpdatePathsInCoreConfig implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    /**
     * @var \Magento\Framework\App\Config\Storage\WriterInterface
     */
    protected $configWriter;

    /**
     * @var \Magento\Framework\Serialize\SerializerInterface
     */
    protected $serializer;

    public function __construct(
        \Magento\Framework\App\Config\Storage\WriterInterface $configWriter,
        \Magento\Framework\Serialize\SerializerInterface $serializer
    ) {
        $this->configWriter = $configWriter;
        $this->serializer = $serializer;
    }

    public function apply()
    {
        $value = [
            '_1620775082445_445' => ['label' => __('Address'), 'value' => 'address']
        ];

        $this->configWriter->save(
            \MageSuite\ErpConnector\Helper\Configuration::XML_PATH_PROVIDER_CONFIGURATION_CODES,
            $this->serializer->serialize($value)
        );
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
