<?php

namespace Aize\Rma\Block;

use Aize\Rma\Helper\OrderConf as OrderHelper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Registry;
use Aize\Rma\Helper\Data;

class Rma extends Template
{
    protected $orderHelper;
    protected $orderRepository;
    protected $registry;
    protected $helper;

    public function __construct(
        Context $context,
        OrderRepositoryInterface $orderRepository,
        Registry $registry,
        OrderHelper $orderHelper,
        Data $helper,
        array $data = []
    ) {
        $this->orderHelper = $orderHelper;
        $this->orderRepository = $orderRepository;
        $this->registry = $registry;
        $this->helper = $helper;
        parent::__construct($context, $data);
    }

    public function isRmaAvailable(): bool
    {
        if (!$this->helper->isModuleEnabled()) {
            return false;
        }

        $order = $this->getOrder();

        if (!$this->orderHelper->isRmaAllowed($order)) {
            return false;
        }

        return true;
    }

    public function getOrder()
    {
        $orderId = $this->getRequest()->getParam('order_id');
        return $this->orderRepository->get($orderId);
    }
}
