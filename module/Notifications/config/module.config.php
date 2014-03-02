<?php
return array(
	'bjyauthorize' => array(
		'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
				'notifications' => array(),
            ),
        ),
		'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
					array(array('user'), 'notifications', array('view')),
                ),
                'deny' => array(
                ),
            ),
        ),
		'guards' => array(
            # If this guard is specified here (i.e. it is enabled), it will block
             # access to all controllers and actions unless they are specified here.
             # You may omit the 'action' index to allow access to the entire controller
             #
             'BjyAuthorize\Guard\Controller' => array(
				array('controller' => 'Notifications', 'roles' => array('user')),
            ),
            'BjyAuthorize\Guard\Route' => array(
				array('route' => 'notifications', 'roles' => array('user')),
            ),
        ),
	),

	'doctrine' => array(
	  'driver' => array(
		'notifications_entities' => array(
		  'class' =>'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
		  'cache' => 'array',
		  'paths' => array(__DIR__ . '/../src/Notifications/Entity')
		),
	
		'orm_default' => array(
		  'drivers' => array(
			'Notifications\Entity' => 'notifications_entities'
		  )
	))),

	'controllers' => array(
        'invokables' => array(
            'Notifications' => 'Notifications\Controller\NotificationsController',
        ),
    ),
	
	'router' => array(
		'routes' => array(
			
			'notifications' => array(
				'type'    => 'Literal',
				'options' => array(
					'route'    => '/notifications',
					'defaults' => array(
						'controller'    => 'Notifications',
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
            'event' => __DIR__ . '/../view'
        ),
    ),
);