<?php

if (!CM_Db_Db::existsColumn('cm_tmp_location', 'nameFull')) {
    CM_Db_Db::exec('ALTER TABLE `cm_tmp_location` ADD COLUMN `nameFull` varchar(480) DEFAULT NULL AFTER `name`;');

    $searchCli = new CM_Elasticsearch_Index_Cli(null, new CM_OutputStream_Stream_Output());
    $searchCli->create('location');
}
