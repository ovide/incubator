<?php
namespace Phalcon\Acl\Adapter;
use Codeception\Util\Stub;

class ModelTest extends \Codeception\TestCase\Test
{
   /**
    * @var \CodeGuy
    */
    protected $codeGuy;
    
    /**
     * @var Model
     */
    protected $acl;
    
    /**
     * @var Mysql
     */
    protected $con;

    protected function _before()
    {
        $di = new DI\FactoryDefault();
        
        $di->set('db', function(){
            $con = new Mysql(array(
                'host'     => 'localhost',
                'dbname'   => 'phalcon_incubator',
                'port'     => 3306,
                'username' => 'phalcon',
                'password' => 'incubator',
            ));            
            return $con;
        });
        
        $this->acl = new Model(array(
            'roles'             => 'roles',
            'resources'         => 'resources',
            'accessList'        => 'access_list',
        ));
        $this->acl->setDefaultAction(\Phalcon\Acl::DENY);
    }

    protected function _after()
    {
        $this->con->execute('DELETE FROM acl_access');
        $this->con->execute('DELETE FROM acl_resource');
        $this->con->execute('DELETE FROM acl_role');
    }
    
    public function testAddRole()
    {
        $I = $this->codeGuy;
        $this->acl->addRole('tester');
        $this->assertTrue($this->acl->isRole('tester'));
        //$I->seeInDatabase('roles', array('name' => 'tester'));
    }
    
    public function testAddInherit()
    {
        $I = $this->codeGuy;
        $this->acl->addRole('root');
        $this->acl->addRole('tester');
        $this->acl->addInherit('tester', 'root');
        //$I->seeInDatabase('roles_inherits', array(
        //    'roles_name'    => 'tester',
        //    'roles_inherit' => 'root'
        //));
    }
    
    public function testIsRole()
    {
        $this->assertFalse($this->acl->isRole('tester'));
        $this->acl->addRole('tester');
        $this->assertTrue($this->acl->isRole('tester'));
    }
    
    public function testIsResource()
    {
        $this->assertFalse($this->acl->isResource('resource'));
        $this->acl->addResource('resource');
        $this->assertTrue($this->acl->isResource('resource'));
    }
    
    public function testAddResource()
    {
        $I = $this->codeGuy;
        $this->acl->addResource('resource');
        $this->assertTrue($this->acl->isResource('resource'));
        //$I->seeInDatabase('resources', array('name' => 'resource'));
    }
    
    public function testAddResourceAccess()
    {
        $I = $this->codeGuy;
        $this->acl->addResource('resource');
        $this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
        $I->seeInDatabase('resources_accesses', array(
            'resources_name' => 'resource',
            'access_name'    => 'add'
        ));
        $I->seeInDatabase('resources_accesses', array(
            'resources_name' => 'resource',
            'access_name'    => 'edit'
        ));
        $I->seeInDatabase('resources_accesses', array(
            'resources_name' => 'resource',
            'access_name'    => 'delete'
        ));
    }
    
    public function testGetResources()
    {
        $this->acl->addResource('resource');
        $this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
        $resources = $this->acl->getResources();
        $this->assertEquals('resource', $resources[0]->getName());
    }
    
    public function testGetRoles()
    {
        $this->acl->addRole('tester');
        $this->acl->addRole('root');
        $roles = $this->acl->getRoles();
        $this->assertEquals('root', $roles[0]->getName());
        $this->assertEquals('tester', $roles[1]->getName());
    }
    
    public function testDropResourceAccess()
    {
        $I = $this->codeGuy;
        $this->acl->addResource('resource');
        $this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
        $this->acl->dropResourceAccess('resource', array('edit', 'delete'));
        $I->seeInDatabase('resources_accesses', array(
            'resources_name' => 'resource',
            'access_name'    => 'add'
        ));
        $I->dontSeeInDatabase('resources_accesses', array(
            'resources_name' => 'resource',
            'access_name'    => 'edit'
        ));
        $I->dontSeeInDatabase('resources_accesses', array(
            'resources_name' => 'resource',
            'access_name'    => 'delete'
        ));
    }
    
    public function testAllow()
    {
        $I = $this->codeGuy;
        $this->acl->addResource('resource');
        $this->acl->addRole('tester');
        $this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
        $this->acl->allow('tester', 'resource', 'add');
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'add'));
        $this->assertFalse($this->acl->isAllowed('tester', 'resource', 'edit'));
        $this->assertFalse($this->acl->isAllowed('tester', 'resource', 'delete'));
        $I->seeInDatabase('access_list', array(
            'roles_name'     => 'tester',
            'resources_name' => 'resource',
            'access_name'    => 'add',
            'allowed'        => '1'
        ));
    }
    
    public function testDeny()
    {
        $I = $this->codeGuy;
        $this->acl->addResource('resource');
        $this->acl->addRole('tester');
        $this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
        $this->acl->allow('tester', 'resource', array('add', 'edit', 'delete'));
        $this->acl->deny('tester', 'resource', 'add');
        $this->assertFalse($this->acl->isAllowed('tester', 'resource', 'add'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'edit'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'delete'));
        $I->seeInDatabase('access_list', array(
            'roles_name'     => 'tester',
            'resources_name' => 'resource',
            'access_name'    => 'add',
            'allowed'        => '0'
        ));
    }
    
    public function testIsAllowed()
    {
        $I = $this->codeGuy;
        $this->acl->addResource('resource');
        $this->acl->addRole('tester');
        $this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
        $this->acl->allow('tester', 'resource', array('add', 'edit', 'delete'));
        $this->acl->deny('tester', 'resource', 'add');
        $this->assertFalse($this->acl->isAllowed('tester', 'resource', 'add'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'edit'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'delete'));
        $I->seeInDatabase('access_list', array(
            'roles_name'     => 'tester',
            'resources_name' => 'resource',
            'access_name'    => 'add',
            'allowed'        => '0'
        ));
    }
}