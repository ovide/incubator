<?php namespace Phalcon\Acl\Adapter\Model;

use Phalcon\Mvc\ModelInterface;


interface Access extends ModelInterface
{
	/**
	 * @param \Phalcon\Acl\Adapter\Model\Role $role
	 * @param \Phalcon\Acl\Adapter\Model\Resource $resource
	 * @param string|string[] $operations
	 */
	public static function allow(Role $role, Resource $resource, $operations);
	/**
	 * @param \Phalcon\Acl\Adapter\Model\Role $role
	 * @param \Phalcon\Acl\Adapter\Model\Resource $resource
	 * @param string|string[] $operations
	 */
	public static function deny(Role $role, Resource $resource, $operations);
	/**
	 * @param string $role
	 * @param string $resource
	 * @param string $operation
	 * @return bool
	 */
	public static function isAllowed($role, $resource, $operation);
}
