<?php

abstract class CM_Paging_StreamChannelArchive_Video_Abstract extends CM_Paging_Abstract {

	protected function _processItem($item) {
		return new CM_Model_StreamChannelArchive_Video($item);
	}
}