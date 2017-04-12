<?php

namespace Unipro\MonetateTagIntegration\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    /**
     * Xml path extension enabled
     */
    const XML_PATH_EXTENSION_ENABLED = 'unipro_monetate_tag/general/enabled';

    /**
     * Xml path tag
     */
    const XML_PATH_TAG = 'unipro_monetate_tag/general/tag';

    /**
     * Is tag available
     *
     * @return bool
     */
    public function isTagAvailable()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_EXTENSION_ENABLED, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get tag
     *
     * @return mixed
     */
    public function getTag()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_TAG, ScopeInterface::SCOPE_STORE);
    }
}
