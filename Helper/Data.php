<?php
namespace Aize\Rma\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    const XML_PATH_RMA = 'rma/general/enable';

    public function isModuleEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_RMA,
            ScopeInterface::SCOPE_STORE
        );
    }
}
