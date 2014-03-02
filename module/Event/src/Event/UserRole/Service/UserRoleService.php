<?php
namespace Event\UserRole\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;


class UserRoleService {

	protected $dbAdapter;
	
	public function __construct($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }
	
	
	public function insertUserRole($data){
		$useRoleLinkTable = new TableGateway('user_role_linker',$this->dbAdapter,null,null);
		$useRoleLinkTable->insert(array("user_id" => $data['user_id'] , "role_id" => $data['role_id']));
	}


}


?>