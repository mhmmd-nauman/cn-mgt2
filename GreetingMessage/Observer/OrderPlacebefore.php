<?php
namespace Learning\GreetingMessage\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class OrderPlacebefore implements ObserverInterface
{
    protected $_objectManager;
	protected $_messageManager;
	protected $_redirect;
	protected $_url;
	protected $_customerSession;
	protected $_order;
	protected $_customerrepository;
	protected $_addressRepository;
	
	//protected $jsonResultFactory;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Message\ManagerInterface $messageManager,
		//\Magento\Framework\Controller\Result\JsonFactory $jsonResultFactory
		\Magento\Framework\App\Response\RedirectInterface $redirect,
		\Magento\Framework\UrlInterface $url,
		\Magento\Customer\Api\CustomerRepositoryInterface $customerrepository,
		\Magento\Customer\Model\Session $customerSession,
		\Magento\Sales\Model\Order $order,
		\Magento\Customer\Api\AddressRepositoryInterface $addressRepository
    ) {
        $this->_objectManager = $objectManager;
		$this->_messageManager = $messageManager;
		//$this->jsonResultFactory = $jsonResultFactory;
		$this->_redirect = $redirect;
		$this->_url = $url;
		$this->_customerSession = $customerSession;
		$this->_order = $order;
		$this->_customerrepository = $customerrepository;
		$this->_addressRepository = $addressRepository;
    }
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
       	//echo  "Hello Testing ";die();
		if(1){
		//$customer = $observer->getEvent()->getCustomer();
        $name = "";//$customer->getName(); //Get customer name
        
		//$event = $observer->getEvent();
		//$orderIds = $event->getOrderIds();

		//$order = $observer->getEvent()->getOrder();
		//$ordercoll = $this->_order->loadByAttribute("entity_id",$orderIds[0]);
		//$shippingdetails = $ordercoll->getShippingAddress()->getData();
		//foreach($orderIds as $key=>$pair){
		//	$name.=" $key=>$pair <br>";
		//}
		
		//$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/customer-log.log');
		//$logger = new \Zend\Log\Logger();
		//$logger->addWriter($writer);
		//$logger->info($name);
		//$order = $observer->getEvent()->getOrder();
		$customerinfo = $this->_customerSession->getData();
		$customer = $this->_objectManager->create('Magento\Customer\Model\Customer')->load($customerinfo['customer_id']);
		foreach($customerinfo as $key=>$pair){
			//$name.=" $key=>$pair <br>";
		}
		$firstname = $customer->getFirstname();
		$lastname = $customer->getLastname();
		$dob = date("m/d/Y",strtotime($customer->getDob()));
		
		$defaultBilling = $customer->getDefaultBilling();
		$address = $this->_addressRepository->getById($defaultBilling);
		 //$address->getCollection();
		//$ab=$address->toArray();
		$zipcode = $address->getPostcode();
		$city = $address->getCity(); // street
		//$name.=$address->getState();
		//$name.=$address->getStreet();
		//$name.=$ab['street'];
		$address="";
		foreach ($customer->getAddresses() as $address1) {
			$temp_array=$address1->toArray();
			if($temp_array["entity_id"] == $defaultBilling){
				$address = $temp_array['street'];
				$country = $temp_array['country_id'];
			}
			//foreach($address1->toArray() as $k=>$v){
			//	if($k == "entity_id" && $v == $defaultBilling){
					//$name.=" $k=>$v || $defaultBilling || ";//$a['street'];
				//}
			//}
		}
		
		//foreach($address as $key=>$pair){
		//	$name.=" $key=>$pair <br>";
		//}
		//throw new \Magento\Framework\Exception\CouldNotDeleteException(__("Integrity age verification service not available! $country $firstname  $lastname $zipcode $dob $address"));
		//$this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
		//return $this;
		
		$message = "";
        //$sid=""; // test
		$sid=""; // live
		//$country = "US"; 
		//$firstname = "DAI";
		//$lastname = "MARKS";
		//$zipcode = "06067";
		//$dob = "5/24/1974";
		//$address = "3 Bridge Road Apt F";
		//Encode the data before sending  
		$country = urlencode($country);
		$lastname = urlencode($lastname);
		$firstname = urlencode($firstname);
		$zipcode = urlencode($zipcode);
		$dob = urlencode($dob);
		$address = urlencode($address);

		//Create the URL string  
		$data ="sid=$sid&zip=$zipcode&first=$firstname&last=$lastname&address=$address&dob=$dob&country=$country"; 
		$url = "https://www.integrity-direct.com/online/authentication_url.asp"; 
		$params = array(
			'http' => array( 
			'method' => 'POST', 
			'content' => $data, 
			'header' => "Content-type: application/x-www-form-urlencoded\r\n" .  "Content-Length: " . strlen ( $data ) . "\r\n"  
			)  
		);
		
		$ctx = stream_context_create($params); 
		$file = fopen($url, "r", false, $ctx); 
		if(!$file) 
		{ 
			//echo "response=404";
			$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-log.log');
			$logger = new \Zend\Log\Logger();
			$logger->addWriter($writer);
			$logger->info("response=404");
			$controller = $observer->getControllerAction();
			throw new \Magento\Framework\Exception\CouldNotDeleteException(__("Integrity age verification service not available!"));
			$this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
			return $this;
			//$result->setPath('home-page'); // Change this to what you want
			//return $result;
			//exit; 
		} 
  
		//This string will be in the format of:  
		//tid=DUA46IG5315198691298&mc=145&err_code=0&err_desc=  
		while(!feof($file)) 
		{ 
			$rs = explode("&", fgets($file, 1024)); 
			for($i = 0; $i < count($rs); $i += 1) 
			{ 
				$srs = explode("=", $rs[$i]); 
				$sname = $srs[0]; 
				$$sname = $srs[1]; 
			} 
			if($err_code == 0) 
			{ 
				if($mc != 0) 
				{ 
					$message = "response=verified"; 
				} 
				else 
				{ 
					$message = "response=notverified";
					//$this->_messageManager->addError($message);
					$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-log.log');
					$logger = new \Zend\Log\Logger();
					$logger->addWriter($writer);
					$logger->info('Order '. " before place " . " called - Integrity age verification not successfull! - $message");
					
					$controller = $observer->getControllerAction();
					throw new \Magento\Framework\Exception\CouldNotDeleteException(__("Integrity age verification not successfull!, Please enter valid Date of Birth and Address before placing order."));
					$this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
					return $this;
					
					
					//exit;
				} 
			} 
			else 
			{ 
				$message = "response=error&code=" . $err_desc; 
				$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-log.log');
				$logger = new \Zend\Log\Logger();
				$logger->addWriter($writer);
				$logger->info('Order '. " before place " . " called - Integrity age verification service failed - $message");
				$controller = $observer->getControllerAction();
				throw new \Magento\Framework\Exception\CouldNotDeleteException(__("Integrity age verification service failed!"));
				$this->actionFlag->set('', \Magento\Framework\App\Action\Action::FLAG_NO_DISPATCH, true);
				return $this;
			} 
		} 
		fclose($file);
		
		
        
		
		}           
                
                
                
	}
}