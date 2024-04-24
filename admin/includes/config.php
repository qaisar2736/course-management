<?php

date_default_timezone_set("Asia/Karachi");
session_start();

if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') 
  $link = "https://$_SERVER[HTTP_HOST]/"; 
else
  $link = "http://$_SERVER[HTTP_HOST]/"; 

define('URL', $link);
define('ADMIN_URL', $link . 'admin/');

/* DATABASE SETTINGS */
define('SERVERNAME', 'SERVER');
define('DB_USER', 'USER');
define('DB_PASSWORD', '');
define('DB_NAME', 'USER');
/* ../ DATABASE SETTINGS */


function clean_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

function getTextDay($day_number) {
  switch($day_number) {
    case 0:
      return 'Sunday';
      break;
    
    case 1:
      return 'Monday';
      break;

    case 2:
      return 'Tuesday';
      break;

    case 3:
      return 'Wednesday';
      break;

    case 4:
      return 'Thursday';
      break;

    case 5:
      return 'Friday';
      break;

    case 6:
      return 'Saturday';
      break;
  }
}

class DB {
	public $conn;
	
	function __construct() {
		$this->conn = mysqli_connect(SERVERNAME, DB_USER, DB_PASSWORD, DB_NAME)
			or die("<h1>Database connection failed</h1>");
	}

	function query($conn, $sql) {
		return $results = mysqli_query($conn, $sql);
	}

	function single_row($sql) {
		if (mysqli_query($this->conn, $sql)) {
		  $result = mysqli_query($this->conn, $sql);
		  if (mysqli_num_rows($result) > 0) {
			  return mysqli_fetch_assoc($result);
			} else {
			  return [];
			}
		} else {
		  echo "Error: " . $sql . "<br>" . mysqli_error($this->conn);
		}
	}

	function multiple_row($sql) {
		$array = Array();
		$results = mysqli_query($this->conn, $sql);
		while($row = mysqli_fetch_assoc($results)) {
			array_push($array, $row);
		}
		return $array;
	}
	// table , column name, value
	function insert($table, $array) {
		// $sql
		$q1 = "insert into $table ";
		$i =0;
		$col = '';
		$val= "'";
		foreach($array as $k=>$v){
			$col .= $k;
			$val .= $v;

			if($i< count($array)-1){
				$col .=', ';
				$val .= "', ";
			}
			$val .= "'";
			$i++;
		}

		$sql  = $q1."(".$col.") values (".$val.")";
		if (mysqli_query($this->conn, $sql)) {
			return true;
		} else {
			echo "Error: " . $sql . "<br>" . $this->conn->error;
		}
	}

	function update ($table, $array, $conditions) { // give value as ["id" => 3, "name" => "qaisar"] array format
		$sql = "UPDATE $table SET";
		$array_length = count($array);
		if (count($array) > 0) {
      foreach ($array as $key => $value) {
        $value = "'$value'";
        $updates[] = "$key = $value";
      }
    }
    $implode_updates_Array = implode(', ', $updates);
    if (count($conditions) > 0) {
    	foreach ($conditions as $key => $value) {
    		$value = "'$value'";
    		$conditions_array[] = "$key = $value";
    	}
    }
    $implode_conditions_Array = implode(' AND ', $conditions_array);
    $sql = "UPDATE $table SET $implode_updates_Array WHERE $implode_conditions_Array";
    if (mysqli_query($this->conn, $sql)) {
		  return true;
		} else {
		  echo "Error updating record: " . mysqli_error($this->conn);
		}
	}

	function delete($table, $array) {
		if (count($array) > 0) {
      foreach ($array as $key => $value) {
        $value = "'$value'";
        $conditions[] = "$key = $value";
      }
    }
    $imploded_array = implode(' AND ', $conditions);
		$sql = "DELETE FROM $table WHERE $imploded_array";
    if (mysqli_query($this->conn, $sql)) {
    	return true;
    } else {
    	return "Error deleting record: " . mysqli_error($this->conn);
    }
	}
}

$db = new DB;