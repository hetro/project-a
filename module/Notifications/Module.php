<?php
namespace Notifications;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;


class Module implements AutoloaderProviderInterface, ConfigProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
	
	public function onBootstrap(\Zend\EventManager\EventInterface $e){
		$app = $e->getParam('application');
		$sm = $app->getServiceManager();
		$auth = $sm->get('zfcuser_auth_service');
		
		if($auth->hasIdentity()){
			
			$user_id =  $auth->getIdentity()->getId();
			$objectManager = $sm->get('Doctrine\ORM\EntityManager');
			
			// OUTOFSTOCK
			$query = $objectManager->createQuery("SELECT e FROM Event\Entity\Inventory e WHERE e.stock < 10 AND e.type = 'Consumable'");
			$outofstock = $query->getResult();
			
			$repository = $objectManager->getRepository('Notifications\Entity\Notifications');
			$validator = new \DoctrineModule\Validator\ObjectExists(array(
				'object_repository' => $repository ,
				'fields' => array('message','notifywho','linkroute')
			));
			
			foreach($outofstock as $v){
				
				$message = 'Item '.$v->getName().'#'.$v->getId().' <code>is out of stock</code>';
				$notifywho = 'custodian';
				$linkroute = 'inventory:update-stock:'.$v->getId();
				
				if(!$validator->isValid(array($message,$notifywho,$linkroute))){
					$notify = new \Notifications\Entity\Notifications();
					$notify->setMessage($message);
					$notify->setNotifywho($notifywho);
					#$notify->setNotifyuser(null);
					$notify->setLinkroute($linkroute);
		
					$objectManager->persist($notify);
				}
			}
		
			// EXPIRED DATEOFRETURN
			$now = new \DateTime("now");
			
			$query = $objectManager->createQuery("SELECT i FROM Event\Entity\InventoryRequest i JOIN i.inventory l WITH l.type != 'Can be borrowed' AND i.dateofreturn < '".$now->format('Y-m-d')."' AND i.status = 'Approved'");
			$expired = $query->getResult();
			
			foreach($expired as $v){
				
				$message = $v->getUser()->getDisplayName().' "'.$v->getDescription().'" '.$v->getInventory().'#'.$v->getInventory()->getId().' <code>date of return has expired</code>';
				$notifywho = 'custodian';
				$linkroute = 'request:returnRequest:'.$v->getId();
				
				if(!$validator->isValid(array($message,$notifywho,$linkroute))){
					$notify = new \Notifications\Entity\Notifications();
					$notify->setMessage($message);
					$notify->setNotifywho($notifywho);
					#$notify->setNotifyuser(null);
					$notify->setLinkroute($linkroute);
		
					$objectManager->persist($notify);
				}
			}
			
			$objectManager->flush();
			
			
			// GET TOTAL NOTIFICATION
			$authorize = $sm->get('BjyAuthorize\Provider\Identity\ProviderInterface');
			$roles = $authorize->getIdentityRoles();
			
			#$tmp = $objectManager->getRepository('Notifications\Entity\Notifications')->findBy( array('notifywho' => $roles[0]) );
			
			
			$viewModel = $app->getMvcEvent()->getViewModel();
			
			
			$query = $objectManager->createQuery("SELECT r FROM Notifications\Entity\Notifications r WHERE r.notifywho = '".$roles[0]."' OR r.notifyuser = ".$user_id);
			$list = $query->getResult();
			
			$viewModel->notifications_count = count($list);
		}
	}

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
	public function getServiceConfig(){
		return array(
			'factories' => array(
				
			),
		);	
	}
}