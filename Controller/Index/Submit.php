<?php
namespace Aize\Rma\Controller\Index;

use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aize\Rma\Model\RmaRequestFactory;
use Aize\Rma\Model\ResourceModel\RmaRequest as RmaRequestResource;
use Aize\Rma\Model\RmaItemFactory;
use Aize\Rma\Model\ResourceModel\RmaItem as RmaItemResource;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class Submit extends Action
{
    protected $logger;
    protected $orderFactory;
    protected $rmaRequestFactory;
    protected $rmaRequestResource;
    protected $formKeyValidator;
    protected $orderRepository;
    protected $transportBuilder;
    protected $storeManager;

    public function __construct(
        Context $context,
        RmaRequestFactory $rmaRequestFactory,
        RmaRequestResource $rmaRequestResource,
        Validator $formKeyValidator,
        OrderFactory $orderFactory,
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        RmaItemFactory $rmaItemFactory,
        RmaItemResource $rmaItemResource,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->rmaRequestFactory = $rmaRequestFactory;
        $this->rmaRequestResource = $rmaRequestResource;
        $this->formKeyValidator = $formKeyValidator;
        $this->orderFactory = $orderFactory;
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->rmaItemFactory = $rmaItemFactory;
        $this->rmaItemResource = $rmaItemResource;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $orderId = $this->getRequest()->getParam('order_id');
            $orderItemIds = $this->getRequest()->getParam('order_item_ids');
            $quantities = $this->getRequest()->getParam('quantities');
            $reasons = $this->getRequest()->getParam('reasons');

            if (!$orderId || !$orderItemIds || !$quantities || !$reasons) {
                throw new \Exception("Missing required fields.");
            }

            $order = $this->orderRepository->get($orderId);
            if (!$order->getId()) {
                throw new \Exception("Invalid order ID.");
            }

            $rmaRequest = $this->rmaRequestFactory->create();
            $rmaRequest->setOrderId($orderId);
            $this->rmaRequestResource->save($rmaRequest);

            foreach ($orderItemIds as $orderItemId) {
                $orderItem = $order->getItemById($orderItemId);
                $rmaItem = $this->rmaItemFactory->create();
                $rmaItem->setRmaId($rmaRequest->getId());
                $rmaItem->setOrderId($orderId);
                $rmaItem->setOrderItemId($orderItemId);
                $rmaItem->setSku($orderItem->getSku());
                $rmaItem->setName($orderItem->getName());
                $rmaItem->setQuantityRequested($quantities[$orderItemId]);
                $rmaItem->setReason($reasons[$orderItemId]);
                $this->rmaItemResource->save($rmaItem);
            }

            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $this->storeManager->getStore()->getId(),
            ];

            $templateVars = [
                'order' => $order,
                'customer_name' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                'rma_request_id' => $rmaRequest->getId(),
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('rma_email_confirmation_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom('general')
                ->addTo($order->getCustomerEmail())
                ->getTransport();

            $transport->sendMessage();

            $this->messageManager->addSuccessMessage(__('Your RMA request has been submitted successfully.'));
            $resultRedirect->setPath('rma/index/view');
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $this->logger->critical($e);
            $resultRedirect->setPath('rma/index/create');
            return $resultRedirect;
        }
    }

}
