<?php
namespace Event\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class RequestFieldset extends Fieldset implements InputFilterProviderInterface
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
            'name' => 'note',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
    }
	
	public function getInputFilterSpecification(){
		return array(
           
        );
	}

    
}