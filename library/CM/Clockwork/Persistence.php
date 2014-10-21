<?php

class CM_Clockwork_Persistence {

    const FOLDER_NAME = 'clockwork';

    /** @var string */
    private $_context;

    /** @var DateTime[] */
    protected $_data = array();

    /**
     * @param string $context
     */
    public function __construct($context) {
        $this->_context = (string) $context;
    }

    /**
     * @param CM_Clockwork_Event $event
     * @return DateTime|null
     */
    public function getLastRuntime(CM_Clockwork_Event $event) {
        $this->_load();
        if (empty($this->_data[$event->getName()])) {
            return null;
        }
        return clone $this->_data[$event->getName()];
    }

    /**
     * @param CM_Clockwork_Event $event
     * @param DateTime           $runtime
     */
    public function setRuntime(CM_Clockwork_Event $event, DateTime $runtime) {
        $this->_load();
        $this->_data[$event->getName()] = clone $runtime;
        $this->_save();
    }

    /**
     * @return CM_File
     */
    private function _getFile() {
        return new CM_File(self::FOLDER_NAME . DIRECTORY_SEPARATOR . $this->_context . '.json', $this->_getFileSystem());
    }

    /**
     * @return CM_File_Filesystem
     */
    private function _getFileSystem() {
        return CM_Service_Manager::getInstance()->getFilesystems()->getData();
    }

    private function _load() {
        if (empty($this->_data)) {
            $file = $this->_getFile();
            if ($file->getExists()) {
                $values = CM_Params::decode($file->read(), true);
                $this->_data = \Functional\map($values, function ($timeStamp) {
                    return new DateTime('@' . $timeStamp);
                });
            }
        }
    }

    private function _save() {
        $values = \Functional\map($this->_data, function (DateTime $dateTime) {
            return $dateTime->getTimestamp();
        });
        $content = CM_Params::encode($values, true);
        $file = $this->_getFile();
        if (!$file->getExists()) {
            $filesystem = $this->_getFileSystem();
            $filesystem->ensureDirectory(self::FOLDER_NAME);
            CM_File::create($file->getPath(), $content, $filesystem);
        } else {
            $file->write($content);
        }
    }
}
