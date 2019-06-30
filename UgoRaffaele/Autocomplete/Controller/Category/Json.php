<?php

namespace UgoRaffaele\Autocomplete\Controller\Category;

class Json extends \Magento\Framework\App\Action\Action {

    private $helper;

    private $storeManager;

    private $categoryCollection;

    private $categoryModel;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \UgoRaffaele\Autocomplete\Helper\Data $helper,
        \Magento\Catalog\Model\CategoryFactory $categoryCollection,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Catalog\Model\Category $categoryModel
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->helper = $helper;
        $this->storeManager = $store;
        $this->categoryCollection = $categoryCollection;
        $this->categoryModel = $categoryModel;
    }

    public function execute() {

        $helper = $this->helper;

        if (0 === ($data = $helper->getCacheData('ugoraffaele_autocomplete_categorycollection'))) {

            $categoryCollection = $this->categoryModel->getCollection()
                ->addAttributeToSelect('name')
                ->addAttributeToSelect('default_sort_by')
                ->addAttributeToSort('position', 'desc')
                ->addAttributeToFilter('is_active', [ 'in' =>  [ 1 ] ])
                ->addAttributeToFilter('level', [ 'in' =>  [ 2, 3, 4, 5 ] ])
                ->load()->toArray();

            $categoryarray =  [];
            foreach ($categoryCollection as $categoryId => $category) {

                if ($category ['parent_id'] == $this->storeManager->getStore()->getRootCategoryId()) {

                    $categoryarray[$categoryId] = [
                        'name' => $category ['name'],
                        'type_id' => '',
                        'sku' => '',
                        'image' => $this->getCategoryImages($categoryId),
                        'url_path' => $this->getCategoryUrl($categoryId),
                        'min_price' => '',
                        'price' => '',
                        'final_price' => '',
                        'max_price' => '',
                        'type' => 'category'
                    ];
                
                } else {

                    $categoryarray[$categoryId] = [
                        'name' => $this->getCategoryName($category ['parent_id']).' > '.$category ['name'],
                        'type_id' => '',
                        'sku' => '',
                        'image' => $this->getCategoryImages($categoryId),
                        'url_path' => $this->getCategoryUrl($categoryId),
                        'min_price' => '',
                        'price' => '',
                        'final_price' => '',
                        'max_price' => '',
                        'type' => 'category'
                    ];

                }

            }

            $finalcategoryarray = [];
            $count = 0;
            foreach ($categoryarray as $key => $value) {
                $finalcategoryarray[$count] = $categoryarray[$key];
                $count++;
            }

            $data = json_encode($finalcategoryarray);
            $lifetime = $helper->getCacheLifetime();
            $helper->storeCacheData('ugoraffaele_autocomplete_categorycollection', $data);

        }

        $this->getResponse()
            ->setHeader('Content-Type', 'application/json')
            ->setBody($data);

    }

    private function loadCategory($category) {
        return $this->categoryCollection->create()->load($category);
    }

    private function getCategoryImages($entity_id) {
        $category = $this->loadCategory($entity_id);
        return $category->getImageUrl();
    }

    private function getCategoryUrl($entity_id) {
        $category = $this->loadCategory($entity_id);
        return $category->getUrl();
    }

    private function getCategoryName($entity_id) {
        $category = $this->loadCategory($entity_id);
        return $category->getName();
    }

}
