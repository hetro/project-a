<?php
namespace Event\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

use Zend\Form\Element;

class InventoryFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('inventory');

        $this->setHydrator(new DoctrineHydrator($objectManager,'Event\Entity\Inventory'))->setObject(new \Event\Entity\Inventory());
		
		$this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		$this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		$this->add(array(
            'name' => 'stock',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		$this->add(array(
            'name' => 'description',
            'attributes' => array(
                'type'  => 'textarea',
            ),
        ));
		
		$select = new Element\Select('category');
		$select->setEmptyOption('Select Category');
		$select->setValueOptions(array(
			 'Janitorial' => 'Janitorial',
			 'Office Supplies' => 'Office Supplies',
			 'Furnitures' => 'Furnitures',
			 'Equipments' => 'Equipments',
		));
		$this->add($select);
		
		$select = new Element\Select('type');
		$select->setValueOptions(array(
			 'Consumable' => 'Consumable',
			 'Record' => 'For record purpose only',
			 'Can be borrowed' => 'Can be borrowed',
		));
		$this->add($select);
		
    }
	
	public function getInputFilterSpecification(){
		return array(
           'type' => array(
		   		'required' => false,
		   )
        );
	}

    
}