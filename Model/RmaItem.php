<?php
namespace Aize\Rma\Model;

use Magento\Framework\Model\AbstractModel;
use Aize\Rma\Model\ResourceModel\RmaItem as RmaItemResource;

class RmaItem extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(RmaItemResource::class);
    }
}
