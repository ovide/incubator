<?php namespace Phalcon\Acl\Adapter\Model;


/**
 * Description of ExtrangeResourceImplementation
 * @author Albert Ovide <albert@ovide.net>
 */
class KynkiResource extends \Phalcon\Mvc\Model implements Resource
{
	protected $name;
	protected $operation = '';

	public function columnMap()
	{
		return array(
			'rsc_name'      => 'name',
			'rsc_operation' => 'operation'
		);
	}
	public function initialize()
	{
		$this->setSource('acl_resource');
		//TODO Per alguna raó cal aquest unset o sempre repeteix la clau del primer registre
		unset($this->_uniqueParams);
	}
    public function getDescription()
    {
		return '';
    }


    public function getName()
    {
		return $this->name;
    }
	/**
     * @param string[] $operations
     */
	public function addOperations($operations)
	{
		$manager = new \Phalcon\Mvc\Model\Transaction\Manager();
		$transaction = $manager->get();
		foreach ($operations as $operation) {
			$resource = new KynkiResource();
			$resource->setTransaction($transaction);
			$resource->name = $this->name;
			$resource->operation = $operation;
			if (!$resource->save())
				$transaction->rollback();
		}
		$transaction->commit();
	}

	public function dropOperations($operations)
	{
		$manager = new \Phalcon\Mvc\Model\Transaction\Manager();
		$transaction = $manager->get();
		foreach ($operations as $operation) {
			$resource = KynkiResource::findFirst(array('name = :name: AND operation = :operation:',
				'bind' => array(
					'name'      => $this->name,
					'operation' => $operation
				)
			));
			if (!$resource) continue;
			$resource->setTransaction($transaction);
			if (!$resource->delete())
				$transaction->rollback();
		}
		$transaction->commit();
	}

	public function setDescription($description) {

	}

	public function setName($name) {
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
	/**
	 * @return string[]
	 */
	public function getOperations()
	{
		$operations = array();
		$rows = KynkiResource::find(array('name= :name:', 'bind' => array('name' => $this->name)));
		foreach ($rows as $row)
			$operations[] = $row->operation;
		return $operations;
	}
}
