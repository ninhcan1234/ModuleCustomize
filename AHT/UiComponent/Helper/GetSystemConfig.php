<?php
namespace AHT\UiComponent\Helper;
use Magento\Framework\App\Helper\Context;

class GetSystemConfig extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue($config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
}
