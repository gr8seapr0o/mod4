<?php
/**
 * basic class for models /
 * has only constructor witch transfer App::$db to $this->db /
 */
class Model {
    
    protected $db;
    
    /**
     * Model constructor /
     * fill db from App::db
     */
    public function __construct()
    {
        $this->db = App::$db;
    }
}