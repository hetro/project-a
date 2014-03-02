<?php
namespace Event\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;

class EventFieldset extends Fieldset implements InputFilterProviderInterface
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('event');

        $this->setHydrator(new DoctrineHydrator($objectManager,'Event\Entity\Event'))->setObject(new \Event\Entity\Event());
		
		$this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
		
		$file = new \Zend\Form\Element\File('image-file');
        $file->setLabel('Image Upload')
             ->setAttribute('id', 'image-file')
			 ->setAttribute('multiple', true)
			 ->setAttribute('class','form-control');
			 
        $this->add($file);
		
		$this->add(array(
            'name' => 'eventtitle',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		$this->add(array(
            'name' => 'report',
            'attributes' => array(
                'type'  => 'textarea',
            ),
        ));
		
		$this->add(array(
            'name' => 'fullname',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		$this->add(array(
            'name' => 'company',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		$this->add(array(
            'name' => 'position',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		$this->add(array(
            'name' => 'address',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		$this->add(array(
            'name' => 'contact',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		$this->add(array(
            'name' => 'purpose',
            'attributes' => array(
                'type'  => 'textarea',
            ),
        ));
		
		$this->add(array(
            'name' => 'noofparticipants',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));
		
		/*$this->add(array(
            'name' => 'venue',
            'attributes' => array(
                'type'  => 'text',
            ),
        ));*/
		
		$select = new \Zend\Form\Element\Select('venue');
		$select->setValueOptions(array(
			 'Classroom A' => 'Classroom A - Limit Capacity (40)',
			 'Classroom B' => 'Classroom B - Limit Capacity (80)',
			 'Lobby' => 'Lobby - Limit Capacity (150)',
			 'Conference Room' => 'Conference Room - Limit Capacity (50)',
		));
		$this->add($select);
		
		$this->add(array(
            'name' => 'concerns',
            'attributes' => array(
                'type'  => 'textarea',
            ),
        ));
		
		
		$this->add(array(
			 'type' => 'Zend\Form\Element\DateTime',
			 'name' => 'startofevent',
			 'options' => array(
					 'label' => 'Start of Event',
					 'format' => 'Y-m-d H:i'
			 ),
			 'attributes' => array(
					 'min' => '2013-01-01 00:00',
					 'max' => '2020-01-01 00:00',
					 'step' => '1', // minutes; default step interval is 1 min
			 )
		 )
		);
		
		$this->add(array(
			 'type' => 'Zend\Form\Element\DateTime',
			 'name' => 'endofevent',
			 'options' => array(
					 'label' => 'End of Event',
					 'format' => 'Y-m-d H:i'
			 ),
			 'attributes' => array(
					 'min' => '2013-01-01 00:00',
					 'max' => '2020-01-01 00:00',
					 'step' => '1', // minutes; default step interval is 1 min
			 )
		 )
		);
		
		 /*$this->add(array(
			 'type' => '\Zend\Form\Element\Date',
			 'name' => 'dateofevent',
			 'options' => array(
					 'label' => 'Date of Event'
			 ),
			 'attributes' => array(
					 'min' => '2013-01-01',
					 'max' => '2020-01-01',
					 'step' => '1', // days; default step interval is 1 day
			 )
		 ));*/
		
		$borrowsFieldset = new EventInventoryRequestFieldset($objectManager);
        $this->add(array(
            'type'    => 'Zend\Form\Element\Collection',
            'name'    => 'borrows',
            'options' => array(
                'count'           => 4,
                'target_element' => $borrowsFieldset
            )
        ));
		
		
		/*$this->add(
			array(
				'type' => 'DoctrineModule\Form\Element\ObjectSelect',
				'name' => 'borrow',
				'attributes' => array(
					'id' => 'borrow'
				),
				'options' => array(
					'object_manager'  => $objectManager,
					'target_class'    => 'Event\Entity\Inventory',
					'find_method'    => array(
						'name'   => 'findAll',
					),
					'empty_option'  => 'Select Item',
				),
			)
		);*/
		
    }
	
	public function getInputFilterSpecification(){
			$fileInput = new \Zend\InputFilter\FileInput('image-file');
			$fileInput->setRequired(false);
			
	
			// You only need to define validators and filters
			// as if only one file was being uploaded. All files
			// will be run through the same validators and filters
			// automatically.
			$fileInput->getValidatorChain()
				// ->attachByName('\Zend\Validator\File\Count', array('max' => 7))
				->attachByName('filesize',      array('max' => 2048000))
				->attachByName('filemimetype',  array('mimeType' => 'image/png,image/jpeg,image/x-png'))
				->attachByName('\Zend\Validator\File\ImageSize', array('maxWidth' => 3000, 'maxHeight' => 3000));
	
			// All files will be renamed, i.e.:
			//   ./data/tmpuploads/avatar_4b3403665fea6.png,
			//   ./data/tmpuploads/avatar_5c45147660fb7.png
			$fileInput->getFilterChain()->attachByName(
				'filerenameupload',
				array(
					'target'    => './data/uploads/file.jpg',
					'randomize' => false,
				)
			);
		return array(
           $fileInput,
		   'borrows' => array(
		   		'required' => false,
		   ),
		   'eventtitle' => array(
                'required' => true,
				'filters'  => array(
                    array('name' => '\Zend\Filter\StripTags'),
                    array('name' => '\Zend\Filter\StringTrim'),
                ),
				'validators' => array(
					array(
						'name' => 'StringLength',
						'options' => array(
							'min'      => 1,
							'max' => 60,
							'encoding'=>'UTF-8'
						),
					)
				),
            ),
        );
	}

    
}