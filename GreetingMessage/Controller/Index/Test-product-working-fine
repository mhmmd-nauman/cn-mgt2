<?php
namespace Learning\GreetingMessage\Controller\Index;
class Test extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory)
	{
		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // instance of object manager
		$product = $objectManager->create('\Magento\Catalog\Model\Product');
		$product->setSku('my-sku-nauman'); // Set your sku here
		$product->setName('nauman Product 201'); // Name of Product
		$product->setAttributeSetId(4); // Attribute set id
		$product->setStatus(1); // Status on product enabled/ disabled 1/0
		$product->setWeight(10); // weight of product
		$product->setVisibility(4); // visibilty of product (catalog / search / catalog, search / Not visible individually)
		$product->setTaxClassId(0); // Tax class id
		$product->setTypeId('simple'); // type of product (simple/virtual/downloadable/configurable)
		$product->setPrice(100); // price of product
		//$product->setStoreId(1);
		//$product->setWebsiteId(1);
		$product->setStockData(
								array(
									'use_config_manage_stock' => 0,
									'manage_stock' => 1,
									'is_in_stock' => 1,
									'qty' => 99
								)
							);
		$product->setData('store_id', 1);
		$product->save();
		
		// Adding Custom option to product
		$options = array(
						array(
							"sort_order"    => 1,
							"title"         => "Custom Option 1",
							"price_type"    => "fixed",
							"price"         => "10",
							"type"          => "field",
							"is_require"   => 0
						),
						array(
							"sort_order"    => 2,
							"title"         => "Custom Option 2",
							"price_type"    => "fixed",
							"price"         => "20",
							"type"          => "field",
							"is_require"   => 0
						)
					);
		foreach ($options as $arrayOption) {
			$product->setHasOptions(1);
			$product->getResource()->save($product);
			$option = $objectManager->create('\Magento\Catalog\Model\Product\Option')
							->setProductId($product->getId())
							->setStoreId($product->getStoreId())
							->addData($arrayOption);
			$option->save();
			$product->addOption($option);
		}
		
		exit;
	}
}
