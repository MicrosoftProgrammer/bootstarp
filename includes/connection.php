<?php

$host = "localhost";	
$dbusername = "root";
$dbpassword = "";	
$dbname = "everestphase1";

function db_connect()
{
	ob_start();
	session_start();
	ini_set('display_errors', '0');
	error_reporting(E_ALL | E_STRICT | E_NOTICE);
	global $host,$dbusername,$dbpassword,$dbname;
	if(!($link_id=mysql_pconnect($host,$dbusername,$dbpassword)))
	{
		echo("Error connecting to Host.");
		exit();
	}
	if(!mysql_select_db($dbname,$link_id))
	{
		echo("Error in selecting the Database.");
	}
	return $link_id;
}

db_connect();

$_SESSION["CompanyName"] = "Company Name";

function backup_tables($tables = '*')
{
	global $host,$dbusername,$dbpassword,$dbname;

	$link = mysql_connect($host,$dbusername,$dbpassword);
	mysql_select_db($dbname,$link);
	
	//get all of the tables
	if($tables == '*')
	{
		$tables = array();
		$result = mysql_query('SHOW TABLES');
		while($row = mysql_fetch_row($result))
		{
			$tables[] = $row[0];
		}
	}
	else
	{
		$tables = is_array($tables) ? $tables : explode(',',$tables);
	}
	
	//cycle through
	foreach($tables as $table)
	{
		$result = mysql_query('SELECT * FROM '.$table);
		$num_fields = mysql_num_fields($result);
		
		$return.= 'DROP TABLE '.$table.';';
		$row2 = mysql_fetch_row(mysql_query('SHOW CREATE TABLE '.$table));
		$return.= "\n\n".$row2[1].";\n\n";
		
		for ($i = 0; $i < $num_fields; $i++) 
		{
			while($row = mysql_fetch_row($result))
			{
				$return.= 'INSERT INTO '.$table.' VALUES(';
				for($j=0; $j<$num_fields; $j++) 
				{
					$row[$j] = addslashes($row[$j]);
					$row[$j] = ereg_replace("\n","\\n",$row[$j]);
					if (isset($row[$j])) { $return.= '"'.$row[$j].'"' ; } else { $return.= '""'; }
					if ($j<($num_fields-1)) { $return.= ','; }
				}
				$return.= ");\n";
			}
		}
		$return.="\n\n\n";
	}

	$path = "../../backup";

    if (!file_exists($path))
        mkdir($path);
	
	//save file
	$handle = fopen($path.'/db-backup-'.time().'-'.(md5(implode(',',$tables))).'.sql','w+');
	fwrite($handle,$return);
	fclose($handle);
}

?>