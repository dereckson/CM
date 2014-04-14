<?php

class CM_FormField_Password extends CM_FormField_Text {

    protected function _setup() {
        $this->_params->set('lengthMin', 4);
        $this->_params->set('lengthMax', 100);
        parent::_setup();
    }

    /**
     * @return static
     */
    public static function create() {
        return new static();
    }
}
