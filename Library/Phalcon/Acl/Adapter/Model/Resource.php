<?php namespace Phalcon\Acl\Adapter\Model;


use Phalcon\Mvc\ModelInterface;
use Phalcon\Acl\ResourceInterface;

interface Resource extends ModelInterface, ResourceInterface
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
     * @param string[] $operations
     */
    public function addOperations($operations);
    /**
     * @param string[] $operations
     */
    public function dropOperations($operations);
	/**
	 * @param string $name
	 * @resturn Resource
	 */
	public static function byName($name);
	/**
	 * @return string[]
	 */
	public static function getAll();
	/**
	 * @return string[]
	 */
	public function getOperations();
}
