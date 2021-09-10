<?php
namespace MageSuite\ErpConnector\Model\Source;

class SoapVersion implements \Magento\Framework\Data\OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => 'Select Version'],
            ['value' => SOAP_1_1, 'label' => '1.1'],
            ['value' => SOAP_1_2, 'label' => '1.2']
        ];
    }
}
