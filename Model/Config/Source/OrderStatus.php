<?php
namespace Aize\Rma\Model\Config\Source;

use Magento\Sales\Model\Order\StatusFactory;

class OrderStatus implements \Magento\Framework\Data\OptionSourceInterface
{
    protected $statusFactory;

    public function __construct(StatusFactory $statusFactory)
    {
        $this->statusFactory = $statusFactory;
    }

    public function toOptionArray()
    {
        $collection = $this->statusFactory->create()->getCollection();
        $options = [];
        foreach ($collection as $item) {
            $options[] = ['value' => $item->getStatus(), 'label' => $item->getLabel()];
        }
        return $options;
    }
}
