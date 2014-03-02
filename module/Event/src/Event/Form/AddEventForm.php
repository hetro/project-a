<?php
namespace Event\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject as DoctrineHydrator;
use Zend\Form\Form;

class AddEventForm extends Form
{
    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('add-event-form');

        $this->setHydrator( new DoctrineHydrator($objectManager, 'Event\Entity\Event') );

        $event = new EventFieldset($objectManager);
        $event->setUseAsBaseFieldset(true);
        $this->add($event);

        // … add CSRF and submit elements …

        // Optionally set your validation group here
		$this->setValidationGroup(
			array(
            	'event' => array(
					#'eventtitle',
					#'startofevent',
					#'endofevent',
				)
			)
		);
    }
}