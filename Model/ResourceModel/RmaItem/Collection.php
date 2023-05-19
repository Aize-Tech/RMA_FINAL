<?php
namespace Aize\Rma\Model\ResourceModel\RmaItem;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aize\Rma\Model\RmaItem as RmaItemModel;
use Aize\Rma\Model\ResourceModel\RmaItem as RmaItemResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(RmaItemModel::class, RmaItemResource::class);
    }
}
