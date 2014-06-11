<?php namespace Phalcon\Acl\Adapter\Model;


/**
 * Description of ExtrangeAccessImplementation
 * @author Albert Ovide <albert@ovide.net>
 */
class KynkiAccess extends \Phalcon\Mvc\Model implements Access
{
	protected $role;
	protected $resource;
	protected $accesses;

	public static function allow(Role $role, Resource $resource, array $operations) {

	}

	public static function disallow(Role $role, Resource $resource, array $operations) {

	}

	public static function isAllowed(Role $role, Resource $resource, $operation) {

	}
}

