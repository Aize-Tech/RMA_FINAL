<?php
namespace Aize\Rma\Block\Adminhtml\RmaRequest;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Aize\Rma\Model\RmaRequestFactory;
use Aize\Rma\Model\ResourceModel\RmaItem\CollectionFactory as RmaItemCollectionFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Magento\Sales\Model\OrderFactory;

class View extends Template
{
    protected $_rmaRequestFactory;
    protected $_rmaItemCollectionFactory;
    protected $_orderItemFactory;
    protected $_orderFactory;

    public function __construct(
        Context $context,
        RmaRequestFactory $rmaRequestFactory,
        RmaItemCollectionFactory $rmaItemCollectionFactory,
        ItemFactory $orderItemFactory,
        OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_rmaRequestFactory = $rmaRequestFactory;
        $this->_rmaItemCollectionFactory = $rmaItemCollectionFactory;
        $this->_orderItemFactory = $orderItemFactory;
        $this->_orderFactory = $orderFactory;
        parent::__construct($context, $data);
    }

    public function getRmaRequest()
    {
        $id = $this->getRequest()->getParam('id');
        $rmaRequest = $this->_rmaRequestFactory->create()->load($id);
        return $rmaRequest;
    }

    public function getRelatedRmaItems()
    {
        $rmaId = $this->getRmaRequest()->getEntityId();
        $rmaItemCollection = $this->_rmaItemCollectionFactory->create();
        $rmaItemCollection->addFieldToFilter('rma_id', $rmaId);
        return $rmaItemCollection;
    }

    public function getOrderUrl($orderId)
    {
        $order = $this->_orderFactory->create()->load($orderId);
        $incrementId = $order->getIncrementId();
        return $this->getUrl('sales/order/view', ['order_id' => $orderId, 'increment_id' => $incrementId]);
    }

    public function getOrderItemName($orderItemId)
    {
        $orderItem = $this->_orderItemFactory->create()->load($orderItemId);
        return $orderItem->getName();
    }

    public function getOrderDate($orderId)
    {
        $order = $this->_orderFactory->create()->load($orderId);
        return $order->getCreatedAtFormatted(\IntlDateFormatter::MEDIUM);
    }

    public function getOrder($orderId)
    {
        return $this->_orderFactory->create()->load($orderId);
    }

    public function getBackUrl()
    {
        return $this->getUrl('aize_rma/rmarequest/index');
    }
}
