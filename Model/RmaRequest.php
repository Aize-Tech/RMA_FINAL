<?php

namespace Aize\Rma\Model;

use Magento\Framework\Model\AbstractModel;
use Aize\Rma\Model\ResourceModel\RmaRequest as RmaRequestResource;

class RmaRequest extends AbstractModel
{

    protected function _construct()
    {
        $this->_init(RmaRequestResource::class);
    }
}
