<?php namespace Phalcon\Acl\Adapter\Model;

use Phalcon\Mvc\ModelInterface;


interface Access extends ModelInterface
{
	/**
	 * @param \Phalcon\Acl\Adapter\Model\Role $role
	 * @param \Phalcon\Acl\Adapter\Model\Resource $resource
	 * @param array $operations
	 */
	public static function allow(Role $role, Resource $resource, Array $operations);
	/**
	 * @param \Phalcon\Acl\Adapter\Model\Role $role
	 * @param \Phalcon\Acl\Adapter\Model\Resource $resource
	 * @param array $operations
	 */
	public static function disallow(Role $role, Resource $resource, Array $operations);
	/**
	 * @param \Phalcon\Acl\Adapter\Model\Role $role
	 * @param \Phalcon\Acl\Adapter\Model\Resource $resource
	 * @param string $operation
	 * @return bool
	 */
	public static function isAllowed(Role $role, Resource $resource, $operation);
}
