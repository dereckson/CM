<?php

class CM_FormField_FileImage extends CM_FormField_File {

    protected function _getAllowedExtensions() {
        return array('jpg', 'jpeg', 'gif', 'png');
    }

    public function validateFile(CM_File $file) {
        parent::validateFile($file);

        try {
            new CM_File_Image($file);
        } catch (CM_Exception $e) {
            throw new CM_Exception_FormFieldValidation('Invalid image');
        }
    }
}
