<?php

class CM_FormField_TreeSelect extends CM_FormField_Abstract {

    /** @var CM_Tree_Abstract */
    protected $_tree;

    public function validate($userInput, CM_Response_Abstract $response) {
        if (!$this->_tree->findNodeById($userInput)) {
            throw new CM_Exception_FormFieldValidation('Invalid value');
        }
        return $userInput;
    }

    public function prepare(array $params) {
        $this->setTplParam('tree', $this->_tree);
    }

    protected function _setup() {
        $this->_tree = $this->_params->get('tree');
    }

    /**
     * @param CM_Tree_Abstract $tree
     * @return static
     */
    public static function create(CM_Tree_Abstract $tree) {
        return new static(array(
            'tree' => $tree,
        ));
    }
}
