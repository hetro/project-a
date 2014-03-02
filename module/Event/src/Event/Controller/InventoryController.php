<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

// @CUSTODIAN

namespace Event\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class InventoryController extends AbstractActionController
{
	
	public function updateStockAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		$error = array();
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$id = (int) $this->params()->fromRoute('id', 0);
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		
		
		$inventory = $objectManager->getRepository('Event\Entity\Inventory')->find( $id );
	
		
	
		if ($this->request->isPost()) {
			if($this->request->getPost('update') == 'Update'){
				$amount = (int)$this->request->getPost('amount');
				if(empty($amount) || $amount == 0) $error[] = "Amount is empty.";
				if($this->request->getPost('type') == 'out')
				$amount = $amount * -1;
				$newStock = $inventory->getStock() + $amount;
				
				if($newStock >= 0){
					if(count($error) == 0){
						$inventoryLog = new \Event\Entity\InventoryLog();
						$inventoryLog->setInventory( $inventory );
						$inventoryLog->setUser( $user );
						$inventoryLog->setCurrentStock( $inventory->getStock() );
						$inventoryLog->setDescription( $this->request->getPost('description') );
						$inventoryLog->setDate(new \DateTime("now"));
						
						
						
						$inventoryLog->setStock( $amount  );
						$objectManager->persist( $inventoryLog );
						
						
						
						$inventory->setStock( $newStock );
						
						$notification = $objectManager->getRepository('Notifications\Entity\Notifications')->findOneBy(array('linkroute' => 'inventory:update-stock:'.$id));
				
						if($notification) $objectManager->remove($notification);
						$objectManager->flush();
						return $this->redirect()->toRoute('inventory/action',array('action' => 'view', 'id' => $id));	
					}
				}
				else $error[] = 'Stock cannot be negative';
				
				
			}
			
			
		}
		
		return array( 'inventory' => $inventory , 'error' => $error );
	}
	
    public function indexAction()
    {
		
		#if(!$this->isAllowed('inventory')) return $this->redirect()->toRoute('home');
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$list = $objectManager->getRepository('Event\Entity\Inventory')->findAll();
		
        return new ViewModel( array( 'list' => $list) );
    }
	
	public function requestAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		
		
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
			
		// Create the form and inject the ObjectManager
		$form = new \Event\Form\InventoryRequestForm($objectManager);
	
		// Create a new, empty entity and bind it to the form
		$inventory = new \Event\Entity\InventoryRequest();
		$inventory->setUser( $user );
		$objectManager->persist( $inventory );
		$form->bind($inventory);
	
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
			
			$tmp = $objectManager->getRepository('Event\Entity\Inventory')->find( $form->get('item-request')->get('inventory')->getValue() );
			$error = 0;
			if ($tmp) if($form->get('item-request')->get('amount')->getValue() > $tmp->getStock()){
				$form->get('item-request')->get('amount')->setMessages(array('Amount must not be greater than '.$tmp->getStock() ));
				$error++;
			}
	
			if ($form->isValid() && $error == 0) {
				
				
				//Notifications
				$message = 'New Inventory Request "'.$inventory->getInventory().'#'.$inventory->getInventory()->getId().'"';
				$notify = new \Notifications\Entity\Notifications();
				$notify->setMessage( $message );
				$notify->setNotifywho( 'admin' );
		
				$objectManager->persist($notify);
				
				// Save the changes
				$objectManager->flush();
				
				
				if($this->isAllowed('inventory', 'view')) return $this->redirect()->toRoute('inventory');
				else return $this->redirect()->toRoute('zfcuser');
			}
		}
        return new ViewModel(array('form' => $form));
	}
	
	public function addAction()
    {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
			
		// Create the form and inject the ObjectManager
		$form = new \Event\Form\AddInventoryForm($objectManager);
	
		// Create a new, empty entity and bind it to the form
		$inventory = new \Event\Entity\Inventory();
		$inventory->setUser( $user );
		$objectManager->persist( $inventory );
		$form->bind($inventory);
	
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
	
			if ($form->isValid()) {
				$inventoryLog = new \Event\Entity\InventoryLog();
				$inventoryLog->setInventory( $inventory );
				$inventoryLog->setUser( $user );
				$inventoryLog->setDescription( '~ Init' );
				$inventoryLog->setDate(new \DateTime("now"));
				$inventoryLog->setStock( $inventory->getStock() );
				$objectManager->persist( $inventoryLog );
				
				// Save the changes
				$objectManager->flush();
				
				return $this->redirect()->toRoute('inventory/action', array( 'action' => 'view' , 'id' => $inventory->getId() ));
			}
		}
        return new ViewModel(array('form' => $form));
    }
	
	public function editAction()
    {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		
		$id = (int) $this->params()->fromRoute('id', 0);
		
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
			
		// Create the form and inject the ObjectManager
		$form = new \Event\Form\AddInventoryForm($objectManager);
		$form->setValidationGroup(array('inventory' => array('name','type')));
	
		// Create a new, empty entity and bind it to the form
		$inventory = $objectManager->getRepository('Event\Entity\Inventory')->find( $id );
		$form->bind($inventory);
	
		if ($this->request->isPost()) {
			$form->setData($this->request->getPost());
	
			if ($form->isValid()) {
				
				// Save the changes
				$objectManager->flush();
				
				return $this->redirect()->toRoute('inventory');
			}
			else print_r($form->getMessages());
		}
        return new ViewModel(array('form' => $form));
    }
	
	public function viewAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		$id = (int) $this->params()->fromRoute('id', 0);
		$entry = $objectManager->getRepository('Event\Entity\Inventory')->find( $id );
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$logs = $objectManager->getRepository('Event\Entity\InventoryLog')->findBy( array( 'inventory' => $id ) );
		return new ViewModel(array('logs' => $logs, 'entry' => $entry ));
	}
}
