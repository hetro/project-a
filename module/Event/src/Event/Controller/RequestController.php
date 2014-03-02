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

class RequestController extends AbstractActionController
{
	public function returnAction()
    {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$id = (int) $this->params()->fromRoute('id', 0);
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		
		$entry = $objectManager->getRepository('Event\Entity\InventoryRequest')->find( $id );
		
		$error = array();
		
		
		if ($this->request->isPost()) {
			if($this->request->getPost('update') == 'Set as returned'){
				$amount = (int)$this->request->getPost('amount');
				if(empty($amount) || $amount == 0) $error[] = "Amount is empty.";
				if($amount > $entry->getAmount()) $error[] = "Amount is greater than borrowed.";
				
				if($amount > 0){
					if(count($error) == 0){
						$entry->setAmount($amount);
						$entry->setStatus('Returned');
						$inventoryLog = new \Event\Entity\InventoryLog();
						$inventoryLog->setInventory( $entry->getInventory() );
						$inventoryLog->setUser( $user );
						$inventoryLog->setCurrentStock( $entry->getInventory()->getStock() );
						$inventoryLog->setDescription( 'Returned : '.$entry->getDescription() );
						$inventoryLog->setDate(new \DateTime("now"));
						
						$inventoryLog->setStock( $amount  );
						$objectManager->persist( $inventoryLog );
						
						$entry->getInventory()->setStock( $entry->getInventory()->getStock() + $amount);
						
						$objectManager->flush();
						return $this->redirect()->toRoute('request');	
					}
				}
				else $error[] = 'Return amount must not be a negative value';
				
				
			}
			
			
		}
		
        return new ViewModel( array( 'entry' => $entry , 'error' => $error) );
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
			
		
		// Create a new, empty entity and bind it to the form
		$request = $objectManager->getRepository('Event\Entity\InventoryRequest')->find( $id );
		
		if ($this->request->isPost()) {
			
				$update = $this->getRequest()->getPost('update', 'Cancel');
				if ($update == 'Update') {
					$request->setAmount( $this->getRequest()->getPost('amount') );
					// Save the changes
					$objectManager->flush();	
				}
				
				
				return $this->redirect()->toRoute('request');
		}
        return new ViewModel(array('request' => $request));
    }
	
	public function declineAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			echo json_encode(array('error' => 'Error: Not logged in'));
			return $this->response;
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		$id = (int) $this->params()->fromRoute('id', 0);
		$request = $objectManager->getRepository('Event\Entity\InventoryRequest')->find( $id );
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		
        if ($this->getRequest()->isPost()) {
			
            $close = $this->getRequest()->getPost('close', 'No');

            if ($close == 'Yes') {
               $request->setNote( $this->getRequest()->getPost('note', null) );
			   $request->setReviewedBy( $user );
			   $request->setStatus( "Declined" );
			   
			   //Notifications
				$message = 'Your request "'.$request->getInventory().'#'.$request->getId().'" <code>has been Declined</code>';
				$notify = new \Notifications\Entity\Notifications();
				$notify->setMessage( $message );
				$notify->setNotifyuser( $request->getUser() );
				
				$objectManager->persist($notify);
			   
			   $objectManager->flush();
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('request');
        }
		
		return new ViewModel( array('id' => $id , 'request' => $request ) );
	}
	
	public function returnRequestAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			echo json_encode(array('error' => 'Error: Not logged in'));
			return $this->response;
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$id = (int) $this->request->getPost('id',0);
		
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		
		$request = $objectManager->getRepository('Event\Entity\InventoryRequest')->find($id);
		$request->setReviewedby( $user );
		$request->setStatus( 'Returned' );
		
		if($request->getInventory()->getType() != 'Can be borrowed'){
						
			$log = new \Event\Entity\InventoryLog();
			$log->setInventory( $request->getInventory() );
			$log->setUser( $user );
			$log->setDescription( 'Returned : '.$request->getDescription() );
			$log->setDate(new \DateTime("now"));
			$log->setCurrentstock( $request->getInventory()->getStock() );
			$log->setStock( $request->getAmount() );
			
			$objectManager->persist($log);
			
			$request->getInventory()->setStock( $request->getInventory()->getStock() + $request->getAmount() );
			
			
		}
		
		$notification = $objectManager->getRepository('Notifications\Entity\Notifications')->findOneBy(array('linkroute' => 'request:returnRequest:'.$id));
		
		$objectManager->remove($notification);
		$objectManager->flush();
		echo json_encode(array('status' => $request->getStatus(), 'error' => '' ));
		return $this->response;
	}
	
	public function approveRequestAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			echo json_encode(array('error' => 'Error: Not logged in'));
			return $this->response;
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$id = (int) $this->request->getPost('id',0);
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		
		$request = $objectManager->getRepository('Event\Entity\InventoryRequest')->find($id);
		$request->setReviewedby( $user );
		$request->setStatus( 'Approved' );
		
		
		$inventory = $objectManager->getRepository('Event\Entity\Inventory')->find( $request->getInventory() );
		
		$log = new \Event\Entity\InventoryLog();
		$log->setInventory( $request->getInventory() );
		$log->setUser( $user );
		$log->setDescription( $request->getDescription() );
		$log->setDate(new \DateTime("now"));
		$log->setCurrentstock( $inventory->getStock() );
		$log->setStock( -$request->getAmount() );
		
		//Update Inventory Stock
		$inventory->setStock( $inventory->getStock() - $request->getAmount());
		$objectManager->persist( $log );
		$objectManager->persist( $request );
		
		//Notifications
		$message = 'Your request "'.$request->getInventory().'#'.$request->getId().'" <code>has been Approved</code>';
		$notify = new \Notifications\Entity\Notifications();
		$notify->setMessage( $message );
		$notify->setNotifyuser( $request->getUser() );

		$objectManager->persist($notify);
		
		
		$objectManager->flush();
		
		echo json_encode(array('status' => $request->getStatus(), 'error' => '' ));
		
		return $this->response;
	}
	
    public function indexAction()
    {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		
		#$list = $objectManager->getRepository('Event\Entity\InventoryRequest')->findAll()->orderBy('');
		$query = $objectManager->createQuery("SELECT r FROM Event\Entity\InventoryRequest r ORDER BY r.id DESC");
		$list = $query->getResult();
		
        return new ViewModel( array( 'list' => $list ) );
    }
}
