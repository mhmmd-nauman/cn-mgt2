<?php
namespace Learning\GreetingMessage\Controller\Index;
use \Magento\Framework\App\Bootstrap;
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
		
		
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
 
		$CategoryLinkRepository = $objectManager->get('\Magento\Catalog\Api\CategoryLinkManagementInterface');
		 
		$category_ids = array('6','8');
		$sku = 'my-sku-nauman';
		 
		$CategoryLinkRepository->assignProductToCategories($sku, $category_ids);
		
		exit;
	}
}
