<?php namespace Phalcon\Acl\Adapter\Model;

use Phalcon\Acl\RoleInterface;
use Phalcon\Mvc\ModelInterface;

interface Role extends ModelInterface, RoleInterface
{
    public function setName($name);
    public function setDescription($description);
    public function setInherit($inherit);
    public function clearInherit();
}