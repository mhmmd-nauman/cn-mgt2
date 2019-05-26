<?php
namespace Learning\GreetingMessage\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class OrderObserver implements ObserverInterface
{
public function execute(\Magento\Framework\Event\Observer $observer)
{
       	$statuscode = $observer->getEvent()->getOrder()->getStatus();
       	$statuslabel = $observer->getEvent()->getOrder()->getStatusLabel();
		
		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/order-log.log');
		$logger = new \Zend\Log\Logger();
		$logger->addWriter($writer);
		$logger->info('Order Updated Status -  statuscode '.$statuscode . " statuslabel $statuslabel ");  
 
}
}