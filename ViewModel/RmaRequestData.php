<?php
namespace Aize\Rma\ViewModel;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Model\OrderFactory;

class RmaRequestData implements ArgumentInterface
{
    protected $request;
    protected $orderFactory;

    public function __construct(RequestInterface $request, OrderFactory $orderFactory)
    {
        $this->request = $request;
        $this->orderFactory = $orderFactory;
    }

    public function getOrder()
    {
        $orderId = $this->request->getParam('order_id');
        $order = $this->orderFactory->create()->load($orderId);
        if ($order && $order->getId()) {
            return $order;
        }
        return null;
    }

    public function getOrderItems()
    {
        $order = $this->getOrder();
        $items = [];
        foreach ($order->getAllItems() as $item) {
            $items[] = $item;
        }
        return $items;
    }

    public function getOrderItemsData()
    {
        $itemsData = [];
        foreach ($this->getOrderItems() as $item) {
            $itemsData[] = [
                'item_id' => $item->getItemId(),
                'qty_ordered' => (int)$item->getQtyOrdered(),
            ];
        }
        return $itemsData;
    }

    public function getOrderIncrementId($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        if ($order && $order->getId()) {
            return $order->getIncrementId();
        }
        return null;
    }



}
