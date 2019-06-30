<?php

namespace UgoRaffaele\Autocomplete\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

    const XML_PATH_ENABLE = 'ugoraffaele_autocomplete/general/enable_in_frontend';
    
    const XML_PATH_LIMIT = 'ugoraffaele_autocomplete/general/limit';

    const XML_PATH_CATELIMIT = 'ugoraffaele_autocomplete/general/catlimit';

    const XML_PATH_MINLENGTH = 'ugoraffaele_autocomplete/general/min_length';

    const XML_PATH_CACHETIME = 'ugoraffaele_autocomplete/general/cache_lifetime';

    const XML_PATH_LOCALSTORAGE = 'ugoraffaele_autocomplete/general/use_local_storage';

    const XML_PATH_SHOWPRODUCTS = 'ugoraffaele_autocomplete/general/showproducts';

    const XML_PATH_SHOWCATEGORIES = 'ugoraffaele_autocomplete/general/showcategories';

    const XML_PATH_CATHEDTEXT = 'ugoraffaele_autocomplete/design/cat_header_text';

    const XML_PATH_CATHEDBACK = 'ugoraffaele_autocomplete/design/cat_header_background';

    const XML_PATH_CATRESTEXT = 'ugoraffaele_autocomplete/design/cat_result_text';

    const XML_PATH_CATRESBACK = 'ugoraffaele_autocomplete/design/cat_result_background';

    const XML_PATH_CATRESTEXTHOV = 'ugoraffaele_autocomplete/design/cat_result_text_hover';

    const XML_PATH_PROHEDTEXT = 'ugoraffaele_autocomplete/design/pro_header_text';

    const XML_PATH_PROHEDBACK = 'ugoraffaele_autocomplete/design/pro_header_background';

    const XML_PATH_PRORESTEXT = 'ugoraffaele_autocomplete/design/pro_result_text';

    const XML_PATH_PRORESBACK = 'ugoraffaele_autocomplete/design/pro_result_background';

    const XML_PATH_PRORESTEXTHOV = 'ugoraffaele_autocomplete/design/pro_result_text_hover';

    const XML_PATH_CATHEADERTEXT = 'ugoraffaele_autocomplete/design/cat_header_textfont';

    const XML_PATH_PROHEADERTEXT = 'ugoraffaele_autocomplete/design/pro_header_textfont';

    const XML_PATH_PROFOOTERTEXT = 'ugoraffaele_autocomplete/design/pro_footer_textfont';

    const XML_PATH_PRORESPRICETEXT = 'ugoraffaele_autocomplete/design/pro_result_price_color';
    
    const XML_PATH_PRORESPRICETEXTHOV = 'ugoraffaele_autocomplete/design/pro_result_price_color_hover';

    const XML_PATH_PRORESPRICELABEL = 'ugoraffaele_autocomplete/design/pro_result_price_label_color';

    const XML_PATH_PRORESPRICELABELHOV = 'ugoraffaele_autocomplete/design/pro_result_price_label_color_hov';

    private $price;

    private $store;

    private $cache;

    public $scopeConfig;

    public function __construct(
        \Magento\Framework\Locale\Format $price,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Framework\App\CacheInterface $cache,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->price = $price;
        $this->store = $store;
        $this->cache = $cache;
        $this->scopeConfig = $scopeConfig;
    }

    //Check if Extension is Enabled or not
    public function isEnabled() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE, $storeScope);
    }

    //Get the Product Limit
    public function getLimit() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_LIMIT, $storeScope) == null) {
            return 3;
        }
        return $this->scopeConfig->getValue(self::XML_PATH_LIMIT, $storeScope);
    }

    //Get the Category Limit
    public function getCategoryLimit() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_CATELIMIT, $storeScope) == null) {
            return 5;
        }
        return $this->scopeConfig->getValue(self::XML_PATH_CATELIMIT, $storeScope);
    }

    //Get the Min Length
    public function getMinLength() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_MINLENGTH, $storeScope) == null) {
            return 1;
        }
        return $this->scopeConfig->getValue(self::XML_PATH_MINLENGTH, $storeScope);
    }

    //Get the Cache LifeTime
    public function getCacheLifetime() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_CACHETIME, $storeScope) == null) {
            return 86400;
        }
        return $this->scopeConfig->getValue(self::XML_PATH_CACHETIME, $storeScope);
    }

    //Get the Local Storage
    public function getUseLocalStorage() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_LOCALSTORAGE, $storeScope);
    }

    //Get the View Product Config
    public function getViewProductConf() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_showproductsS, $storeScope);
    }

    //Get the View Category Config
    public function getViewCategoryConf() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_SHOWCATEGORIES, $storeScope);
    }

    //Get the Js Format Price
    public function getJsPriceFormat() {
        $priceFormat = $this->price;
        return $priceFormat->getPriceFormat();
    }

    //Get the Base Url
    public function getBaseUrl() {
        $storeManager = $this->store;
        return $storeManager->getStore()->getBaseUrl();
    }

    //Get the Media Url
    public function getBaseUrlMedia() {
        $storeManager = $this->store;
        return $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    //Get the return Cache Data
    public function getCacheData($name) {
        $cache_filename = hash('sha256', $name);
        if (false !== ($data = $this->cache->load($cache_filename))) {
            return $data;
        } else {
            return 0;
        }
    }

    //Store Cache Data
    public function storeCacheData($name, $Cachedata) {
        $cache_filename = hash('sha256', $name);
        $this->cache->save($Cachedata, $cache_filename, ['ugoraffaele_autocomplete']);
    }

    //Get the Category Header Text Color
    public function getCategoryHeaderTextColor() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_CATHEDTEXT, $storeScope) == null) {
            return '#FFF';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_CATHEDTEXT, $storeScope);
    }

    //Get the Category Header Background Color
    public function getCategoryHeaderBackground() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_CATHEDBACK, $storeScope) == null) {
            return '#3D3D3D';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_CATHEDBACK, $storeScope);
    }

    //Get the Category Result Text Color
    public function getCategoryResultTextColor() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_CATRESTEXT, $storeScope) == null) {
            return '#3D3D3D';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_CATRESTEXT, $storeScope);
    }

    //Get the Category Result Background Color
    public function getCategoryResultBackground() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_CATRESBACK, $storeScope) == null) {
            return '#FFF';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_CATRESBACK, $storeScope);
    }

    //Get the Category Result Text Hover Color
    public function getCategoryResultTextColorHover() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_CATRESTEXTHOV, $storeScope) == null) {
            return '#000';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_CATRESTEXTHOV, $storeScope);
    }

    //Get the Product Header Text Color
    public function getProductHeaderTextColor() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PROHEDTEXT, $storeScope) == null) {
            return '#FFF';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PROHEDTEXT, $storeScope);
    }

    //Get the Product Header Background Color
    public function getProductHeaderBackground() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PROHEDBACK, $storeScope) == null) {
            return '#3D3D3D';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PROHEDBACK, $storeScope);
    }

    //Get the Product Result Text Color
    public function getProductResultTextColor() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PRORESTEXT, $storeScope) == null) {
            return '#3D3D3D';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PRORESTEXT, $storeScope);
    }

    //Get the Product Result Background Color
    public function getProductResultBackground() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PRORESBACK, $storeScope) == null) {
            return '#FFF';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PRORESBACK, $storeScope);
    }

    //Get the Product Result Text Hover Color
    public function getProductResultTextHoverColor() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PRORESTEXTHOV, $storeScope) == null) {
            return '#000';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PRORESTEXTHOV, $storeScope);
    }

    //Get the Product Header Text
    public function getProductHeaderText() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PROHEADERTEXT, $storeScope) == null) {
            return 'PRODUCTS';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PROHEADERTEXT, $storeScope);
    }

    //Get the Product Footer Text
    public function getProductFooterText() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PROFOOTERTEXT, $storeScope) == null) {
            return 'VIEW MORE RESULTS';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PROFOOTERTEXT, $storeScope);
    }

    //Get the Category Header Text
    public function getCategoryHeaderText() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_CATHEADERTEXT, $storeScope) == null) {
            return 'CATEGORIES';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_CATHEADERTEXT, $storeScope);
    }

    //Get the Product Result Price Text Color
    public function getProductResultPriceTextColor() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PRORESPRICETEXT, $storeScope) == null) {
            return '#3D3D3D';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PRORESPRICETEXT, $storeScope);
    }

    //Get the Product Result Price Text Color
    public function getProductResultPriceTextColorHover() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PRORESPRICETEXTHOV, $storeScope) == null) {
            return '#000';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PRORESPRICETEXTHOV, $storeScope);
    }

    //Get the Product Result Price Label Text Color
    public function getProductResultPriceLabelTextColor() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PRORESPRICELABEL, $storeScope) == null) {
            return '#3D3D3D';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PRORESPRICELABEL, $storeScope);
    }

    //Get the Product Result Price Label Text Color
    public function getProductResultPriceLabelTextHoverColor() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        if ($this->scopeConfig->getValue(self::XML_PATH_PRORESPRICELABELHOV, $storeScope) == null) {
            return '#000';
        }
        return $this->scopeConfig->getValue(self::XML_PATH_PRORESPRICELABELHOV, $storeScope);
    }

}
