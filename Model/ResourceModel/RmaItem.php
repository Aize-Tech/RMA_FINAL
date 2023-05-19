<?php
namespace Aize\Rma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RmaItem extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('rma_item', 'rma_item_id');
    }
}
