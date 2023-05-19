<?php
namespace Aize\Rma\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Sales\Model\Order;

class OrderConf extends AbstractHelper
{
    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function isRmaAllowed(Order $order)
    {
        $allowedOrderStatuses = $this->scopeConfig->getValue(
            'rma/general/order_statuses',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $allowedOrderStatusesArray = explode(',', $allowedOrderStatuses);

        return in_array($order->getStatus(), $allowedOrderStatusesArray);
    }
}
