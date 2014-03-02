<?php
namespace Event\UserRole\Service;

class NotificationsService {
	protected $objectManager;
	public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }
	
	public function getObjectManager(){
		return $this->objectManager;
	}
	
	
}


?>