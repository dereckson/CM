<?php

abstract class CM_Model_StorageAdapter_AbstractAdapter extends CM_Class_Abstract {

	/**
	 * @param int $type
	 * @param array $id
	 * @return array|false
	 */
	abstract public function load($type, array $id);

	/**
	 * @param array $idTypeArray [['type' => int, id' => array],...]
	 * @return array['type', 'id', 'data']|false
	 *
	 * Return Array must preserve the keys of $idTypeArray
	 */
	abstract public function loadMultiple(array $idTypeArray);

	/**
	 * @param int $type
	 * @param array $id
	 * @param array $data
	 */
	abstract public function save($type, array $id, array $data);

	/**
	 * @param int $type
	 * @param array $data
	 * @return array
	 */
	abstract public function create($type, array $data);

	/**
	 * @param int $type
	 * @param array $id
	 */
	abstract public function delete($type, array $id);
}
