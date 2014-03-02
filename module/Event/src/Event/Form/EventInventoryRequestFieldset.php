<?php
namespace Event\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class EventInventoryRequestFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('item-request');

        $this->setHydrator(new DoctrineHydrator($objectManager,'Event\Entity\InventoryRequest'))->setObject(new \Event\Entity\InventoryRequest());
		
		$this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		$this->add(
			array(
				'type' => 'DoctrineModule\Form\Element\ObjectSelect',
				'name' => 'inventory',
				'attributes' => array(
					'id' => 'item'
				),
				'options' => array(
					'object_manager'  => $objectManager,
					'target_class'    => 'Event\Entity\Inventory',
					'find_method'    => array(
						'name'   => 'findBy',
						'params' => array(
							'criteria' => array('type' => 'Can be borrowed'),
							'orderBy'  => array('name' => 'ASC'),
						),
					),
					'empty_option'  => 'Select Item',
				),
			)
		);
		
		$this->add(array(
            'name' => 'amount',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		
    }
	
	public function getInputFilterSpecification(){
		return array(
           'amount' => array(
		   		'required' => true,
				'validators' => array( new \Zend\Validator\Between( array('min' => 1, 'max' => '100'))),
		   )
        );
	}

    
}