<?php
namespace Event;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

use Event\UserRole\Service\UserRoleService;

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

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
	
	public function onBootstrap(\Zend\EventManager\EventInterface $e){
		$app = $e->getParam('application');
		// $em is a Zend\EventManager\SharedEventManager
		$em  = $app->getEventManager()->getSharedManager();
		
		$sm = $app->getServiceManager();
		$userService = $sm->get('zfcuser_user_service');
		$userService->getEventManager()->attach('register.post', 
            function(\Zend\EventManager\Event $e) use ($sm) {
				//get user entity from event params
				$user = $e->getParam('user');
				//this is my own "userrole" service, you can target to "userrolelinker" service
				$userRoleService = $sm->get('UserRole\Service\UserRoleService');
				//below line is actually calling the insert functionallity via service
				//if you would like can also be done by "mapper" class also without 
				//the involvement of "service" class 
				$userRoleService->insertUserRole(array(
					'user_id' => $user->getId(),
					'role_id' => 1 //my target role id for the new users
				));
			});	
		#$notifications = $sm->get('UserRole\Service\NotificationsService');
		$viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
		$viewModel->x = 'Hello';
	}
	
	public function getServiceConfig(){
		return array(
			'factories' => array(
				'UserRole\Service\UserRoleService' => function($sm){
					$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
					$userRoleService = new UserRoleService($dbAdapter);
					return $userRoleService;
				},
			),
		);	
	}
}