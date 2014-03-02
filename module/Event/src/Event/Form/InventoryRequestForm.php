<?php
namespace Event\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;

class InventoryRequestForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('item-request-form');

        $this->setHydrator( new DoctrineHydrator($objectManager, 'Event\Entity\InventoryRequest') );

        $itemRequest = new InventoryRequestFieldset($objectManager);
        $itemRequest->setUseAsBaseFieldset(true);
        $this->add($itemRequest);

        // … add CSRF and submit elements …

        // Optionally set your validation group here
		$this->setValidationGroup(
			array(
            	'item-request' => array(
					#'title',
					#'price',
				)
			)
		);
    }
}