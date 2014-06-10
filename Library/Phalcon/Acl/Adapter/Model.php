<?php

namespace Phalcon\Acl\Adapter;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Acl\Adapter;
use Phalcon\Acl\Exception;
use Phalcon\Mvc\ModelInterface;


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
        /* @var $role Model\Role */
        $role    = Model\Role::findFirst($roleName);
        $inherit = Model\Role::findFirst($roleToInherit);
        if ($role && $inherit)
            $role->addInherit($inherit);
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
        $model = Model\Resource::findFirst($resource->getName());
        if (!$model) {
            $model = new Model\Resource();
            $model->setName($resource->getName());
            $model->setDescription($resource->getDescription());
        }
        $model->addOperation($accessList);
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
        $model = Model\Role::findFirst($role->getDescription());
        if (!$model) {
            $model = new Model\Role();
            $model->setName($role->getName());
            $model->setDescription($role->getDescription());
        }
        $model->addInherit($accessInherits);
        $model->save();
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
        
    }

    /**
     * Removes an access from a resource
     *
     * @param string $resourceName
     * @param mixed $accessList
     */
    public function dropResourceAccess($resourceName, $accessList)
    {
        
    }

    /**
     * Return an array with every resource registered in the list
     *
     * @return \Phalcon\Acl\ResourceInterface[]
     */
    public function getResources()
    {
        
    }

    /**
     * Return an array with every role registered in the list
     *
     * @return \Phalcon\Acl\RoleInterface[]
     */
    public function getRoles()
    {
        
    }

    /**
     * Check whether a role is allowed to access an action from a resource
     *
     * @param  string $role
     * @param  string $resource
     * @param  string $access
     * @return boolean
     */
    public function isAllowed($role, $resource, $access)
    {
        
    }

    /**
     * Check whether resource exist in the resources list
     *
     * @param  string $resourceName
     * @return boolean
     */
    public function isResource($resourceName)
    {
        
    }

    /**
     * Check whether role exist in the roles list
     *
     * @param  string $roleName
     * @return boolean
     */
    public function isRole($roleName)
    {
        
    }

}

