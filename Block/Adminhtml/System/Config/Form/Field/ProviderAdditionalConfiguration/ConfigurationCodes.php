<?php
namespace MageSuite\ErpConnector\Block\Adminhtml\System\Config\Form\Field\ProviderAdditionalConfiguration;

class ConfigurationCodes extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    protected function _construct()
    {
        $this->addColumn(
            'label',
            [
                'label' => __('Label'),
                'class' => 'input-text required-entry',
            ]
        );
        $this->addColumn(
            'value',
            [
                'label' => __('Code'),
                'class' => 'input-text required-entry',
            ]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');

        parent::_construct();
    }
}
