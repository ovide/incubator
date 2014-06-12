<?php namespace Phalcon\Acl\Adapter\Model;


/**
 * Description of ExtrangeRoleImplementation
 * @author Albert Ovide <albert@ovide.net>
 */
class KynkiRole extends \Phalcon\Mvc\Model implements Role
{
	protected $description = '';
	protected $name;
	protected $inherits = '';


	public function columnMap()
	{
		return array(
			'rl_name'     => 'name',
			'rl_inherits' => 'inherits'
		);
	}

	public function notSave()
	{
		echo 'NOT SAVE'.PHP_EOL;
		foreach($this->getMessages() as $message){
			echo "ERROR ".$message->getMessage();
		}
	}

	public function initialize()
	{
		$this->setSource('acl_role');
		//TODO Per alguna raó cal aquest unset o sempre repeteix la clau del primer registre
		unset($this->_uniqueParams);
	}

    public function getDescription()
    {
		return $this->inherits;
    }

    public function getName()
    {
		return $this->name;
    }

	public function setInherit($inherit)
	{
		$this->inherits = $inherit;
	}

	public function clearInherit()
	{
		$this->inherits = '';
	}

	public function setDescription($description)
	{
		$this->description = $description;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public static function byName($name)
	{
		return self::findFirst(array('name = :name:',
			'bind'      => array('name' => $name),
			'hydration' => \Phalcon\Mvc\Model\Resultset::HYDRATE_OBJECTS
		));
	}

	public static function getAll()
	{
		return self::find(array('group' => 'name'));
	}

}

