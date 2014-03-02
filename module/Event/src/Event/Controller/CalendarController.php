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

class CalendarController extends AbstractActionController
{
	public function indexAction(){
	
			
	}
	
	public function getCalendarEventsAction(){
		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$date = $this->request->getQuery('date', null);
		$query = $objectManager->createQuery("SELECT e FROM Event\Entity\Event e WHERE '".$date." 23:59:59' > e.startofevent AND '".$date."' < e.endofevent AND e.status = 'Approved'");
		$events = $query->getResult();
		#$events = $objectManager->getRepository('Event\Entity\Event')->findBy( array('startofevent' => new \DateTime($date) , 'status' => 'Approved' ) );

		$view = new ViewModel( array( 'events' => $events ));
    	$view->setTerminal(true);
    	return $view;
	}
}
