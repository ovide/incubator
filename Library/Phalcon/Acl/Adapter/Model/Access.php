<?php namespace Phalcon\Acl\Adapter\Model;

use Phalcon\Mvc\ModelInterface;


interface Access extends ModelInterface
{
	/**
	 * @param string $role
	 * @param string $resource
	 * @param string $operation
	 * @return int
	 */
	public static function getAccess($role, $resource, $operation);
	/**
	 *
	 * @param \Phalcon\Acl\Adapter\Model\Role $role
	 * @param \Phalcon\Acl\Adapter\Model\Resource $resource
	 * @param string|string[] $operations
	 * @param int $allowOrDeny
	 */
	public static function setAccess(Role $role, Resource $resource, $operations, $allowOrDeny);
	/**
	 *
	 * @param \Phalcon\Acl\Adapter\Model\Role $role
	 * @param \Phalcon\Acl\Adapter\Model\Resource $resource
	 * @param string|string[] $operations
	 */
	public static function deleteOperations(Role $role, Resource $resource, $operations);
}
