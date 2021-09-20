<?php
namespace MageSuite\ErpConnector\Model\Source;

class Providers implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @var \MageSuite\ErpConnector\Api\ProviderRepositoryInterface
     */
    protected $providerRepository;

    public function __construct(\MageSuite\ErpConnector\Api\ProviderRepositoryInterface $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    public function toOptionArray()
    {
        $providers = $this->providerRepository->getList();

        if (!$providers->getTotalCount()) {
            return [];
        }

        $list = [];

        foreach ($providers->getItems() as $provider) {
            $list[] = [
                'value' => $provider->getId(),
                'label' => $provider->getName()
            ];
        }

        return $list;
    }
}
