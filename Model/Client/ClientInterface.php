<?php

namespace MageSuite\ErpConnector\Model\Client;

interface ClientInterface
{
    const LOCATION_FORMAT = '%s@%s';
    const FILE_PATH_FORMAT = '%s/%s';
    const MOVED_FILE_NAME_FORMAT = '%s/%s-%s';
    const FILE_PREFIX_DATETIME_FORMAT = 'Ymd-His-';
    const ERROR_MESSAGE_TITLE_FORMAT = '%s provider ERROR';
}
