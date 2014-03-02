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

class ReportsController extends AbstractActionController
{
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
		
        return new ViewModel(  );
    }
	
	public function inventoryAction()
    {
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$inventory = array();
		if($this->request->isPost()){
			$hide = true;
			$start =  $this->request->getPost('start');
			$end =  $this->request->getPost('end');
			
			if(!empty($start) && !empty($end)){
				$query = $objectManager->createQuery("SELECT i.name,i.category,i.stock,l.currentstock,SUM(l.stock) + l.currentstock as endstock FROM Event\Entity\Inventory i LEFT JOIN i.inventorylog l WITH l.date > '$start 00:00:00' AND l.date < '$end 23:59:59'  WHERE i.category = '".$_POST['category']."' GROUP BY i.name");
				#$query = $objectManager->createQuery("SELECT `name`, SUM(inventorylog.stock) as stocknew FROM inventory JOIN inventorylog ON inventorylog.inventory_id = inventory.id WHERE inventorylog.date < '2014-01-29 01:31:33' GROUP BY `name`");
				$inventory = $query->getResult();
			}
			else $inventory = array();
		}
		else {
			$hide = false;
			$query = $objectManager->createQuery("SELECT i.name,i.category,i.stock,l.currentstock,SUM(l.stock) + l.currentstock as endstock FROM Event\Entity\Inventory i LEFT JOIN i.inventorylog l GROUP BY i.name");

				$inventory = $query->getResult();	
				
			$start = '';
			$end = '';
		}
		
        return new ViewModel( array( 'list' => $inventory , 'hide' => $hide , 'dates' => array('start' => $start , 'end' => $end)) );
    }
}
