<?php namespace Phalcon\Acl\Adapter;
use Codeception\Util\Stub;

require_once __DIR__.'/Model/KynkiAccess.php';
require_once __DIR__.'/Model/KynkiResource.php';
require_once __DIR__.'/Model/KynkiRole.php';

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

    protected function _before()
    {
        $di = new \Phalcon\DI\FactoryDefault();
        $di->set('db', function(){
			$events = new \Phalcon\Events\Manager();
			$events->attach('db:afterQuery', function(\Phalcon\Events\Event $event, \Phalcon\Db\Adapter\Pdo\Mysql $connection){
				echo $connection->getSQLStatement().PHP_EOL;
			});
            $con = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
                'host'     => 'localhost',
                'dbname'   => 'phalcon_incubator',
                'username' => 'phalcon',
                'password' => 'incubator',
				'options'  => array(
					\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8'",
				)
            ));
			$con->setEventsManager($events);
            return $con;
        });
		$di['modelsMetadata'] = function() {
			$metaData = new \Phalcon\Mvc\Model\MetaData\Apc(array(
				"lifetime" => 86400,
				"prefix"   => "my-prefix"
			));
			return $metaData;
		};
		\Phalcon\Mvc\Model::setup(array('notNullValidations' => false));
        $this->acl = new Model(
			'\Phalcon\Acl\Adapter\Model\KynkiRole',
            '\Phalcon\Acl\Adapter\Model\KynkiResource',
            '\Phalcon\Acl\Adapter\Model\KynkiAccess'
        );
        $this->acl->setDefaultAction(\Phalcon\Acl::DENY);
    }
    protected function _after()
    {
		$di = \Phalcon\DI::getDefault();
		$con = $di['db'];
        $con->execute('DELETE FROM acl_access');
        $con->execute('DELETE FROM acl_resource');
        $con->execute('DELETE FROM acl_role');
		unset($di);
		unset($this->acl);
    }
	public function testModel()
	{
		$I = $this->codeGuy;
		$role = new Model\KynkiRole();
		$role->setName('testName');
		$role->save();
		$I->seeInDatabase('acl_role', array('rl_name' => 'testName'));
	}
    public function testRole()
    {
        $I = $this->codeGuy;
        $this->acl->addRole(new \Phalcon\Acl\Role('tester'));
		$this->acl->addRole(new \Phalcon\Acl\Role('root'));
        $this->assertTrue($this->acl->isRole('tester'));
        $I->seeInDatabase('acl_role', array('rl_name' => 'tester', 'rl_inherits' => ''));
		$this->acl->addInherit('tester', 'root');
		$I->seeInDatabase('acl_role', array('rl_name' => 'tester', 'rl_inherits' => 'root'));
    }
    public function testResource()
    {
        $I = $this->codeGuy;
        $this->acl->addResource(new \Phalcon\Acl\Resource('resource'));
        $this->assertTrue($this->acl->isResource('resource'));
		$this->acl->addResource(new \Phalcon\Acl\Resource('new_resource'), array('add','edit','delete'));
		$this->assertTrue($this->acl->isResource('new_resource'));
        $I->seeInDatabase('acl_resource', array('rsc_name' => 'resource', 'rsc_operation' => ''));
		$I->seeInDatabase('acl_resource', array('rsc_name' => 'new_resource', 'rsc_operation' => 'add'));
		$I->seeInDatabase('acl_resource', array('rsc_name' => 'new_resource', 'rsc_operation' => 'edit'));
		$I->seeInDatabase('acl_resource', array('rsc_name' => 'new_resource', 'rsc_operation' => 'delete'));
		$this->acl->addResourceAccess('resource', array('add'));
		$I->seeInDatabase('acl_resource', array('rsc_name' => 'resource', 'rsc_operation' => 'add'));
		$this->acl->addResourceAccess('resource', array('edit'));
		$I->seeInDatabase('acl_resource', array('rsc_name' => 'resource', 'rsc_operation' => 'add'));
		$I->seeInDatabase('acl_resource', array('rsc_name' => 'resource', 'rsc_operation' => 'edit'));
		$this->acl->dropResourceAccess('new_resource', array('edit', 'none'));
		$I->dontSeeInDatabase('acl_resource', array('rsc_name' => 'new_resource', 'rsc_operation' => 'edit'));
		$I->dontSeeInDatabase('acl_resource', array('rsc_name' => 'new_resource', 'rsc_operation' => 'none'));
		$this->acl->dropResourceAccess('new_resource', array('add', 'edit', 'delete'));
		$I->dontSeeInDatabase('acl_resource', array('rsc_name' => 'new_resource', 'rsc_operation' => 'add'));
		$I->dontSeeInDatabase('acl_resource', array('rsc_name' => 'new_resource', 'rsc_operation' => 'edit'));
		$I->dontSeeInDatabase('acl_resource', array('rsc_name' => 'new_resource', 'rsc_operation' => 'delete'));
    }
    public function testGetResources()
    {
		$memory = new \Phalcon\Acl\Adapter\Memory();
        $this->acl->addResource(new \Phalcon\Acl\Resource('resource'));
		$memory->addResource(new \Phalcon\Acl\Resource('resource'));
        $this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
		$memory->addResourceAccess('resource', array('add', 'edit', 'delete'));
        $resources = $this->acl->getResources();
		$memoryResources = $memory->getResources();

		$this->assertTrue(is_array($resources));
		$this->assertInstanceOf('\Phalcon\Acl\ResourceInterface', $resources[0]);
        $this->assertEquals($memoryResources[0]->getName(), $resources[0]->getName());
		$this->assertEquals(count($memoryResources), count($resources));
    }
    public function testGetRoles()
    {
		$I = $this->codeGuy;

		$memory = new \Phalcon\Acl\Adapter\Memory();
        $this->acl->addRole(new \Phalcon\Acl\Role('tester'));
		$memory->addRole(new \Phalcon\Acl\Role('tester'));
        $this->acl->addRole(new \Phalcon\Acl\Role('root'));
		$memory->addRole(new \Phalcon\Acl\Role('root'));
        $roles = $this->acl->getRoles();
		$memoryRoles = $memory->getRoles();

		$I->amGoingTo('check roles is an array');
		$this->assertTrue(is_array($roles));
		$I->amGoingTo('check roles items are instance of \Phalcon\Acl\RoleInterface');
		$this->assertInstanceOf('\Phalcon\Acl\RoleInterface', $roles[0]);
		$I->amGoingTo('check that this adapter works like \Phalcon\Acl\Adapter\Memory');
		$this->assertTrue(in_array($roles[0], $memoryRoles));
		$this->assertTrue(in_array($roles[1], $memoryRoles));
		$this->assertEquals(count($memoryRoles), count($roles));
		$I->amGoingTo('check that I get the inserted role names');
        $this->assertEquals('root', $roles[0]->getName());
        $this->assertEquals('tester', $roles[1]->getName());
    }
    public function testAccess()
    {
        $this->acl->addResource(new \Phalcon\Acl\Resource('resource'));
        $this->acl->addRole(new \Phalcon\Acl\Role('tester'));
        $this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
        $this->acl->allow('tester', 'resource', 'add');
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'add'));
        $this->assertFalse($this->acl->isAllowed('tester', 'resource', 'edit'));
        $this->assertFalse($this->acl->isAllowed('tester', 'resource', 'delete'));
		$this->acl->allow('tester', 'resource', array('add', 'edit', 'delete', 'fakeOperation'));
		$this->assertTrue($this->acl->isAllowed('tester', 'resource', 'add'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'edit'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'delete'));
		$this->assertFalse($this->acl->isAllowed('tester', 'resource', 'fakeOperation'));
		$this->acl->deny('tester', 'resource', 'add');
		$this->assertFalse($this->acl->isAllowed('tester', 'resource', 'add'));
		$this->acl->deny('tester', 'resource', array('add', 'edit', 'fakeOperation'));
		$this->assertFalse($this->acl->isAllowed('tester', 'resource', 'add'));
		$this->assertFalse($this->acl->isAllowed('tester', 'resource', 'edit'));
		$this->assertFalse($this->acl->isAllowed('tester', 'resource', 'fakeOperation'));
		$this->assertTrue($this->acl->isAllowed('tester', 'resource', 'delete'));
    }
	public function testInheritedAccess()
	{
		$I = $this->codeGuy;

		$I->amGoingTo("add a resource 'resource'");
		$this->acl->addResource(new \Phalcon\Acl\Resource('resource'));
		$I->amGoingTo("add 'add', 'edit', 'delete' operations to 'resource'");
		$this->acl->addResourceAccess('resource', array('add', 'edit', 'delete'));
		$I->amGoingTo("add roles 'tester', 'user' and 'admin'");
		$this->acl->addRole(new \Phalcon\Acl\Role('tester'));
		$this->acl->addRole(new \Phalcon\Acl\Role('user'));
		$this->acl->addRole(new \Phalcon\Acl\Role('admin'));
		$I->amGoingTo("make tester inherits from user");
		$this->acl->addInherit('tester', 'user');

		$I->amGoingTo("Grant 'add' access to 'user'");
		$this->acl->allow('user', 'resource', 'add');
		$I->amGoingTo("Grant 'edit' access to 'user'");
		$this->acl->allow('tester', 'resource', 'edit');
		$I->amGoingTo("Grant 'delete' access to 'admin'");
		$this->acl->allow('admin', 'resource', 'delete');

		$I->wantToTest("that 'tester' is allowed to 'add' and 'edit', but not 'delete'");
		$this->assertTrue($this->acl->isAllowed('tester', 'resource', 'add'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'edit'));
        $this->assertFalse($this->acl->isAllowed('tester', 'resource', 'delete'));

		$I->amGoingTo("make admin inherits from user");
		$this->acl->addInherit('admin', 'user');
		$I->amGoingTo("make tester inherits from admin");
		$this->acl->addInherit('tester', 'admin');
		$I->wantToTest("that 'tester' is allowed to 'add' and 'edit' and 'delete'");
		$this->assertTrue($this->acl->isAllowed('tester', 'resource', 'add'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'edit'));
        $this->assertTrue($this->acl->isAllowed('tester', 'resource', 'delete'));
	}

	public function testAvoidRecursiveInherits()
	{

	}

	public function testClearUselessRows()
	{
		
	}
}
