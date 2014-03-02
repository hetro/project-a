<?php
return array(
	'doctrine' => array(
	  'driver' => array(
		'application_entities' => array(
		  'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
		  'cache' => 'array',
		  'paths' => array(__DIR__ . '/../src/Event/Entity')
		),
	
		'orm_default' => array(
		  'drivers' => array(
			'Event\Entity' => 'application_entities'
		  )
	))),

	'controllers' => array(
        'invokables' => array(
            'Event' => 'Event\Controller\EventController',
			'Inventory' => 'Event\Controller\InventoryController',
			'Request' => 'Event\Controller\RequestController',
			'Calendar' => 'Event\Controller\CalendarController',
			'Reports' => 'Event\Controller\ReportsController',
			'User' => 'Event\Controller\UserController',
			'Super' => 'Event\Controller\SuperController',
            // Do similar for each other controller in your module
        ),
    ),
	
	'router' => array(
		'routes' => array(
			'zfcuser' => array(
                'options' => array(
                    'route' => '/account',
					'defaults' => array(
						#'controller' => 'zfcuser',
						'controller' => 'User',
					),
                ),
			),
			
			'super' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/super',
					'defaults' => array(
						'controller'    => 'Super',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'action' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/[:action[/:id]]',
							'constraints' => array(
								'id'     => '[0-9]*',
								'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
							),
						),
					),
				),
			),
			
			'event' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/event',
					'defaults' => array(
						'controller'    => 'Event',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'action' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/[:action[/:id]]',
							'constraints' => array(
								'id'     => '[0-9]*',
								'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
							),
						),
					),
				),
			),
			
			'inventory' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/inventory',
					'defaults' => array(
						'controller'    => 'Inventory',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'action' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/[:action[/:id]]',
							'constraints' => array(
								'id'     => '[0-9]*',
								'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
							),
						),
					),
				),
			),
			
			'reports' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/reports',
					'defaults' => array(
						'controller'    => 'Reports',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'action' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/[:action[/:id]]',
							'constraints' => array(
								'id'     => '[0-9]*',
								'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
							),
						),
					),
				),
			),
			
			'request' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/request',
					'defaults' => array(
						'controller'    => 'Request',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'action' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/[:action[/:id]]',
							'constraints' => array(
								'id'     => '[0-9]*',
								'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
							),
						),
					),
				),
			),
			
			
			'calendar' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/calendar',
					'defaults' => array(
						'controller'    => 'Calendar',
						'action'        => 'index',
					),
				),
				'may_terminate' => true,
				'child_routes' => array(
					'action' => array(
						'type'    => 'Segment',
						'options' => array(
							'route'    => '/[:action[/:id]]',
							'constraints' => array(
								'id'     => '[0-9]*',
								'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
							),
							'defaults' => array(
							),
						),
					),
				),
			),
			
		),
	),
	
    'view_manager' => array(
        'template_path_stack' => array(
            'notifications' => __DIR__ . '/../view'
        ),
    ),
	
	'service_manager' => array(
		'factories' => array(
			'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
		),
    ),
);