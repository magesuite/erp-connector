<?php
namespace MageSuite\ErpConnector\Model\Source;

class RequestMethod implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_GET, 'label' => __('GET')],
            ['value' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_POST, 'label' => __('POST')],
            ['value' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_PUT, 'label' => __('PUT')],
            ['value' => \Magento\Framework\Webapi\Rest\Request::HTTP_METHOD_DELETE, 'label' => __('DELETE')],
        ];
    }
}
