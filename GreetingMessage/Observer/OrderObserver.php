<?php
namespace Learning\GreetingMessage\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class OrderObserver implements ObserverInterface
{
public function execute(\Magento\Framework\Event\Observer $observer)
{
       	$statuscode = $observer->getEvent()->getOrder()->getStatus();
       	$statuslabel = $observer->getEvent()->getOrder()->getStatusLabel();
        
        $country="";
        $customer_name = "";
        $shipping_address = "";
        $State = "";
        
        $shippingAddress = $observer->getEvent()->getOrder()->getShippingAddress();
        $Telephone = $shippingAddress->getTelephone();
       // $shipping_address = $shippingAddress->getData();
        $email_address = $observer->getEvent()->getOrder()->getCustomerEmail();
       // $country = $shippingAddress->getCountryModel()->getName();
        $PostalCode = $shippingAddress->getPostcode();
        //$State = $shippingAddress->getState();
        $City = $shippingAddress->getCity();
        //$customer_name = $observer->getEvent()->getOrder()->getCustomerName();
        $customer_attention = "";
        $customer_id = "";
	$list = array
        (
        "$customer_id,$customer_attention,$customer_name, "
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
		$logger->info('Order Updated Status -  statuscode '.$statuscode . " statuslabel $statuslabel "); 
                
                

                $file = fopen('order_shipping_export.csv','a');  // 'a' for append to file - created if doesn't exit

                foreach ($list as $line)
                {
                    fputcsv($file,explode(',',$line));
                }

                fclose($file);
                
 
}
}