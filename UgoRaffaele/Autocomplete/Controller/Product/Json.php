<?php

namespace UgoRaffaele\Autocomplete\Controller\Product;

class Json extends \Magento\Framework\App\Action\Action {

	private $helper;

	private $store;

	private $productCollection;

	private $image;

	private $product;

	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory,
		\UgoRaffaele\Autocomplete\Helper\Data $helper,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
		\Magento\Store\Model\StoreManagerInterface $store,
		\Magento\Catalog\Helper\Image $image,
		\Magento\Catalog\Model\ProductFactory $product
	) {
		parent::__construct($context);
		$this->resultPageFactory = $resultPageFactory;
		$this->helper = $helper;
		$this->productCollection = $productCollection;
		$this->store = $store;
		$this->image = $image;
		$this->product = $product;
	}

	public function execute() {

		$helper = $this->helper;

		if (0 === ($data = $helper->getCacheData('ugoraffaele_autocomplete_productcollection'))) {

			$productCollection = $this->productCollection;

			$collection = $productCollection->create()
				->addAttributeToSelect('*')
				->addAttributeToSort('entity_id', 'DESC')
				->load();

			$collection->joinAttribute('status', 'catalog_product/status', 'entity_id', null, 'inner');
			$collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner');
			$collection->addWebsiteNamesToResult();
			$collection->addFinalPrice();
			$collection->applyFrontendPriceLimitations();

			$storeManager = $this->store;
			$productarray = [];
			$count = 0;
			$width = 150;
			$height = null;

			foreach ($collection as $value) {

				$imageUrl = $this->image->init($value, 'product_page_image_small')
					->setImageFile($value->getFile())
					->getUrl();

				if ($value->getStatus() == 1 && $value->getVisibility() == \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH) {
					$product = $this->loadProduct($value->getData('entity_id'));
					if ($value->getTypeId() == 'configurable') {
						$productarray[$count] = $this->getConfigProductData($value, $product, $imageUrl);
					} else {
						$productarray[$count] = $this->getSimpleProductData($value, $product, $imageUrl);
					}
					$count++;
				}

			}

			$data = json_encode($productarray);
			$lifetime = $helper->getCacheLifetime();
			$helper->storeCacheData('ugoraffaele_autocomplete_productcollection', $data);

		}

		$this->getResponse()
			->setHeader('Content-Type', 'application/json')
			->setBody($data);

	}

	private function loadProduct($entityid) {
		return $this->product->create()->load($entityid);
	}

	private function getConfigProductData($value, $product, $imageUrl) {
		$additional['_escape'] = true;
		return [
			'entity_id' => $value->getData('entity_id'),
			//'attribute_set_id' => $value->getData('attribute_set_id'),
			'type_id' => $value->getData('type_id'),
			'sku' => $value->getData('sku'),
			//'has_options' => $value->getData('has_options'),
			//'required_options' => $value->getData('required_options'),
			'created_at' => $value->getData('created_at'),
			'updated_at' => $value->getData('updated_at'),
			'name' => $value->getData('name'),
			'image' => $imageUrl,
			'url_path' => $product->getUrlModel()->getProductUrl($product, $additional),
			'price' => $product->getFinalPrice(),
			//'tax_class_id' => $product->getTaxId(),
			//'minimal_price' => $product->getFinalPrice(),
			//'min_price' => $product->getFinalPrice(),
			//'max_price' => $product->getFinalPrice(),
			//'tier_price' => $product->getTierPrice(),
		];
	}

	private function getSimpleProductData($value, $product, $imageUrl) {
		$additional['_escape'] = true;
		return [
			'entity_id' => $value->getData('entity_id'),
			//'attribute_set_id' => $value->getData('attribute_set_id'),
			'type_id' => $value->getData('type_id'),
			'sku' => $value->getData('sku'),
			//'has_options' => $value->getData('has_options'),
			//'required_options' => $value->getData('required_options'),
			'created_at' => $value->getData('created_at'),
			'updated_at' => $value->getData('updated_at'),
			'name' => $value->getData('name'),
			'image' => $imageUrl,
			'url_path' => $product->getUrlModel()->getProductUrl($product, $additional),
			'price' => $product->getPriceInfo()->getPrice('regular_price')->getValue(),
			'final_price' => $product->getPriceInfo()->getPrice('final_price')->getValue(),
			//'tax_class_id' => $value->getTaxId(),
			//'minimal_price' => $value->getSpecialPrice(),
			//'min_price' => $value->getSpecialPrice(),
			//'max_price' => $value->getPrice(),
			//'tier_price' =>$value->getTierPrice(),
		];
	}

}
