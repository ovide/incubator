<?php namespace Phalcon\Acl\Adapter\Model;


/**
 * Description of ExtrangeAccessImplementation
 * @author Albert Ovide <albert@ovide.net>
 */
class KynkiAccess extends \Phalcon\Mvc\Model implements Access
{
	protected $role;
	protected $resource;
	protected $operation;

	public function columnMap()
	{
		return array(
			'acs_role'      => 'role',
			'acs_resource'  => 'resource',
			'acs_operation' => 'operation'
		);
	}

	public function notSave()
	{
		echo 'NOT SAVE'.PHP_EOL;
		foreach($this->getMessages() as $message){
			echo "ERROR ".$message->getMessage();
		}
	}

	public function initialize()
	{
		$this->setSource('acl_access');
		//TODO Per alguna raó cal aquest unset o sempre repeteix la clau del primer registre
		unset($this->_uniqueParams);
	}


	public static function allow(Role $role, Resource $resource, $operations) {
		$roleName = $role->getName();
		$resourceName = $resource->getName();
		if (is_string($operations))
			$operations = array($operations);

		$manager = new \Phalcon\Mvc\Model\Transaction\Manager();
		$transaction = $manager->get();
		$available = array_intersect($operations, $resource->getOperations());
		foreach ($available as $operation) {
			$access = new KynkiAccess();
			$access->setTransaction($transaction);
			$access->role = $roleName;
			$access->resource = $resourceName;
			$access->operation = $operation;
			$access->save();
		}
		$transaction->commit();
	}

	public static function deny(Role $role, Resource $resource, $operations) {
		$roleName = $role->getName();
		$resourceName = $resource->getName();
		if (is_string($operations))
			$operations = array($operations);
		$manager = new \Phalcon\Mvc\Model\Transaction\Manager();
		$transaction = $manager->get();
		$available = array_intersect($operations, $resource->getOperations());
		$accesses = KynkiAccess::query()->andWhere('role = :role:', array('role' => $roleName))
				->andWhere('resource = :resource:', array('resource' => $resourceName))
				->inWhere('operation', $available)
				->execute();

		foreach ($accesses as $access) {
			$access->setTransaction($transaction);
			$access->delete();
		}
		$transaction->commit();
	}

	public static function isAllowed($role, $resource, $operation) {
		return (bool) KynkiAccess::findFirst(array(
			'role = :role: AND resource = :resource: AND operation = :operation:',
			'bind' => array(
				'role'      => $role,
				'resource'  => $resource,
				'operation' => $operation
			)
		));
	}
}

