<?php

namespace Config;

use PDO;

class MyPdo extends PDO {

    private $_sgbd = CONFIG['db.driver'];
    private $_host = CONFIG['db.host'];
    private $_bdd = CONFIG['db.name'];
    private $_user = CONFIG['db.user'];
    private $_password = CONFIG['db.password'];

    public function __construct()
    {
        $sgbdHost = $this->_sgbd . ':host=' . $this->_host . ';dbname=' . $this->_bdd . ';charset=UTF8';
        parent::__construct($sgbdHost, $this->_user, $this->_password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

}
