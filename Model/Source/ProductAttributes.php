<?php

declare(strict_types=1);

namespace MageSuite\ErpConnector\Model\Source;

class ProductAttributes implements \Magento\Framework\Data\OptionSourceInterface
{
    protected \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory;
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
    }

    protected function getProductAttributes(): \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
    {
        return $this->collectionFactory
            ->create()
            ->addFieldToFilter('entity_type_id', 4);
    }

    public function toOptionArray(): array
    {
        $attributes = $this->getProductAttributes();

        $options = [['value' => '', 'label' => __('-- Please Select --')]];
        foreach ($attributes as $attribute) {
            $options[] = [
                'value' => $attribute->getAttributeCode(),
                'label' => sprintf(
                    '%s (%s)',
                    $attribute->getDefaultFrontendLabel(),
                    $attribute->getAttributeCode()
                )
            ];
        }
        return $options;
    }
}
