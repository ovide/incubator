<?php namespace Phalcon\Acl\Adapter;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Adapter;
use Phalcon\Acl\Exception;

/**
 * Description of Model
 * @author Albert Ovide <albert@ovide.net>
 */
class Model extends Adapter
{
    /**
     * @var string Model\Role
     */
    protected $modelRole;
    /**
     * @var string Model\Resource
     */
    protected $modelResource;
    /**
     * @var string Model\Access
     */
    protected $modelAccess;

    /**
     *
     * @param string $roleModel
     * @param string $resourceModel
     * @param string $accessModel
     */
    public function __construct($roleModel, $resourceModel, $accessModel)
    {
        $Irole     = '\Phalcon\Acl\Adapter\Model\Role';
        $Iaccess   = '\Phalcon\Acl\Adapter\Model\Access';
        $Iresource = '\Phalcon\Acl\Adapter\Model\Resource';
        $not       = 'is not a subclass of';

		if (!is_subclass_of($roleModel, $Irole))
            throw new Exception ("$roleModel $not $Irole");
        if (!is_subclass_of($resourceModel, $Iresource))
            throw new Exception ("$resourceModel $not $Iresource");
        if (!is_subclass_of($accessModel, $Iaccess))
            throw new Exception ("$accessModel $not $Iaccess");

        $this->modelRole     = $roleModel;
        $this->modelResource = $resourceModel;
        $this->modelAccess   = $accessModel;
    }

    /**
     * Do a role inherit from another existing role
     *
     * @param string $roleName
     * @param string $roleToInherit
     */
    public function addInherit($roleName, $roleToInherit)
    {
		$Role = $this->modelRole;
		if (($child = $Role::byName($roleName)) && $this->isRole($roleToInherit)) {
			$child->setInherit($roleToInherit);
			$r = $child->update();
		}
    }

    /**
     * Adds a resource to the ACL list
     *
     * Access names can be a particular action, by example
     * search, update, delete, etc or a list of them
     *
     * @param   \Phalcon\Acl\ResourceInterface $resource
     * @param   array $accessList
     * @return  boolean
     */
    public function addResource($resource, $accessList=null)
    {
		$new           = false;
		$ResourceModel = $this->modelResource;
        $model         = $ResourceModel::byName($resource->getName());

        if (!$model) {
			$new   = true;
            $model = new $ResourceModel();
            $model->setName($resource->getName());
            $model->setDescription($resource->getDescription());
        }

		$hasList = count($accessList);
		if ($hasList)
			$model->addOperations($accessList);

		if ($new || $hasList !== null)
			$model->save();
    }

    /**
     * Adds access to resources
     *
     * @param string $resourceName
     * @param mixed $accessList
     */
    public function addResourceAccess($resourceName, $accessList)
    {
		if (is_string($accessList))
			$accessList = array($accessList);

		$ResourceModel = $this->modelResource;
		$model         = $ResourceModel::byName($resourceName);

		if ($model && count($accessList)) {
			$model->addOperations($accessList);
			$model->save();
		}
    }

    /**
     * Adds a role to the ACL list.
     * Second parameter lets to inherit access data from other existing role
     *
     * @param  \Phalcon\Acl\RoleInterface $role
     * @param  string $accessInherits
     * @return boolean
     */
    public function addRole($role, $accessInherits = null)
    {
		$Role = $this->modelRole;

		if ($this->isRole($role->getName())) {
			$model = $Role::byName($role->getName());
		} else {
            $model = new $Role();
            $model->setName($role->getName());
            $model->setDescription($role->getDescription());
        }

		if ($accessInherits && $this->isRole($accessInherits)) {
			$model->setInherit($accessInherits);
		}

        if ($model->save()) $this->_activeRole = $role;
	}

    /**
     * Allow access to a role on a resource
     *
     * @param string $roleName
     * @param string $resourceName
     * @param mixed $access
     */
    public function allow($roleName, $resourceName, $access)
    {
		$AccessModel   = $this->modelAccess;
		$RoleModel     = $this->modelRole;
		$ResourceModel = $this->modelResource;
		$roleRow       = $RoleModel::byName($roleName);
		$resourceRow   = $ResourceModel::byName($resourceName);

		if ($roleRow && $resourceRow)
			$AccessModel::setAccess($roleRow, $resourceRow, $access, Acl::ALLOW);
}

    /**
     * Deny access to a role on a resource
     *
     * @param string $roleName
     * @param string $resourceName
     * @param mixed $access
     * @return boolean
     */
    public function deny($roleName, $resourceName, $access)
    {
		$AccessModel   = $this->modelAccess;
		$RoleModel     = $this->modelRole;
		$ResourceModel = $this->modelResource;
		$roleRow       = $RoleModel::byName($roleName);
		$resourceRow   = $ResourceModel::byName($resourceName);

		$AccessModel::setAccess($roleRow, $resourceRow, $access, Acl::DENY);
    }

    /**
     * Removes an access from a resource
     *
     * @param string $resourceName
     * @param mixed $accessList
     */
    public function dropResourceAccess($resourceName, $accessList)
    {
		$ResourceModel = $this->modelResource;
		$model         = $ResourceModel::byName($resourceName);

		if ($model && count($accessList)) {
			$model->dropOperations($accessList);
			$model->save();
		}
    }

    /**
     * Return an array with every resource registered in the list
     *
     * @return \Phalcon\Acl\ResourceInterface[]
     */
    public function getResources()
    {
		/* @var $rows \Phalcon\Mvc\Model\ResultsetInterface */
		$result        = array();
		$ResourceModel = $this->modelResource;
		$rows          = $ResourceModel::getAll();

		foreach ($rows as $row)
			$result[] = new Resource($row->getName(), $row->getDescription());

		return $result;
	}

    /**
     * Return an array with every role registered in the list
     *
     * @return \Phalcon\Acl\RoleInterface[]
     */
    public function getRoles()
    {
		/* @var $rows \Phalcon\Mvc\Model\ResultsetInterface */
		$result     = array();
		$RolesModel = $this->modelRole;
		$rows       = $RolesModel::getAll();

		foreach ($rows as $row)
			$result[] = new Role($row->getName(), $row->getDescription());

		return $result;
    }

    /**
     * Check whether a role or inherited is allowed to access an action from a resource
     *
     * @param  string $role
     * @param  string $resource
     * @param  string $access
     * @return boolean
     */
    public function isAllowed($role, $resource, $access)
    {
		$allowed = $this->_defaultAccess;
		$this->_activeRole     = $role;
		$this->_activeResource = $resource;
		$this->_activeAccess   = $access;

		$RoleModel             = $this->modelRole;
		$ResourceModel         = $this->modelResource;

		$roleRow               = $RoleModel::byName($role);
		$resourceRow           = $ResourceModel::byName($resource);
		echo "\n\n";
		$return = $this->allowed($roleRow, $resourceRow, $access);
		echo "\n----$return----\n";
		
		if ($return !== null) $allowed = $return;
		return (bool)$allowed;
    }

    /**
     * Check whether a role or inherited role is allowed to access an action from a resource
     *
     * @param  Phalcon\Acl\Adapter\Model\Role $role
     * @param  Phalcon\Acl\Adapter\Model\Resource $resource
     * @param  string $operation
     * @return boolean
     */
	private function allowed(Model\Role $role, Model\Resource $resource, $operation)
	{
		echo __FUNCTION__.' '.$role->getName().' '.$resource->getName()." $operation\n";
		$access = null;
		/* @var $roleRow Phalcon\Acl\Adapter\Model\Role */
		$AccessModel   = $this->modelAccess;

		$inherit       = $role->getInherit();

		if ($inherit && $inherit->getName() != $this->_activeRole) {
			$parentAccess = $this->allowed($inherit, $resource, $operation);
			if ($parentAccess !== null) $access = $parentAccess;
		}

		$row  = $AccessModel::getAccess($role->getName(), $resource->getName(), $operation);
		if ($row) $access = $row;
		return $access;
	}

    /**
     * Check whether resource exist in the resources list
     *
     * @param  string $resourceName
     * @return boolean
     */
    public function isResource($resourceName)
    {
		$Resource = $this->modelResource;
		$row      = $Resource::findFirst(array('name = :name:', 'bind' => array('name' => $resourceName)));
		return (bool) $row;
    }

    /**
     * Check whether role exist in the roles list
     *
     * @param  string $roleName
     * @return boolean
     */
    public function isRole($roleName)
    {
		$Role = $this->modelRole;
		$row  = $Role::byName($roleName);
		return (bool) $row;
    }
}
