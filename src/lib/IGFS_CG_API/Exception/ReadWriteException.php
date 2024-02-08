<?php

namespace IGFS_CG_API\Exception;

class ReadWriteException extends IOException {
    public function __construct($url, $message) {
        parent::__construct("[" . $url . "] " . $message);
    }
}