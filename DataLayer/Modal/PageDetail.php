<?php

namespace Unipro\Datalayer\Model;

use Magento\Framework\DataObject;
use Persomi\Digitaldatalayer\Helper\Data as DdlHelper;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableProductType;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProductType;

class DataLayer extends DataObject {

        /**
     * @param MessageInterface $message
     * @param null $parameters
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        DdlHelper $ddlHelper,
        ConfigurableProductType $ConfigurableProductType,
        GroupedProductType $GroupedProductType,
        \Magento\Sales\Model\Order $salesOrder,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Tax\Model\Calculation $calculation,
        \Magento\Catalog\Helper\Data $catalogHelper,
        \Magento\Framework\Registry $registry
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->_customerSession = $customerSession;
        $this->_context = $context;
        $this->_coreRegistry = $registry;
        $this->_checkoutSession = $checkoutSession;
        $this->_ddlHelper = $ddlHelper;
        $this->_salesOrder = $salesOrder;
        $this->_storeManager = $storeManager;
        $this->_productloader = $productFactory;
        $this->_categoryloader = $categoryFactory;
        $this->_stockRegistry = $stockRegistry;
        $this->_calculationTool = $calculation;
        $this->_ConfigurableProductType = $ConfigurableProductType;
        $this->_GroupedProductType = $GroupedProductType;
        $this->catalogHelper = $catalogHelper;

        $this->fullActionName = $this->_context->getRequest()->getFullActionName();
    }
