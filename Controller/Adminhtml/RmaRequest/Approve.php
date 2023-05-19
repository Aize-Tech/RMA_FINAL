<?php
namespace Aize\Rma\Controller\Adminhtml\RmaRequest;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aize\Rma\Model\RmaRequest;
use Aize\Rma\Model\RmaItem;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class Approve extends Action
{
    protected $rmaItemModel;
    protected $orderRepository;
    protected $transportBuilder;
    protected $storeManager;

    public function __construct(
        Context $context,
        RmaRequest $rmaRequestModel,
        RmaItem $rmaItemModel,
        OrderRepositoryInterface $orderRepository,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->rmaRequestModel = $rmaRequestModel;
        $this->rmaItemModel = $rmaItemModel;
        $this->orderRepository = $orderRepository;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function execute()
    {
        $rmaRequestId = $this->getRequest()->getParam('rma_id');
        $rmaItemId = $this->getRequest()->getParam('rma_item_id');

        try {
            $rmaItem = $this->rmaItemModel->load($rmaItemId);

            if ($rmaItem->getRmaId() != $rmaRequestId) {
                throw new \Exception(__('The RMA item does not belong to the specified request.'));
            }

            $orderId = $rmaItem->getOrderId();
            $order = $this->orderRepository->get($orderId);

            $rmaItem->setRmaStatus('Approved');
            $rmaItem->save();

            $rmaItems = $this->rmaItemModel->getCollection()->addFieldToFilter('rma_id', $rmaRequestId);

            $approvedCount = 0;
            $deniedCount = 0;

            foreach($rmaItems as $item) {
                if($item->getRmaStatus() == 'Approved') {
                    $approvedCount++;
                } elseif($item->getRmaStatus() == 'Denied') {
                    $deniedCount++;
                }
            }

            $rmaRequest = $this->rmaRequestModel->load($rmaRequestId);

            if($deniedCount > 0 && $approvedCount > 0) {
                $rmaRequest->setAuthorized('Partially Authorized');
            } elseif($deniedCount > 0) {
                $rmaRequest->setAuthorized('Denied');
            } elseif($approvedCount > 0) {
                $rmaRequest->setAuthorized('Approved');
            }
            $rmaRequest->save();

            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                'store' => $this->storeManager->getStore()->getId(),
            ];

            $templateVars = [
                'order' => $order,
                'customer_name' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(),
                'order_url' => $this->_url->getUrl('sales/order/view', ['order_id' => $order->getId()]),
                'rma_id' => $rmaRequestId,
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('rma_email_approval_template')
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom('general')
                ->addTo($order->getCustomerEmail())
                ->getTransport();

            $transport->sendMessage();

            $this->messageManager->addSuccessMessage(__('The RMA item has been approved and the customer has been notified.'));
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setRefererUrl();
        return $resultRedirect;
    }
}
