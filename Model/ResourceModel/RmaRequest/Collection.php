<?php

namespace Aize\Rma\Model\ResourceModel\RmaRequest;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aize\Rma\Model\RmaRequest;
use Aize\Rma\Model\ResourceModel\RmaRequest as RmaRequestResource;

class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(RmaRequest::class, RmaRequestResource::class);
    }
}
