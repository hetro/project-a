<?php

return array(
    'bjyauthorize' => array(

        // set the 'guest' role as default (must be defined in a role provider)
        'default_role' => 'guest',

        /* this module uses a meta-role that inherits from any roles that should
         * be applied to the active user. the identity provider tells us which
         * roles the "identity role" should inherit from.
         *
         * for ZfcUser, this will be your default identity provider
         */
        'identity_provider' => 'BjyAuthorize\Provider\Identity\ZfcUserZendDb',

        /* If you only have a default role and an authenticated role, you can
         * use the 'AuthenticationIdentityProvider' to allow/restrict access
         * with the guards based on the state 'logged in' and 'not logged in'.
         *
         * 'default_role'       => 'guest',         // not authenticated
         * 'authenticated_role' => 'user',          // authenticated
         * 'identity_provider'  => 'BjyAuthorize\Provider\Identity\AuthenticationIdentityProvider',
         */

        /* role providers simply provide a list of roles that should be inserted
         * into the Zend\Acl instance. the module comes with two providers, one
         * to specify roles in a config file and one to load roles using a
         * Zend\Db adapter.
         */
        'role_providers' => array(

            /* here, 'guest' and 'user are defined as top-level roles, with
             * 'admin' inheriting from user
             */
            'BjyAuthorize\Provider\Role\Config' => array(
                'guest' => array(),
                'user'  => array('children' => array(
                    'employee',
					'custodian',
					'secretary',
					'admin' => array('children' => array('super admin')),
                )),
            ),

            // this will load roles from the user_role table in a database
            // format: user_role(role_id(varchar), parent(varchar))
            'BjyAuthorize\Provider\Role\ZendDb' => array(
                'table'                 => 'user_role',
                'identifier_field_name' => 'id',
                'role_id_field'         => 'role_id',
                'parent_role_field'     => 'parent_id',
            ),

            // this will load roles from
            // the 'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' service
            /*'BjyAuthorize\Provider\Role\ObjectRepositoryProvider' => array(
                // class name of the entity representing the role
                'role_entity_class' => 'My\Role\Entity',
                // service name of the object manager
                'object_manager'    => 'My\Doctrine\Common\Persistence\ObjectManager',
            ),*/
        ),

        // resource providers provide a list of resources that will be tracked
        // in the ACL. like roles, they can be hierarchical
        'resource_providers' => array(
            'BjyAuthorize\Provider\Resource\Config' => array(
                'pants' => array(),
				'inventory' => array(),
				'event' => array(),
				'request' => array(),
				'reports' => array(),
				'super' => array(),
            ),
        ),

        /* rules can be specified here with the format:
         * array(roles (array), resource, [privilege (array|string), assertion])
         * assertions will be loaded using the service manager and must implement
         * Zend\Acl\Assertion\AssertionInterface.
         * *if you use assertions, define them using the service manager!*
         */
        'rule_providers' => array(
            'BjyAuthorize\Provider\Rule\Config' => array(
                'allow' => array(
                    // allow guests and users (and admins, through inheritance)
                    // the "wear" privilege on the resource "pants"
                    //array(array('guest', 'user'), 'pants', 'wear')
					array(array('custodian'), 'inventory', array('request','add','view')),
					array(array('custodian'), 'request', array('returnRequest','view')),
					
					array(array('secretary','employee'), 'inventory', array('request','item-request')),
					array(array('secretary'), 'event', array('request','view','print','add-report','decline')),
					array(array('user'), 'event', array('request','view')),
					array(array('user'), 'notifications', array('view')),
					array(array('admin'), 'event', array('view','approve','decline')),
					array(array('admin'), 'request', array('view','approve')),
					array(array('custodian','admin'), 'reports', array('view')),
					array(array('super admin'), array('reports', 'event','inventory','request','super')),
                ),

                // Don't mix allow/deny rules if you are using role inheritance.
                // There are some weird bugs.
                'deny' => array(
					#array(array('custodian'), 'event')
                    // ...
                ),
            ),
        ),

        /* Currently, only controller and route guards exist
         *
         * Consider enabling either the controller or the route guard depending on your needs.
         */
        'guards' => array(
            # If this guard is specified here (i.e. it is enabled), it will block
             # access to all controllers and actions unless they are specified here.
             # You may omit the 'action' index to allow access to the entire controller
             #
             'BjyAuthorize\Guard\Controller' => array(
                array('controller' => 'index', 'action' => 'index', 'roles' => array('guest','user')),
                array('controller' => 'index', 'action' => 'stuff', 'roles' => array('user')),
                // You can also specify an array of actions or an array of controllers (or both)
                // allow "guest" and "admin" to access actions "list" and "manage" on these "index",
                // "static" and "console" controllers
                array(
                    'controller' => array('index', 'static', 'console'),
                    'action' => array('list', 'manage'),
                    'roles' => array('guest', 'admin')
                ),
                array(
                    'controller' => array('search', 'administration'),
                    'roles' => array('admin')
                ),
                array('controller' => 'zfcuser', 'roles' => array()),
                array('controller' => 'User', 'roles' => array()),
                // Below is the default index action used by the ZendSkeletonApplication
                array('controller' => 'Application\Controller\Index', 'roles' => array('guest', 'user')),
				
				array('controller' => 'Event', 'roles' => array('user','guest')),
				array('controller' => 'Inventory', 'roles' => array('custodian','secretary','employee','super admin')),
				array('controller' => 'Request', 'roles' => array('admin','custodian')),
				array('controller' => 'Calendar', 'roles' => array('guest', 'user')),
				array('controller' => 'Reports', 'roles' => array('custodian','admin')),
				array('controller' => 'Super', 'roles' => array('super admin')),
            ),

            /* If this guard is specified here (i.e. it is enabled), it will block
             * access to all routes unless they are specified here.
             */
            'BjyAuthorize\Guard\Route' => array(
				
				
				
				// Admin routes
				array('route' => 'zfcadmin', 'roles' => array('user')),
				array('route' => 'zfcuser', 'roles' => array('user','guest')),
                array('route' => 'zfcuser/logout', 'roles' => array('user','guest')),
                array('route' => 'zfcuser/login', 'roles' => array('guest')),
                array('route' => 'zfcuser/register', 'roles' => array('guest')),
				
                // Below is the default index action used by the ZendSkeletonApplication
                array('route' => 'home', 'roles' => array('guest', 'user')),
				
				// EVENT
				array('route' => 'event', 'roles' => array('guest', 'user')),
				array('route' => 'event/action', 'roles' => array('guest', 'user')),
				
				
				// INVENTORY
				array('route' => 'inventory', 'roles' => array('guest', 'user')),
				array('route' => 'inventory/action', 'roles' => array('guest', 'user')),
				
				// REQUEST
				array('route' => 'request', 'roles' => array('guest', 'user')),
				array('route' => 'request/action', 'roles' => array('guest', 'user')),
				
				// CALENDAR
				array('route' => 'calendar', 'roles' => array('guest', 'user')),
				array('route' => 'calendar/action', 'roles' => array('guest', 'user')),
				
				// REPORTS
				array('route' => 'reports', 'roles' => array('user')),
				array('route' => 'reports/action', 'roles' => array('user')),
				
				// REPORTS
				array('route' => 'super', 'roles' => array('super admin')),
				array('route' => 'super/action', 'roles' => array('super admin')),
				
				array('route' => 'notifications', 'roles' => array('user')),
				array('route' => 'notifications/action', 'roles' => array('user')),
            ),
        ),
    ),
	
	
);