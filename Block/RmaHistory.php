<?php

namespace Aize\Rma\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Customer\Model\Session as CustomerSession;
use Aize\Rma\Model\ResourceModel\RmaRequest\CollectionFactory;
use Magento\Sales\Model\Order\ItemFactory;
use Aize\Rma\ViewModel\RmaRequestData;
use Aize\Rma\Helper\Data as DataHelper;

class RmaHistory extends Template
{
    protected $customerSession;
    protected $rmaCollectionFactory;
    protected $orderItemFactory;
    protected $rmaRequestData;
    protected $dataHelper;

    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CollectionFactory $rmaCollectionFactory,
        ItemFactory $orderItemFactory,
        RmaRequestData $rmaRequestData,
        DataHelper $dataHelper,
        array $data = []
    ) {
        $this->customerSession = $customerSession;
        $this->rmaCollectionFactory = $rmaCollectionFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->rmaRequestData = $rmaRequestData;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function getCustomerRmaRequests()
    {
        if(!$this->dataHelper->isModuleEnabled()) {
            return [];
        }
        $collection = $this->rmaCollectionFactory->create();
        $collection->setOrder('entity_id', 'DESC');

        return $collection;
    }

    public function getOrderItemName($orderItemId)
    {
        $orderItem = $this->orderItemFactory->create()->load($orderItemId);
        return $orderItem->getName();
    }

    public function getViewModel()
    {
        return $this->rmaRequestData;
    }
}
