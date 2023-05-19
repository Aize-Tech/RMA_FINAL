<?php
namespace Aize\Rma\Model\Config\Source\Email;

use Magento\Framework\Option\ArrayInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class Template implements ArrayInterface
{
    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function toOptionArray()
    {
        $storeId = $this->scopeConfig->getValue('aize_rma/general/store_id', ScopeInterface::SCOPE_STORE);
        $templates = [
            [
                'value' => 'aize_rma/rma_email_confirmation_template',
                'label' => __('RMA Request Confirmation'),
            ],
            [
                'value' => 'aize_rma/rma_email_approval_template',
                'label' => __('RMA Request Approval'),
            ],
            [
                'value' => 'aize_rma/rma_email_denial_template',
                'label' => __('RMA Request Denial'),
            ]
        ];

        foreach ($templates as $key => $template) {
            $templateConfigPath = $template['value'];
            $availableTemplates = $this->scopeConfig->getValue($templateConfigPath . '/available_templates', ScopeInterface::SCOPE_STORES, $storeId);
            if ($availableTemplates) {
                $availableTemplates = explode(',', $availableTemplates);
                if (!in_array($templateConfigPath, $availableTemplates)) {
                    unset($templates[$key]);
                }
            }
        }

        return $templates;
    }
}
