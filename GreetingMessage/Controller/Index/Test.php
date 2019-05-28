<?php
namespace Learning\GreetingMessage\Controller\Index;
use \Magento\Framework\App\Bootstrap;
use \Learning\GreetingMessage\Service;
use Magento\Framework\App\Filesystem\DirectoryList;

class Test extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
        protected $_importimageservice;
        
        protected $_stockStateInterface;
        protected $_stockRegistry;
        
	public function __construct(
		\Magento\Framework\App\Action\Context $context,
                \Magento\Framework\View\Result\PageFactory $pageFactory,
                \Learning\GreetingMessage\Service\ImportImageService $importimageservice,
                \Magento\CatalogInventory\Api\StockStateInterface $stockStateInterface,
                \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
                \Magento\Catalog\Model\ProductFactory $productFactory
                )
	{
                $this->_importimageservice = $importimageservice;
		$this->_pageFactory = $pageFactory;
                
                $this->_stockStateInterface = $stockStateInterface;
                $this->_stockRegistry = $stockRegistry;
                
                $this->productFactory = $productFactory;
                
		return parent::__construct($context);
	}

	

	public function execute()
	{
            exit();
            //ready object manager
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            
            // ready the logger 
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/product-add-log.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            
            // try to connect to local db for new products from bcp api
            define("SERVER", "localhost");
            define("USER", "root");
            define("PASSWORD", "");
            define("DB", "cn");
            //connection to the database
            $conn = mysqli_connect(SERVER, USER, PASSWORD) or die ('Error connecting to mysql'); 
            mysqli_select_db($conn, DB)or die ('Error selecting to db');
		
            $query = "SELECT  * FROM `api_product_new` WHERE final_script_run = '0' limit 0,2; ";
            $result = $conn->query($query) or die($conn->error.__LINE__);
            if($result->num_rows > 0 ){ 
                while($prod_data = $result->fetch_assoc()) 
                {
                    $checksku = 'SKU'.$prod_data['product_id'];
                    $array[]= 'SKU'.$prod_data['product_id'];
                    $product_ap = $prod_data['product_price']; /* Product Advertised Price */	
                    $productId = $objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($checksku);
                    $product_quantity = $prod_data['quantity']; /* Product Quantity */
                    if(!$productId)
                    {
                            //$product_availability = $product_data['availability'];
                            $product_sku = 'SKU'.$prod_data['product_id']; /* Product SKU */ 
                            $Arrreport[] = 'SKU'.$prod_data['product_id']; /* Product SKU */ 
                            $product_name = $prod_data['product_name']; /* Product Name */
                            $product_flavor = $prod_data['flavor']; /* Product Flavor */
                            $product_brand = $prod_data['brand']; /* Product Brand */
                            $product_pack = $prod_data['packaging']; /* Product Pack */
                            $product_count = $prod_data['count']; /* Product Count */
                            $product_msrp = explode('$',$prod_data['MSRP']); /* Product MSRP */
                            $product_length = $prod_data['length']; /* Product length */
                            //$product_msr = $product_msrp[1];
                            $product_msr = 100; //$product_msrp[1];
                            $product_ap = $prod_data['product_price']; /* Product Advertised Price */		
                            $product_wrapper = $prod_data['wrapper']; /* Product Wrapper */	
                            $product_strength = $prod_data['strength']; /* Product Strength */	
                            $product_shape = $prod_data['shape']; /* Product Shape */	
                            $product_origin = $prod_data['origin']; /* Product Origin */	
                            $product_description = $prod_data['product_description']; /* Product Description */	
                            	
                            $product_gauge = $prod_data['diameter']; 
                            $product_image = $prod_data['product_image']; 
                            $product_api = "Yes";
                            $sku = $product_sku;			
                            if($prod_data['brand'] !=="NULL" ) 
                            { 
                                $combine = 'Cigar Brand :'.$prod_data['brand'].'<br><br>'; //Set Description Brand Line here
                            }
                            if($prod_data['diameter'] !=="NULL") 
                            { 
                                $combine .= 'Cigar Diameter :'.$prod_data['diameter'].'<br><br>'; //Set Description diameter Line here
                            }
                            if($prod_data['length'] !=="NULL" ) 
                            { 
                                $combine .= 'Cigar Length :'.$prod_data['length'].'<br><br>'; //Set Description Length Line here
                            }
                            if($prod_data['wrapper'] !=="NULL") 
                            { 
                                $combine .= 'Wrapper :'.$prod_data['wrapper'].'<br><br>';   //Set Description Wrapper Line here
                            }
                            if($prod_data['shape']!=="NULL") 
                            { 
                                $combine .= 'Shape :'.$prod_data['shape'].'<br><br>';    //Set Description shape Line here
                            }
                            if($prod_data['flavor'] !=="NULL" ) 
                            {
                                $combine .= 'Cigar Flavor :'.$prod_data['flavor'].'<br><br>';   //Set Description flavor Line here
                            }
                            if($prod_data['origin'] !=="NULL" ) 
                            {
                                $combine .= 'Origin :'.$prod_data['origin'].'<br><br>';     //Set Description origin Line here
                            }
                            if($prod_data['packaging'] !=="NULL" ) 
                            {
                                $combine .= 'Packaging Type :'.$prod_data['packaging'].'<br>';  //Set Description packaging Line here
                            }
                            //$checksku = trim($product_key);
                            $product = $objectManager->create('\Magento\Catalog\Model\Product'); 
                            $product->setSku($product_sku); 
                            $product->setAttributeSetId('4');# 4 is for default 
                            $product->setTypeId('simple'); 
                            $product->setStoreId(1);			
                            $product->setStatus(1);	
                            $product->setName(trim($product_name));
                            $product->setDescription(trim(addslashes($combine)));
                            $product->setShortDescription(trim($product_description ));
                            $product->setWeight(0.00);
                            $product->setQty($prod_data['quantity']);
                            $product->setWebsiteIds(array(1));
                            //Some db calls to get $sku and $qty and start the loop
                            $product->setTaxClassId(2);
                            ////tax class (0 - none, 1 - default, 2 - taxable, 4 - shipping)
                            $product->setPrice($product_ap); # Set some price
                            $product->setVisibility(4);
                            try{
                                $product->save();
                            } catch(Exception $e){
                                echo $e->getMessage();
                            }
                            
                            if($product_count != '' || $product_count != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("count");
                                if($attrb->usesSource()) 
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId($product_count); 
                                }
                                if(empty($attrb_option_id))$attrb_option_id=$product_count;
                                $product->setData('count',$attrb_option_id);     //Set Attribute  Count Option here
                                $product->save();
                                $attrb_option_id="";
                            }
                            if($product_flavor != '' || $product_flavor != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("flavor");
                                if($attrb->usesSource()) 
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId($product_flavor);  

                                }
                                $product->setData('flavor',$attrb_option_id);          //Set Attribute  Flavor Option here
                                $product->save();
                            }
                            if($product_strength != '' || $product_strength != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("strength");      
                                if($attrb->usesSource()) 
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId(trim($product_strength)); 
                                }
                                $product->setData('strength',$attrb_option_id);      //Set Attribute  Strength Option here
                                $product->save();
                            }

                            if($product_pack != '' || $product_pack != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("packaging");
                                if($attrb->usesSource())
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId(trim($product_pack)); 
                                }
                                $product->setData('packaging',$attrb_option_id);        //Set Attribute  Packaging Option here
                                $product->save();
                            }
                            if($product_wrapper != '' || $product_wrapper != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("wrapper");
                                if($attrb->usesSource())
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId(trim($product_wrapper)); 
                                }
                                $product->setData('wrapper',$attrb_option_id);   //Set Attribute  Wrapper Option here
                                $product->save();
                            }
                            if($product_shape != '' || $product_shape != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("shape");
                                if($attrb->usesSource())
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId(trim($product_shape)); 
                                }
                                $product->setData('shape',$attrb_option_id);         //Set Attribute  Shape Option here
                                $product->save();
                            }
                            if($product_origin != '' || $product_origin != null )
                            {
                                $attrb = $product->getResource()->getAttribute("origin");
                                if($attrb->usesSource()) 
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId(trim($product_origin)); 
                                }
                                $product->setData('origin',$attrb_option_id);   //Set Attribute  Origin Option here
                                $product->save();
                            }
                            if($product_brand != '' || $product_brand != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("manufacturer_new");
                                if($attrb->usesSource()) 
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId(trim($product_brand)); 
                                }
                                $product->setData('manufacturer_new',$attrb_option_id);    //Set Attribute  Brand or Manufacturer_new Option here
                                $product->save();
                            }
                            if($product_length != '' || $product_length != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("length");
                                if($attrb->usesSource()) 
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId($product_length);
                                }
                                $product->setData('length',$attrb_option_id);        //Set Attribute  Length Option here
                                $product->save();
                            }
                            if($product_gauge != '' || $product_gauge != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("gauge");
                                if($attrb->usesSource()) 
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId($product_gauge); 
                                }
                                $product->setData('gauge',$attrb_option_id);          //Set Attribute  gauge Option here
                                $product->save();
                            }
                            if($product_api != '' || $product_api != null ) 
                            {
                                $attrb = $product->getResource()->getAttribute("apivalue");
                                if($attrb->usesSource()) 
                                { 
                                    $attrb_option_id = $attrb->getSource()->getOptionId($product_api); 

                                }
                                $product->setData('apivalue',$attrb_option_id);          //Set Attribute  gauge Option here
                                $product->save();
                            }
                            
                            // adding images to product 
                            $imagePath = $product_image; // path of the image
                            $this->_importimageservice->execute($product, $imagePath,true,array('image', 'small_image', 'thumbnail'));
                           // $tmpDir = $this->getMediaDirTmpDir();
                           // $product->addImageToMediaGallery($imagePath, array('image', 'small_image', 'thumbnail'), false, false);
                            $product->save();
                            
                            
                            // try stock again
                            $stockItem = $this->_stockRegistry->getStockItemBySku($product_sku);
                            $stockItem->setQty($product_quantity);
                            $this->_stockRegistry->updateStockItemBySku($product_sku, $stockItem);
                            
                            // enable disable product
                           // $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                           // $product->save();
                            
                            //$prodLoad = $objectManager->get('Magento\Catalog\Model\Product');
                            //$prodID = $objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($product_sku);
                            //if ($prodID != '' || $prodID != null )
                           // {	
                                
                               // $stockItem=$this->_stockRegistry->getStockItem($prodID);
                               // $stockItem->setData('is_in_stock',1); //set updated data as your requirement
                               // $stockItem->setData('qty',$product_quantity); //set updated quantity 
                                //$stockItem->setData('manage_stock',1);
                               // $stockItem->setData('use_config_notify_stock_qty',1);
                                //$stockItem->save(); //save stock of item
                                //$prodLoad->save(); //  also save product
                                //Mage::log('New Product Insert Updated'.$prodID , null, 'scriptreport.log');
                                $logger->info('New Product Insert -  sku '.$product_sku . " qty $product_quantity ");  
                           // }
                           // exit;
                     }else{
                        // product exists we need to update the quantity 
                        $product_sku = $checksku;
                        $stockItem = $this->_stockRegistry->getStockItemBySku($product_sku);
                        $stockItem->setQty($product_quantity);
                        $this->_stockRegistry->updateStockItemBySku($product_sku, $stockItem);
                         
                        $logger->info("Update Existing Product Quantity - sku ".$checksku." - qty $product_quantity" );
                     }
                    //
                    //update final script 
                     $updatequery = 'UPDATE `api_product_new` SET final_script_run = "1" WHERE product_id = '.$prod_data['product_id'];
                     $conn->query($updatequery) or die($conn->error.__LINE__);
                }
            }
            
            // enable disable products
            $query = "SELECT  * FROM `api_product_new` WHERE 1 ; ";
            $result = $conn->query($query) or die($conn->error.__LINE__);
            if($result->num_rows > 0 ){ 
                while($prod_data = $result->fetch_assoc()){
                    $products_in_api[]= 'SKU'.$prod_data['product_id'];
                }
            }
            if(count($products_in_api)>0) {
                //exit;
                $productCollection = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\CollectionFactory');
                $collection = $productCollection->create()
                ->addAttributeToSelect('*')
                ->load();
                foreach ($collection as $_product)
                {				
                    $productsku[] = $_product->getSku();
                }
                //echo "from magento ";
               // echo "<pre>";
                //print_r($productsku);
                //echo "</pre>";
                 
                foreach($productsku as $sku)
                {
                    
                    $productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
                    $product = $productRepository->get($sku);
                    
                    if(in_array($sku, $products_in_api))
                    {
                        
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);
                        $product->save();
                        $logger->info("Enabled Product - sku ".$sku." " );
                    }
                    else
                    {
                        $product->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED);
                        $product->save();
                        $logger->info("Disabled Product - sku ".$sku." " );

                    }
                }
            }
            
            
            exit;
		
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
