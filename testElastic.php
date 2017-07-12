<?php

namespace TestElastic;
error_reporting(E_ALL);

use Elasticsearch\ClientBuilder;
use \TestElastic\Tools\DbInt;

require_once('vendor/autoload.php');
require_once('Tools/DbInt.php');

class TestElastic {
	private $client = null;
	private $db = null;

	public function __construct() {
		$logger = ClientBuilder::defaultLogger('test.log');
		$connectionPool = '\Elasticsearch\ConnectionPool\StaticNoPingConnectionPool';            

		$this->client = ClientBuilder::create()
									->setLogger($logger)
									->setConnectionPool($connectionPool)
									->setRetries(5)
									->build();
		$this->db = new DbInt();
	}

	public function tryConnection() {
		$query = "SELECT * FROM `first`.`user` WHERE 1";

		$result = $this->db->query($query);

		if (!empty($result)) {
			$this->createIndex('user');

			foreach ($result as $row) {
			    $params = [
				    'index' => 'user',
				    'type' => 'data',
				    'id' => '1',
				    'body' => [
				    			'id' => $row[0],
				    			'firstname' => $row[1],
				    			'lastname' => $row[2],
				    			'age' => $row[3],
				    			'birthdate' => $row[4]
				    		  ]
				];

				$response = $this->client->index($params);
				echo print_r($response);
			}
		} else {
			echo "Empty results...\n";
		}
	}

	private function createIndex($index) {
		$params = [
		    'index' => $index,
		    'body' => [
		        'settings' => [
		            'number_of_shards' => 2,
		            'number_of_replicas' => 0
		        ]
		    ]
		];

		$response = $this->client->indices()->create($params);
		echo print_r($response);
	}
}

$test = new TestElastic();
$test->tryConnection();