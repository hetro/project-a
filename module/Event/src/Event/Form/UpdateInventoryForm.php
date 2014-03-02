<?php
namespace Event\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;

class UpdateInventoryForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('update-inventory-form');

        $this->setHydrator( new DoctrineHydrator($objectManager, 'Event\Entity\Inventory') );

        $inventoryFieldset = new InventoryFieldset($objectManager);
        $inventoryFieldset->setUseAsBaseFieldset(true);
        $this->add($inventoryFieldset);

        // … add CSRF and submit elements …

        // Optionally set your validation group here
		$this->setValidationGroup(
			array(
            	'inventory' => array(
					#'title',
					#'price',
				)
			)
		);
    }
}