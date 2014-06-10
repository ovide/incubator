<?php namespace Phalcon\Acl\Adapter\Model;


/**
 * Description of ExtrangeResourceImplementation
 * @author Albert Ovide <albert@ovide.net>
 */
class ExtrangeResourceImplementation extends \Phalcon\Mvc\Model implements Resource
{
	protected $name;
	protected $operations = '';


	public function columnMap()
	{
		return array(
			'rsc_name'       => 'name',
			'rsc_operations' => 'operations'
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

	public function addOperations($operations)
	{
		if (!$this->operations) {
			$this->operations = implode(',', $operations);
			return;
		}
		$arrCurOps = explode(',', $this->operations);
		$merged = array_merge($arrCurOps, $operations);
		$unique = array_unique($merged);
		$this->operations = implode(',', $unique);
	}

	public function dropOperations($operations)
	{
		$arrCurOps = explode(',', $this->operations);
		$result = array_diff($arrCurOps, $operations);
		$this->operations = implode(',', $result);
	}

	public function setDescription($description) {

	}

	public function setName($name) {
		$this->name = $name;
	}

}

