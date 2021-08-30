<?php
namespace MageSuite\ErpConnector\Block\Adminhtml\Provider\Edit;

class DeleteButton implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->request = $request;
        $this->urlBuilder = $urlBuilder;
    }

    public function getButtonData()
    {
        $providerId = $this->request->getParam('provider_id');

        if (!$providerId) {
            return [];
        }

        return [
            'label' => __('Delete Provider'),
            'class' => 'delete',
            'on_click' => 'deleteConfirm(\'' . __(
                'Are you sure you want to do this?'
            ) . '\', \'' . $this->getDeleteUrl($providerId) . '\', {"data": {}})',
            'sort_order' => 20,
        ];
    }

    public function getDeleteUrl($providerId)
    {
        return $this->urlBuilder->getUrl('*/*/delete', ['provider_id' => $providerId]);
    }
}
