<?php
namespace MageSuite\ErpConnector\Block\Adminhtml\Connection;

class Check extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $url;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Backend\Model\UrlInterface $url,
        array $data = []
    ) {
        $this->url = $url;

        parent::__construct($context, $data);
    }

    public function getCheckConnectionUrl()
    {
        return $this->url->getRouteUrl('erp_connector/connection/check/');
    }
}
