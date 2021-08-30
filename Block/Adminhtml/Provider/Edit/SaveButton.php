<?php
namespace MageSuite\ErpConnector\Block\Adminhtml\Provider\Edit;

class SaveButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'id_hard' => 'save_and_continue',
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => 'erp_connector_form.erp_connector_form',
                                'actionName' => 'save',
                                'params' => [
                                    true,
                                    [
                                        'back' => 'continue'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'class_name' => \Magento\Ui\Component\Control\Container::SPLIT_BUTTON
        ];
    }
}
