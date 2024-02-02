<?php

declare(strict_types=1);

namespace MageSuite\ErpConnector\Model\Source;

class ProductAttributes implements \Magento\Framework\Data\OptionSourceInterface
{
    protected \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory;
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;
    protected \Magento\Eav\Model\Config $eavConfig;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $collectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->storeManager = $storeManager;
        $this->eavConfig = $eavConfig;
    }

    protected function getProductAttributes(): \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
    {
        $entityTypeId = $this->eavConfig
            ->getEntityType(\Magento\Catalog\Api\Data\ProductAttributeInterface::ENTITY_TYPE_CODE)
            ->getEntityTypeId();

        return $this->collectionFactory
            ->create()
            ->addFieldToFilter('entity_type_id', $entityTypeId);
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
