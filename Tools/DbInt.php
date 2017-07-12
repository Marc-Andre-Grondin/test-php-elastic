<?php

namespace TestElastic\Tools;

class DbInt {
	private $mysqli = null;
	private $stmt = null;

	private $host = '127.0.0.1';
	private $user = 'root';
	private $pass = 'In Flames We Trust 101';
	private $dbname = 'first';

	private $result = [];

	public function __construct() {
		$this->initMySQLIConnection();
		$this->initStmt();
	}

	public function prepare($query) {
		if (!$this->stmt->prepare($query)) {
		    echo "Failed to prepare statement\n";
		    return false;
		}

		return true;
	}

	public function execute() {
		$this->stmt->execute();
		$this->result = $this->stmt->get_result();
	}

	public function getNbRows() {
		if ($this->stmt->affected_rows != 0) {
			return $this->stmt->affected_rows;
		}

		return $this->stmt->num_rows;
	}

	public function getResults() {
		$return = [];

		if (!empty($this->result)) {
		    while ($row = $this->result->fetch_array(MYSQLI_NUM)) {
		        $return[] = $row;
		    }
		}

	    return $return;
	}

	public function query($query) {
		$array = [];

		if ($this->prepare($query)) {
			$this->execute();

			$array = $this->getResults();

			$this->stmt->free_result();
		}

		return $array;
	}

	private function initMySQLIConnection() {
		if ($this->mysqli == null) {
			$this->mysqli = new \mysqli("p:" . $this->host, $this->user, $this->pass, $this->dbname);

			$this->checkMySQLIConnection();
		}
	}

	private function checkMySQLIConnection() {
		if ($this->mysqli->connect_error) {
		    die("{$this->mysqli->connect_errno}: {$this->mysqli->connect_error}");
		}
	}

	private function initStmt() {
		if ($this->mysqli !== null) {
			$this->stmt = $this->mysqli->stmt_init();
		}
	}
}