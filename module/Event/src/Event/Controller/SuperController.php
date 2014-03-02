<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Event\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class SuperController extends AbstractActionController
{
	public function indexAction(){
	
	}
	
	public function manageAccountsAction(){
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$query = $objectManager->createQuery("SELECT r FROM Event\Entity\User r WHERE r.user_id != ".$user_id);
		$accounts = $query->getResult();
		
		return array( 'accounts' => $accounts );
	}
	
	public function deleteAccountAction(){
		$id = (int) $this->params()->fromRoute('id', 0);
		
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $id );
		
		if ($this->request->isPost()) {
			
				$delete = $this->getRequest()->getPost('delete', 'No');
				if ($delete == 'Yes') {
					$objectManager->remove($user);
					
					// Save the changes
					$objectManager->flush();	
				}
				
				return $this->redirect()->toRoute('super/action', array('action' => 'manage-accounts'));
		}
	}
	
	public function updateAccountAction(){
		$id = (int) $this->params()->fromRoute('id', 0);
		
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $id );
		
		$db  = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		
		$statement = $db->createStatement("SELECT * FROM `user_role`");
		$user_role = $statement->execute();
		
		$statement = $db->createStatement("SELECT * FROM `user_role_linker` WHERE `user_id` = ".$user->getUserId());
		$result = $statement->execute();
		$row = $result->current();
		
		$role_id = $row['role_id'];
		
		if ($this->request->isPost()) {
			
				$update = $this->getRequest()->getPost('update', 'Cancel');
				if ($update == 'Update') {
					$new_role = $this->getRequest()->getPost('user_role', 0);
					
					if($new_role){
						$statement = $db->createStatement("UPDATE `user_role_linker` SET `role_id` = $new_role WHERE `user_id` = ".$user->getUserId());
						$statement->execute();
					}
				}
				
				return $this->redirect()->toRoute('super/action', array('action' => 'manage-accounts'));
		}
		
		return array('role_id' => $role_id , 'user_role' => $user_role , 'user'=> $user);
	}
}
