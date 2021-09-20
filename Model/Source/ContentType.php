<?php
namespace MageSuite\ErpConnector\Model\Source;

class ContentType implements \Magento\Framework\Data\OptionSourceInterface
{
    const CONTENT_TYPE_XML = 'text/xml';
    const CONTENT_TYPE_JSON = 'application/json';

    public function toOptionArray()
    {
        return [
            ['value' => self::CONTENT_TYPE_XML, 'label' => __('Xml')],
            ['value' => self::CONTENT_TYPE_JSON, 'label' => __('Json')]
        ];
    }
}
