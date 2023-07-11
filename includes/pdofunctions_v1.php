<?php 
	date_default_timezone_set("Asia/Kolkata");
	function ConnectPDO() {
		$DatabaseName = "gitsociety";
		$Password     = "";  //H1ndust@n"GITC00P1979";
	$options=array( 
	    PDO::ATTR_CURSOR                    =>  PDO::CURSOR_SCROLL,
	    PDO::ATTR_PERSISTENT                =>  FALSE,
	    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY  =>  TRUE,
	    PDO::MYSQL_ATTR_INIT_COMMAND        =>  'SET NAMES \'utf8\' COLLATE \'utf8_unicode_ci\', @@sql_mode = STRICT_ALL_TABLES, @@foreign_key_checks = 1');
		try {
			
	        $db = new PDO("mysql:host=localhost:3306;dbname=$DatabaseName;charset=utf8", 'root', $Password,$options);
    		$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			return $db;
		} catch (Exception $ex) {
			//echo $ex->getMessage();
		}
	} 
	function getSingleField($db,$Sql) {
			
		try {
	      	$stmt = $db->prepare($Sql);
	      	$stmt->execute();
	      	$result = $stmt->fetch(PDO::FETCH_COLUMN);    //fetchColumn();
	      	return $result;
	   	} catch (Exception $ex) {
	      	return $ex->getMessage();
	  	}		
	}
	function getResultSet($db,$Sql) {
	   	try {
	      	$stmt = $db->prepare($Sql);
	      	$stmt->execute();
	      	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);    //fetchColumn();
	      	return $result;
	      
	   	} catch (Exception $ex) {
	      	//echo $ex->getMessage();
	   	}
	}	
	function insert($db,$TableName,$values = array()) {
		// check if $values set
		if(count($values)) {
			$fields = array_keys($values);
			$value = '';
			$x = 1;
			foreach($values as $field) {
				$value .="?";
				if($x < count($values)) {
					$value .= ", ";
				}
				$x++;
			}
			//print_r($fields);
			// generate sql statement
			//$sql = "INSERT INTO $TableName (`" . implode('`,`', $fields) ."`)";
			$sql = "INSERT INTO $TableName (" . implode(',', $fields) .")";
			$sql .= " VALUES({$value})";
			//echo $sql;
			$stmt = $db->prepare($sql);
			//print_r($values);
	      	$stmt->execute(array_values($values));				
			return true;
		}
		return false;
	}
	function update($db,$TableName,$values = array(),$where) {
		// check if $values set
		if(empty($where)) {
			return false;
		}
		//echo "in update:";
		//print_r($values);
		if(count($values)) {
			// generate sql statement
			$FormUpdateStmt = "";
			foreach($values as $key => $value) {
				$FormUpdateStmt.= $key . "='" . $value ."'," ;
			}
			// remove last comma
			$FormUpdateStmt = substr($FormUpdateStmt,0,-1);
			//echo "FormUpdateStmt: ".$FormUpdateStmt;
			$sql  = "UPDATE $TableName Set $FormUpdateStmt Where $where";
			//echo $sql;
			$stmt = $db->prepare($sql);
	      	$stmt->execute();				
			return true;
		}
		return false;
	}

	function build_table($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
		$rs  = $db->query($Sql,PDO::FETCH_ASSOC);
		$meta 	= $rs->getColumnMeta(0);
		//var_dump($meta);
		for ($i = 0; $i < $rs->columnCount(); $i++) {
    		$col = $rs->getColumnMeta($i);
    		$columns[] = $col['name'];
    		$columntypes[] = $col['native_type'];
		}
		//print_r($columns);		
				
		$Table = "<table id='gridtable' class='table table-striped table-responsive table-bordered table-condensed'>";
		$Table .= "<thead><tr style='background-color:yellow;'>";
		if($SerialNoReqd=='SNo') {
			$Table .= "<td>SNo</td>";
			foreach ($columns as $field)
			{
		        $Table .= "<td>".$field."</td>";
			}
			if($EditReqd=='Yes'){
		        $Table .= "<td>Edit</td>";
			}
			if($DelReqd=='Yes'){
		        $Table .= "<td>Del</td>";
			}
		}
		$Table .= "</tr></thead><tbody>";
		$SerialNo=1;
		foreach($rs as $row) {
			//print_r($row);
			$PrimaryFieldVal = $row[$PrimaryID];
			$Table .= "<tr>";
			if($SerialNoReqd=='SNo') {
		        $Table .= "<td>".$SerialNo."</td>";
			}
			foreach($row as $Field=>$Value) {
				//if strstr($Field,"Date"){
			    //    $Table .= "<td>".date("d-m-Y",strtotime($Value))."</td>";
				//} else{
			    $Table .= "<td>".$Value."</td>";
				//}
			}
			if($EditReqd=='Yes'){
		        $Table .= "<td><button class='btn btn-danger btn-sm' onclick=js_editsharetran('".$PrimaryFieldVal."') >Edit</button></td>";
			}
			if($DelReqd=='Yes'){
		        $Table .= "<td><button class='btn btn-danger btn-sm' onclick=js_delsharetran('".$PrimaryFieldVal."') >Del</button></td>";
			}
			$Table .= "</tr>";
			$SerialNo++;
		}
		$Table .= "</tbody></table>";
		return $Table;
		
	}	
	function build_table_bankentries($db,$Sql,$SerialNoReqd,$EditReqd='No',$DelReqd='No',$PrimaryID) {
		$rs  = $db->query($Sql,PDO::FETCH_ASSOC);
		$meta 	= $rs->getColumnMeta(0);
		//var_dump($meta);
		for ($i = 0; $i < $rs->columnCount(); $i++) {
    		$col = $rs->getColumnMeta($i);
    		$columns[] = $col['name'];
    		$columntypes[] = $col['native_type'];
		}
		//print_r($columns);		
				
		$Table = "<table id='gridtable' class='table table-striped table-responsive table-bordered table-condensed'>";
		$Table .= "<theda><tr style='background-color:yellow;'>";
		if($SerialNoReqd=='SNo') {
			$Table .= "<td>SNo</td>";
			foreach ($columns as $field)
			{
		        $Table .= "<td>".$field."</td>";
			}
			if($EditReqd=='Yes'){
		        $Table .= "<td>Edit</td>";
			}
			if($DelReqd=='Yes'){
		        $Table .= "<td>Del</td>";
			}
		}
		$Table .= "</tr></thead><tbody>";
		$SerialNo=1;
		foreach($rs as $row) {
			//print_r($row);
			$PrimaryFieldVal = $row[$PrimaryID];
			$Table .= "<tr>";
			if($SerialNoReqd=='SNo') {
		        $Table .= "<td>".$SerialNo."</td>";
			}
			foreach($row as $Field=>$Value) {
		        $Table .= "<td>".$Value."</td>";
			}
			if($EditReqd=='Yes'){
		        $Table .= "<td><button class='btn btn-danger btn-sm' onclick=js_editbanktran('".$PrimaryFieldVal."') >Edit</button></td>";
			}
			if($DelReqd=='Yes'){
		        $Table .= "<td><button class='btn btn-danger btn-sm' onclick=js_delbanktran('".$PrimaryFieldVal."') >Del</button></td>";
			}
			$Table .= "</tr>";
			$SerialNo++;
		}
		$Table .= "</tbody></table>";
		return $Table;
		
	}	
	function export2excel($db,$Sql,$SerialNoReqd) {
		$rs  = $db->query($Sql,PDO::FETCH_ASSOC);
		$meta 	= $rs->getColumnMeta(0);
		//var_dump($meta);
		for ($i = 0; $i < $rs->columnCount(); $i++) {
    		$col = $rs->getColumnMeta($i);
    		$columns[] = $col['name'];
    		$columntypes[] = $col['native_type'];
		}
		//print_r($columns);		
		$Table = "";		
		if($SerialNoReqd=='SNo') {
			$Table .= "SNo\t";
			foreach ($columns as $field)
			{
		        $Table .= $field."\t";
			}
		}
		$Table .= "\n";
		$SerialNo=1;
		foreach($rs as $row) {
			//print_r($row);
			if($SerialNoReqd=='SNo') {
		        $Table .= $SerialNo."\t";
			}
			foreach($row as $Field=>$Value) {
		        $Table .= $Value."\t";
			}
			$Table .= "\n";
			$SerialNo++;
		}
		return $Table;
	}	
	function getResultSet_json($db,$Sql) {
	   	try {
	   		//echo $Sql;
	   		
	      	$stmt = $db->prepare($Sql);
	      	$stmt->execute();
	      	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);    //fetchColumn();
	      	
	      	$Str1 = json_encode($result);
	      	//echo $Str1;
	      	$Str1 = str_replace('"','',$Str1);
	      	$Str1 = str_replace(',','',$Str1);
	      	$Str1 = str_replace('[','',$Str1);
	      	$Str1 = str_replace('{','',$Str1);
			return $Str1;
			
	      
	   	} catch (Exception $ex) {
	      	echo $ex->getMessage();
	   	}
	}	
	
?>
