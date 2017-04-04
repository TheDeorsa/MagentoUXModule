<?php

namespace Unipro\DataLayer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Cookie\Helper\Cookie as CookieHelper;

class Ddl extends Template {

    protected $categoryFactory;
      /**
       * This is the controller for the dataLayer module.*
       * @param Context $context
       * @param CookieHelper $cookieHelper
       * @param DdlHelper $ddlHelper
       * @param \Unipro\DataLayer\Model\DataLayer $dataLayer
       * @param \Magento\Framework\App\ProductMetadata $productMetadata
       * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection
       * @param array $data
       * @param \Magento\Catalog\Block\Product\ListProduct $productListing
       * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
       */
    public function __construct(
        Context $context,
        CookieHelper $cookieHelper,
        DdlHelper $ddlHelper,
        \Unipro\DataLayer\Model\DataLayer $dataLayer,
        \Magento\Framework\App\ProductMetadata $productMetadata,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $salesOrderCollection,
        array $data = [],
        \Magento\Catalog\Block\Product\ListProduct $productListing,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->_cookieHelper = $cookieHelper;
        $this->_ddlHelper = $ddlHelper;
        $this->_dataLayerModel = $dataLayer;
        $this->_productMetadata = $productMetadata;
        $this->_salesOrderCollection = $salesOrderCollection;
        parent::__construct($context, $data);
        $this->categoryFactory = $categoryFactory;
        $this->productListing = $productListing;
        $this->_dataLayerModel->setDigitalDataLayer();
    }


}
