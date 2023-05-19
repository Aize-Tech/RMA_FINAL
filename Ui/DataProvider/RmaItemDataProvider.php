<?php
namespace Aize\Rma\Ui\DataProvider;

use Aize\Rma\Model\ResourceModel\RmaItem\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class RmaItemDataProvider extends AbstractDataProvider
{
    protected $request;

    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->collection = $this->getRmaItemCollection($collectionFactory);
    }

    protected function getRmaItemCollection(CollectionFactory $collectionFactory)
    {
        $collection = $collectionFactory->create();

        $rmaId = $this->request->getParam('rma_id');

        if ($rmaId) {
            $collection->addFieldToFilter('rma_id', $rmaId);
        }
        return $collection;
    }

    public function getData()
    {
        $items = $this->collection->getItems();
        $result = [];

        foreach ($items as $item) {
            $result[$item->getId()] = $item->getData();
        }

        return [
            'totalRecords' => $this->collection->getSize(),
            'items' => $result,
        ];
    }
}
