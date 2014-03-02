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

class EventController extends AbstractActionController
{
	
	
	public function addEventReportAction(){
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
	
		// Create the form and inject the ObjectManager
		$form = new \Event\Form\EditEventForm($objectManager);
		
		$event = $objectManager->getRepository('Event\Entity\Event')->find( $id );


		$form->bind($event);
		
		if($this->request->isPost()){
			$data = array_merge_recursive(
				$this->request->getPost()->toArray(),
				$this->request->getFiles()->toArray()
			);
			
			$form->setData($data);
			#print_r($this->request->getFiles()->toArray());
			$files = $this->request->getFiles()->toArray();
			if($form->isValid()){
				foreach($files['event']['image-file'] as $file){
					$upload_dir = './public/uploads/'.$event->getId();
					if(!file_exists($upload_dir)){
						if(!mkdir($upload_dir, 755, true))
							throw new \Exception("Failed to create upload folders : Please contact an Administrator");
					}
					if(move_uploaded_file($file['tmp_name'],$upload_dir.'/'.$file['name'])){
						/*$uploads = new \Event\Entity\Uploads();
						$uploads->setEvent($event);
						$uploads->setName($file['name']);
						$uploads->setTmpName($file['tmp_name']);
						$uploads->setSize($file['size']);
						$objectManager->persist($uploads);*/
					}
				}
				#print_r($form->get('event')->get('image-file'));
				#print_r($form->getData());
				$objectManager->flush();
				return $this->redirect()->toRoute('event/action', array('action' => 'view', 'id' => $id));
			}
			
		}
		
		return array( 'form' => $form, 'event' => $event );
	}
			
	public function endorseAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		$id = (int) $this->params()->fromRoute('id', 0);
		$request = $objectManager->getRepository('Event\Entity\Event')->find( $id );
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		
        if ($this->getRequest()->isPost()) {
			
            $close = $this->getRequest()->getPost('close', 'No');

            if ($close == 'Yes') {
			   $request->setStatus( "Endorsed" );
			   
			   //Notifications
				$message = 'New Event for Approval "'.$request->getEventTitle().'#'.$request->getId().'"';
				$notify = new \Notifications\Entity\Notifications();
				$notify->setMessage( $message );
				$notify->setNotifywho( 'admin' );
				$notify->setLinkroute( 'event:view:'.$request->getId() );
		
				$objectManager->persist($notify);
			   
			   $objectManager->flush();
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('event');
        }
		
		return new ViewModel( array('id' => $id , 'request' => $request ) );
	}
	
	public function declineAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		$id = (int) $this->params()->fromRoute('id', 0);
		$request = $objectManager->getRepository('Event\Entity\Event')->find( $id );
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		
        if ($this->getRequest()->isPost()) {
			
            $close = $this->getRequest()->getPost('close', 'No');

            if ($close == 'Yes') {
			   $request->setStatus( "Declined" );
			   
			   //Notifications
				$message = 'Event "'.$request->getEventTitle().'#'.$request->getId().'" has been declined';
				$notify = new \Notifications\Entity\Notifications();
				$notify->setMessage( $message );
				$notify->setNotifyuser( $request->getUser() );
		
				$objectManager->persist($notify);
			   
			   $objectManager->flush();
            }

            // Redirect to list of albums
            return $this->redirect()->toRoute('event');
        }
		
		return new ViewModel( array('id' => $id , 'request' => $request ) );
	}
	
	public function approveAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			echo json_encode(array('error' => 'Error: Not logged in'));
			return $this->response;
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		$id = (int) $this->params()->fromRoute('id', 0);
		$request = $objectManager->getRepository('Event\Entity\Event')->find( $id );
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
		
        if ($this->getRequest()->isPost()) {
			
            $close = $this->getRequest()->getPost('close', 'No');

            if ($close == 'Yes') {
				
				$request->setNotes( $this->getRequest()->getPost('note', null) );
				$request->setReviewedBy( $user );
				$request->setStatus( 'Approved');
				
				foreach($request->getBorrows() as $borrow){
					$borrow->setReviewedby( $user );
					$borrow->setStatus( 'Approved');
					
					$inventory = $objectManager->getRepository('Event\Entity\Inventory')->find( $borrow->getInventory() );
					
					
					
					
					/*//InventoryLog
					$log = new \Event\Entity\InventoryLog();
					$log->setInventory( $borrow->getInventory() );
					$log->setUser( $borrow->getUser() );
					$log->setDescription( $borrow->getDescription() );
					$log->setDate(new \DateTime("now"));
					$log->setCurrentstock( $inventory->getStock() );
					$log->setStock( -$borrow->getAmount() );
					
					//Update Inventory Stock
					$inventory->setStock( $inventory->getStock() - $borrow->getAmount());
					
					$objectManager->persist( $log );*/
				}
				//Notifications
				$message = 'Event Request "'.$request->getEventTitle().'#'.$request->getId().'" <code>has been approved</code>';
				$notify = new \Notifications\Entity\Notifications();
				$notify->setMessage( $message );
				$notify->setNotifyuser( $request->getUser()  );
		
				$objectManager->persist($notify);
				
			  $objectManager->flush();
            }

            return $this->redirect()->toRoute('event');
        }
		
		return new ViewModel( array('request' => $request ) );
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
		
		$authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
		$roles = $authorize->getIdentityRoles();
		
		switch($roles[0]){
			case "admin" : 
				$query = $objectManager->createQuery("SELECT e FROM Event\Entity\Event e WHERE e.status = 'Endorsed' OR e.status = 'Approved' ORDER BY e.id DESC");
				$list = $query->getResult();
				break;
			case "secretary" : 
				#$list = $objectManager->getRepository('Event\Entity\Event')->findAll();
				$query = $objectManager->createQuery("SELECT e FROM Event\Entity\Event e ORDER BY e.id DESC");
				$list = $query->getResult();
				break;
			default :
				#return $this->redirect()->toRoute('home'); 
				$query = $objectManager->createQuery("SELECT e FROM Event\Entity\Event e WHERE e.status = 'Approved' ORDER BY e.id DESC");
				$list = $query->getResult();
				break;
		}
        return new ViewModel( array( 'list' => $list) );
    }
	
	public function requestAction()
    {
        if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		$error = 0;
		$authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
		$roles = $authorize->getIdentityRoles();
		#if($roles[0] != 'secretary') return $this->redirect()->toRoute('event');
		
		
		// Get your ObjectManager from the ServiceManager
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		$user = $objectManager->getRepository('Event\Entity\User')->find( $user_id );
	
		// Create the form and inject the ObjectManager
		$form = new \Event\Form\AddEventForm($objectManager);
		
		$event = new \Event\Entity\Event();
		$event->setUser( $user );
		$objectManager->persist( $event );
		
		
		$form->bind($event);
		
	
		$request = $this->request;
		if ($request->isPost()) {
			// Set data from post
			$form->setData($request->getPost());
			
			$post =  $request->getPost('event');
			$startofevent = new \DateTime($post['startofevent']);
			$endofevent = new \DateTime($post['endofevent']);
			#$startofevent->modify('-1 second');
			
			$today = new \DateTime("now");
			if($today->format('Y-m-d') > $startofevent->format('Y-m-d')) $form->get('event')->get('startofevent')->setMessages(array('Must not be earlier than today'));
			if($today->format('Y-m-d') > $endofevent->format('Y-m-d')) $form->get('event')->get('startofevent')->setMessages(array('Must not be earlier than today'));
			if($endofevent->format('Y-m-d') < $startofevent->format('Y-m-d') ) $form->get('event')->get('endofevent')->setMessages(array('Must not be earlier than Start of Event'));
			#$endofevent->modify('-1 second');
			
			$venue = $form->get('event')->get('venue')->getValue(); 
			
			#$query = $objectManager->createQuery("SELECT c FROM Event\Entity\Event c WHERE c.status = 'Approved' AND c.venue = '".$venue."' AND ((c.startofevent between '".$startofevent->format('Y-m-d H:i:s')."' and '".$endofevent->format('Y-m-d H:i:s')."') OR (c.endofevent between '".$startofevent->format('Y-m-d H:i:s')."' and '".$endofevent->format('Y-m-d H:i:s')."'))");
			#$query = $objectManager->createQuery("SELECT c FROM Event\Entity\Event c WHERE c.status = 'Approved' AND c.venue = '".$venue."' AND ((?1 between c.startofevent and c.endofevent) OR (?2 between c.startofevent and c.endofevent))");
			$query = $objectManager->createQuery('SELECT c FROM Event\Entity\Event c WHERE c.status = ?6 AND c.venue = ?5 AND (((c.startofevent < ?1 AND c.endofevent > ?2) OR (c.startofevent < ?3 AND c.endofevent > ?4)))');

			$query->setParameter(1, $startofevent->modify('+1 second')->format('Y-m-d H:i:s'));
			$query->setParameter(2, $startofevent->format('Y-m-d H:i:s'));
			$query->setParameter(3, $endofevent->format('Y-m-d H:i:s'));
			$query->setParameter(4, $endofevent->format('Y-m-d H:i:s'));
			$query->setParameter(5, $venue);
			$query->setParameter(6,"Approved");
			
			$startofeventresult = $query->getResult();
			$check1 = count($startofeventresult);
			/*$query = $objectManager->createQuery("SELECT c FROM Event\Entity\Event c WHERE c.status = 'Approved' AND c.venue = '".$venue."' AND (c.startofevent between '".$startofevent->format('Y-m-d H:i:s')."' and '".$endofevent->format('Y-m-d H:i:s')."')");
			$startofeventresult = $query->getResult();
			$check1 = count($startofeventresult);
			
			
			$query = $objectManager->createQuery("SELECT c FROM Event\Entity\Event c WHERE c.status = 'Approved' AND c.venue = '".$venue."' AND (c.endofevent between '".$startofevent->format('Y-m-d H:i:s')."' and '".$endofevent->format('Y-m-d H:i:s')."')");
			$endofeventresult = $query->getResult();
			$check2 = count($endofeventresult);*/
			
			if(!($check1 == 0/* && $check2 == 0*/)){
				$error++;
				if($check1) $form->get('event')->get('endofevent')->setMessages(array('Conflict at '.$venue.' on "'.$startofeventresult[0]->getEventTitle().'" '.$startofeventresult[0]->getStartofevent()->format('Y-m-d H:i:s').' - '.$startofeventresult[0]->getEndofevent()->format('Y-m-d H:i:s').''));
				#if($check2) $form->get('event')->get('endofevent')->setMessages(array('Conflict at '.$venue.' on "'.$endofeventresult[0]->getEventTitle().'" '.$endofeventresult[0]->getStartofevent()->format('Y-m-d H:i:s').' - '.$endofeventresult[0]->getEndofevent()->format('Y-m-d H:i:s').''));
			}
			
			foreach($form->get('event')->get('borrows') as $borrow){
				$tmp = $objectManager->getRepository('Event\Entity\Inventory')->find( $borrow->get('inventory')->getValue() );
				
				if($tmp){ 
					if($borrow->get('amount')->getValue() > $tmp->getStock()) {
						$borrow->get('amount')->setMessages(array('Amount must not be greater than '.$tmp->getStock() ));
						$error++;
					}
				}
			}
			
			
			/*$tmp = $objectManager->getRepository('Event\Entity\Inventory')->find( $form->get('item-request')->get('inventory')->getValue() );
			
					if($form->get('item-request')->get('amount')->getValue() > $tmp->getStock()) 
						$form->get('item-request')->get('amount')->setMessages(array('Amount must not be greater than '.$tmp->getStock() ));*/
			
			if ($form->isValid() && $error ==0) {
				
				$event->setDateCreated( new \DateTime("now") );
				
				foreach($event->getBorrows() as $borrow){
					
					if((!is_integer($borrow->getAmount()) || $borrow->getAmount()<1) || $borrow->getInventory() == NULL) $event->removeBorrow( $borrow );
					
					$borrow->setUser( $user );
					$borrow->setDescription( '~ Event Request' );
					$borrow->setDateofreturn( $event->getEndOfEvent() );

					
				}
				
				

				$objectManager->flush();
				
				//Notifications
				$message = 'New Event Request "'.$event->getEventTitle().'#'.$event->getId().'"';
				$notify = new \Notifications\Entity\Notifications();
				$notify->setMessage( $message );
				$notify->setNotifywho( 'secretary' );
				$notify->setLinkroute( 'event:view:'.$event->getId() );
		
				$objectManager->persist($notify);
				$objectManager->flush();
				
				return $this->redirect()->toRoute('event');
			}
		}
		
        return new ViewModel( array( 'form' => $form ));
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
		$authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
		$roles = $authorize->getIdentityRoles();
		
		$id = (int) $this->params()->fromRoute('id', 0);
		$entry = $objectManager->getRepository('Event\Entity\Event')->find( $id );
		$images = array();
		$dir = "./public/uploads/".$id;
		if(is_dir($dir)){
			$files = scandir($dir, 1);
			
			foreach($files as $file){
				$fileInfo = pathinfo($file);
				if(isset($fileInfo['extension'])){
					if(preg_match("/(\.|\/)(jpe?g|png)$/i",$file)){
						$images[] = $file;
					}
				}
			}
		}
		
		return new ViewModel(array( 'entry' => $entry , 'roles' => $roles , 'images' => $images ));
	}
}
