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

class UserController extends \ZfcUser\Controller\UserController
{
	public function indexAction(){
		if (!$this->zfcUserAuthentication()->hasIdentity()) {			
			
			$redirect = $this->getRequest()->getRequestUri();			
			$this->getRequest()->getQuery()->set('redirect', $redirect);
			return $this->forward()->dispatch('zfcuser', array(
				'action' => 'login'
			));
        }

		$objectManager = $this->getServiceLocator()->get('Doctrine\ORM\EntityManager');
		$user_id =  $this->zfcUserAuthentication()->getIdentity()->getId();
		
		$authorize = $this->getServiceLocator()->get('BjyAuthorize\Provider\Identity\ProviderInterface');
		$roles = $authorize->getIdentityRoles();
		
		$query = $objectManager->createQuery("SELECT e FROM Event\Entity\Event e WHERE e.user = ".$user_id." ORDER BY e.id DESC");
		$list = $query->getResult();
		
		$query = $objectManager->createQuery("SELECT e FROM Event\Entity\InventoryRequest e WHERE e.user = ".$user_id." ORDER BY e.id DESC");
		$inventory = $query->getResult();
				
        return new ViewModel( array( 'list' => $list , 'inventory' => $inventory) );	
	}
}
