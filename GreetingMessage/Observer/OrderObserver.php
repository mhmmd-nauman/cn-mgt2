<?php
namespace Learning\GreetingMessage\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class OrderObserver implements ObserverInterface
{
    protected $_objectManager;
    protected $_checkoutSession;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Sales\Model\Order $order,
        \Magento\Checkout\Model\Session $checkoutSession
    ) {
        $this->_objectManager = $objectManager;
        $this->order = $order;
        $this->_checkoutSession = $checkoutSession;
    }
public function execute(\Magento\Framework\Event\Observer $observer)
{
       	$statuscode = $observer->getEvent()->getOrder()->getStatus();
       	$statuslabel = $observer->getEvent()->getOrder()->getStatusLabel();
        
        $country="";
        $customer_name = "";
        $shipping_address = "";
        $State = "";
        $customer_id = "";
        $shippingAddress = $observer->getEvent()->getOrder()->getShippingAddress();
        $Telephone = $shippingAddress->getTelephone();
       // $shipping_address = $shippingAddress->getData();
        $email_address = $observer->getEvent()->getOrder()->getCustomerEmail();
       // 
        $PostalCode = $shippingAddress->getPostcode();
        //$State = $shippingAddress->getState();
        $City = $shippingAddress->getCity();
        //$customer_name = $observer->getEvent()->getOrder();
        $order = $observer->getEvent()->getOrder();
        $orderId = $order->getIncrementId();
        $order_info = $this->_objectManager->create('Magento\Sales\Model\Order')->load($orderId);
        $customer_id = $order_info->getCustomerId();
        //$shippingAddressObj = $order_info->getShippingAddress();
        $shippingAddressObj = $this->_checkoutSession->getQuote()->getShippingAddress();
        $shippingAddressArray = $shippingAddressObj->getData();
        $firstname = $shippingAddressArray['firstname'];
        $lastname = $shippingAddressArray['lastname'];
        $customer_name = $firstname." ".$lastname;
        //$telephone = $shippingAddressArray['telephone'];
        $street = $shippingAddressArray['street'];
        //$city = $shippingAddressArray['city'];
        $country = $shippingAddressObj->getCountryModel()->getName();
       // $email = $shippingAddressArray['email'];
        $State = $shippingAddressArray['region'];

       // $PostalCode = $shippingAddressArray['postcode'];
        $shipping_address = $street;
        $customer_attention = "";
        
	$list = array
        (
        "$customer_id , $customer_attention,$customer_name, "
                . " $shipping_address,$City ,$State , $PostalCode ,"
                . " $country, $email_address, $Telephone ",
        );
        //$order->getCustomerEmail();
        // ->getShippingAddress()->getData()
        // $shippingAddress = $order->getShippingAddress();
        // $shippingAddress->getTelephone()
        // $shippingAddress->getPostcode()
        //$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        //$order= $objectManager->get('\Magento\Sales\Model\Order');
       // $orderId=161;//your order id is here
        //$orderData=$order->load($orderId);
        
        
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-log.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Order Updated Status -  statuscode '.$statuscode . " statuslabel $orderId "); 
                
                

                $file = fopen('order_shipping_export.csv','a');  // 'a' for append to file - created if doesn't exit

                foreach ($list as $line)
                {
                    fputcsv($file,explode(',',$line));
                }

                fclose($file);
                
 
}
}