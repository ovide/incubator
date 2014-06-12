<?php namespace Phalcon\Acl\Adapter\Model;

use Phalcon\Acl\RoleInterface;
use Phalcon\Mvc\ModelInterface;

interface Role extends ModelInterface, RoleInterface
{
	/**
	 * @param string $name
	 */
    public function setName($name);
	/**
	 * @param string $description
	 */
    public function setDescription($description);
	/**
	 * @param Role $inherit
	 */
    public function setInherit($inherit);
	/**
	 *
	 */
    public function clearInherit();
	/**
	 * @param string $name
	 * @return Role
	 */
	public static function byName($name);
	/**
	 * @return string[]
	 */
	public static function getAll();
}