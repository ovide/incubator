<?php namespace Phalcon\Acl\Adapter\Model;

use Phalcon\Acl;

/**
 * Description of ExtrangeAccessImplementation
 * @author Albert Ovide <albert@ovide.net>
 */
class KynkiAccess extends \Phalcon\Mvc\Model implements Access
{
	protected $role;
	protected $resource;
	protected $operation;
	protected $access;

	public function columnMap()
	{
		return array(
			'acs_role'      => 'role',
			'acs_resource'  => 'resource',
			'acs_operation' => 'operation',
			'acl_access'    => 'access'
		);
	}

	public function initialize()
	{
		$this->setSource('acl_access');
		//TODO Per alguna raó cal aquest unset o sempre repeteix la clau del primer registre
		unset($this->_uniqueParams);
	}

	/**
	 *
	 * @param \Phalcon\Acl\Adapter\Model\Role $role
	 * @param \Phalcon\Acl\Adapter\Model\Resource $resource
	 * @param string|string[] $operations
	 * @param int $allowOrDeny
	 */
	public static function setAccess(Role $role, Resource $resource, $operations, $allowOrDeny)
	{
		$roleName     = $role->getName();
		$resourceName = $resource->getName();
		if (is_string($operations))
			$operations = array($operations);

		$manager     = new \Phalcon\Mvc\Model\Transaction\Manager();
		$transaction = $manager->get();
		$available   = array_intersect($operations, $resource->getOperations());

		foreach ($available as $operation) {
			$access = new KynkiAccess();
			$access->setTransaction($transaction);
			$access->role      = $roleName;
			$access->resource  = $resourceName;
			$access->operation = $operation;
			$access->access    = $allowOrDeny;
			$access->save();
		}
		$transaction->commit();
	}

	public static function deleteOperations(Role $role, Resource $resource, $operations) {
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

	/**
	 *
	 * @param string $role
	 * @param string $resource
	 * @param string $operation
	 * @return int
	 */
	public static function getAccess($role, $resource, $operation) {
		$row = KynkiAccess::findFirst(array(
			'role = :role: AND resource = :resource: AND operation = :operation:',
			'bind'      => array(
				'role'      => $role,
				'resource'  => $resource,
				'operation' => $operation,
			)
		));
		return $row ? $row->access : null;
	}
}

