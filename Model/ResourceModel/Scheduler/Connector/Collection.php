<?php /** @noinspection ALL */
namespace Lindenvalley\RemoteOrder\Model\ResourceModel\Order\Export\Scheduler\Connector;

use Lindenvalley\RemoteOrder\Model\Order\Export\Scheduler\Connector;
use Lindenvalley\RemoteOrder\Model\ResourceModel\Order\Export\Scheduler\Connector as ResourceConfiguration;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'scheduler_connector_id';

    /**
     * Define resource model
     *
     * @return void
     * @noinspection MagicMethodsValidityInspection
     */
    protected function _construct(): void
    {
        $this->_init(Connector::class, ResourceConfiguration::class);
    }
}
