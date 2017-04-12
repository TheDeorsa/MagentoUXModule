<?php

namespace Unipro\MonetateTagIntegration\Block;

use \Magento\Framework\App\RequestInterface;

class PageDetails extends \Magento\Framework\View\Element\Template
{

    protected $_categoryFactory;
    protected $_registry;
    protected $_cart;

    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            \Magento\Checkout\Model\Cart $cart,
            \Magento\Catalog\Block\Product\ListProduct $productListing,
            \Magento\Framework\Registry $registry,
            \Magento\Customer\Model\Session $customerSession,
            array $data = []
    )
    {

        $this->_categoryFactory = $categoryFactory;
        $this->_cart = $cart;
        $this->_productListing = $productListing;
        $this->_registry = $registry;
        $this->_customerSession = $customerSession;
        parent::__construct($context, $data);
    }

    public function getPageType()
    {
        // Get page type and return it.
        $pageActionName = $this->_request->getFullActionName();

        switch ($pageActionName) {
            case "cms_index_index":
                $pageType = "Index";
                break;
            case "catalog_category_view":
                $pageType = "Category";
                break;
            case "catalogsearch_result_index":
            case "catalogsearch_advanced_result":
            case "catalogsearch_advanced_index":
                $pageType = "Search";
                break;
            case "catalog_product_view":
                $pageType = "Product";
                break;
            case "checkout_cart_index":
                $pageType = "Cart";
                break;
            case "checkout_index_index":
                $pageType = "Checkout";
                break;
            case "checkout_onepage_success":
                $pageType = "Purchase";
            case "wishlist_index_index":
            case "wishlist_index_share":
                $pageType = "Wish List";
            case "customer_account_login":
                $pageType = 'Login';
                break;
            default:
                $pageType = "not tracked"; // TODO should we call setPageType in this scenario?
        }

        return $pageType;
    }

    public function getCartContents()
    {
        // Get cart info.
        $cartInfo = $this->_cart;

        if ($cartInfo->getItemsCount() > 0) {
            // Get currency code from the config.
            $currency = $cartInfo->getQuote()->getCurrency()->getStoreCurrencyCode();

            // Load each set of product details into an array and return it.
            foreach ($cartInfo->getQuote()->getItems() as $cartItem) {
                $cartRows[] = array (
                    'productId' => (string)$cartItem->getProductId(),
                    'sku' => (string)$cartItem->getSku(),
                    'quantity' => (string)$cartItem->getQty(),
                    'unitPrice' => (string)round($cartItem->getPrice(),2),
                    'currency' => $currency
                );
            }
            return $cartRows;
        }
    }

    public function getCurrentProduct()
    {
        // Get the current product and return it's ID.
        $productDetails = $this->_registry->registry('current_product');
        if ($productDetails) {
            return $productDetails->getId();
        }

    }

    public function getCurrentCategories()
    {
        // Get current category from the registry
        $currentCat = $this->_registry->registry('current_category');
        // If there is a current category, see if there are parent categories and push to an array.
        if ($currentCat) {
            foreach ($currentCat->getParentCategories() as $parent) {
                $catList[] = $parent->getName();
            }
            return $catList;
        }
    }

    public function getProductCollection()
    {
        $pageType = $this->getPageType();
        // Only run this function on a category or search page.
        if ($pageType === 'Category' || $pageType === 'Search') { //TODO is there a better way of doing this???
            // Pagination, Get number of items per page and page number.
            $limit = $this->getLimit();
            $page = ($this->getRequest()->getParam('p'))? $this->getRequest()->getParam('p') : 1;
            // Get loaded products and set the page size and limit on variables above.
            $loadedProds = $this->_productListing->getLoadedProductCollection();
            $loadedProds->setPageSize($limit);
            $loadedProds->setCurPage($page);

            // Loop through the array of products and get the product ID
            $listedProds = array();
            foreach ($loadedProds as $product) {
                array_push($listedProds, $product->getId());
            }
            return $listedProds;
        }
    }

    protected function getLimit()
    {
            /** @var \Magento\Catalog\Block\Product\ProductList\Toolbar $productListBlockToolbar */
            $productListBlockToolbar = $this->_layout->getBlock('product_list_toolbar');
            if (empty($productListBlockToolbar)) {
                return 1;
            }
            return (int) $productListBlockToolbar->getLimit();
    }

    // public function getUserDetails()
    // {
    //     if ($this->_customerSession->isLoggedIn()) {
    //        $this->_customerSession->getCustomerId();
    //        $this->_customerSession->getCustomerGroupId();
    //        $this->_customerSession->getCustomer();
    //        $this->_customerSession->getCustomerData();
    //        $this->_customerSession->getCustomer()->getEmail();
    //     }
    // }


    public function getCartContents2()
    {
        // Get cart info.
        $cartInfo = $this->_cart;

        if ($cartInfo->getItemsCount() > 0) {
            // Get currency code from the config.
            $currency = $cartInfo->getQuote()->getCurrency()->getStoreCurrencyCode();

            // Load each set of product details into an array and return it.
            foreach ($cartInfo->getQuote()->getItems() as $cartItem) {
                $cartRows[] = $cartItem;
                // $cartRows[] = array (
                //     'productId' => (string)$cartItem->getProductId(),
                //     'sku' => (string)$cartItem->getSku(),
                //     'quantity' => (string)$cartItem->getQty(),
                //     'unitPrice' => (string)round($cartItem->getPrice(),2),
                //     'currency' => $currency
                // );
            }
            return $cartRows;
            // return $cartInfo->getQuote();
        }
    }


}
