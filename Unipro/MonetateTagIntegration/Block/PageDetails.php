<?php

namespace Unipro\MonetateTagIntegration\Block;

use \Magento\Framework\App\RequestInterface;

class PageDetails extends \Magento\Framework\View\Element\Template
{

    protected $_categoryFactory;
    protected $_registry;
    protected $_cart;
    protected $_customerSession;
    protected $_layerResolver;
    protected $_data;

    public function __construct(
            \Magento\Backend\Block\Template\Context $context,
            \Magento\Catalog\Model\CategoryFactory $categoryFactory,
            \Magento\Checkout\Model\Cart $cart,
            \Magento\Catalog\Block\Product\ListProduct $productListing,
            \Magento\Framework\Registry $registry,
            \Magento\Customer\Model\Session $customerSession,
            \Magento\Catalog\Model\Layer\Resolver $layerResolver,
            array $data = []
    )
    {
        $this->_categoryFactory = $categoryFactory;
        $this->_cart = $cart;
        $this->_productListing = $productListing;
        $this->_registry = $registry;
        $this->_customerSession = $customerSession;
        $this->_layerResolver = $layerResolver;
        $this->_data = $data;
        parent::__construct($context, $data);
    }

    public function getPageType()
    {
        // Get page type and return it.
        $pageActionName = $this->_request->getFullActionName();

        switch ($pageActionName) {
            case 'cms_index_index':
            case 'cms_index_defaultindex':
                $pageType = "Main";
                break;
            case "catalog_category_view":
            case "catalog_category_view_type_default":
            case "catalog_category_view_type_default_without_children":
                if ($this->isCategoryLandingPage()) {
                    $pageType = "Index";
                } else {
                    $pageType = "Category";
                }
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
                break;
            case "wishlist_index_index":
            case "wishlist_index_share":
                $pageType = "Wish List";
                break;
            case "customer_account_login":
                $pageType = 'Login';
                break;
            case "customer_account_index":
                $pageType = 'Account';
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

            // Declare variable before loop.
            $cartRows = [];
            // Load each set of product details into an array and return it.
            foreach ($cartInfo->getQuote()->getAllVisibleItems() as $cartItem) {
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

        // Declare Variable before loop.
        $catList = [];

        // If there is a current category, see if there are parent categories and push to an array.
        if ($currentCat) {
            foreach ($currentCat->getParentCategories() as $parent) {
                $catList[] = $parent->getName();
            }
            return $catList;
        }
    }


    /**
     * @return \Magento\Catalog\Model\Category|null
     */
    public function getCurrentCategory()
    {
        /** @var \Magento\Catalog\Model\Category $category */
        $category = null;
        $pageId = $this->_request->getFullActionName();
        if (!in_array($pageId, ['catalogsearch_result_index', 'catalogsearch_advanced_result'])) {
            $category = $this->_registry->registry('current_category');
        }
        return $category;
    }

    /**
     * @return bool
     */
    public function isCategoryLandingPage() {
        //\Magento\Catalog\Model\Category::DM_MIXED - Shows static blocks and products
        //\Magento\Catalog\Model\Category::DM_PAGE - Shows static blocks
        //\Magento\Catalog\Model\Category::DM_PRODUCT - Shows products

        if (!is_null($this->getCurrentCategory()) && $this->getCurrentCategory()->getDisplayMode() == \Magento\Catalog\Model\Category::DM_PAGE) {
            return true; // content mode
        } else {
            return false; // mixed or product
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

    public function getSortFilters()
    {
        $filterList = [];
        $filters = $this->_layerResolver->get()->getState()->getFilters();

        foreach ($filters as $filter) {
            $filterList[] = array(
                'name' => $filter->getName(),
                'value' => $filter->getValueString()
            );
        }

        return $filterList;

    }

     public function getUserDetails()
     {
         if ($this->_customerSession->isLoggedIn()) {
//            $this->_customerSession->getCustomerId();
//            $this->_customerSession->getCustomerGroupId();
//            $this->_customerSession->getCustomer();
//            $this->_customerSession->getCustomerData();
//            $this->_customerSession->getCustomer()->getEmail();

             $userDetails[] = array(
                'customerID' => $this->_customerSession->getCustomerId(),
                'names' => $this->_customerSession->getCustomer()->getName(),
                'customerGroupId' => $this->_customerSession->getCustomerGroupId(),
                'customerEmail' => $this->_customerSession->getCustomer()->getEmail()
             );

             return $userDetails;
         }

//         return $userDetails;
     }


}
