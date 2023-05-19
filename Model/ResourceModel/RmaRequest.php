<?php

namespace Aize\Rma\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class RmaRequest extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('rma_request', 'entity_id');
    }
}
