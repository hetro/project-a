<?php
namespace Event\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class InventoryRequestFieldset extends Fieldset implements InputFilterProviderInterface
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
		
		
		$this->add(array(
            'name' => 'amount',
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
		
		$this->add(array(
            'name' => 'dateofreturn',
            'attributes' => array(
                'type'  => 'text',
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
							'criteria' => array('type' => 'Consumable'),
							'orderBy'  => array('name' => 'ASC'),
						),
					),
					'empty_option'  => 'Select Item',
				),
			)
		);
    }
	
	public function getInputFilterSpecification(){
		return array(
           'description' => array('required'=> true),
		   'dateofreturn' => array('required'=> true),
		   'amount' => array(
		   		'required'=> true,
				'validators' => array( new \Zend\Validator\Between( array('min' => 1, 'max' => '10000'))),
				
			),
        );
	}

    
}