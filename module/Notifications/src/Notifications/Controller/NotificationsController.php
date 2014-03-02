<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Notifications\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class NotificationsController extends AbstractActionController
{
	public function indexAction(){
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		$authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
		$roles = $authorize->getIdentityRoles();
		
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$query = $objectManager->createQuery("SELECT e FROM Notifications\Entity\Notifications e WHERE e.notifywho = '".$roles[0]."' OR e.notifyuser = ".$user_id." ORDER BY e.id DESC");
		$notifications = $query->getResult();
		
		/*$query = $objectManager->createQuery("SELECT e FROM Event\Entity\Inventory e WHERE e.stock = 0");
		$outofstock = $query->getResult();*/
		
		/*$repository = $objectManager->getRepository('Notifications\Entity\Notifications');
		$validator = new \DoctrineModule\Validator\ObjectExists(array(
			'object_repository' => $repository,
			'fields' => array('notifywho')
		));
		
		$test = $objectManager->getRepository('Event\Entity\Event')->find( 1 );
		
		var_dump($validator->isValid(array('custodian','inventory:edit:20')));*/
	
		return array( 'notifications' => $notifications );
	}
	
	
	public function deleteAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			echo json_encode(array('error' => 'Error: Not logged in'));
			return $this->response;
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$id = (int) $this->request->getPost('id',0);
		$notification = $objectManager->getRepository('Notifications\Entity\Notifications')->find( $id );
		#$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		#$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		if($notification){
			$objectManager->remove( $notification );
			$objectManager->flush();
		}
		return $this->response;
	}
	
}
