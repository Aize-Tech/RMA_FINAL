<?php
namespace Aize\Rma\Ui\Component\Listing\Column;

use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Framework\Data\Form\FormKey;

class RmaRequestActions extends Column
{
    protected $urlBuilder;
    protected $formKey;

    public function __construct(
        UrlInterface $urlBuilder,
        FormKey $formKey,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->formKey = $formKey;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                $item[$name]['view'] = [
                    'href' => $this->urlBuilder->getUrl(
                        'aize_rma/rmarequest/view', // Updated the URL path here
                        [
                            'id' => $item['entity_id']
                        ]
                    ),
                    'label' => __('View'),
                    'hidden' => false,
                ];
            }
        }
        return $dataSource;
    }
}
