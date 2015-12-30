<?php


function getClassData() {
  try {
        $db = DemoDB::getConnection();
        $sql = "select * from class";
        $stmt = $db->prepare($sql);		
        $ok = $stmt->execute();
		$result = array();
        if ($ok) {
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			if ($row != null) {
				$result[] = $row ;
			}
		  }  
		  return $result;
		 }
        else {
        
        }
      }
      catch (PDOException $e) {
         echo $e->getMessage();
      }
	  
}

function getClassDatabyId($id) {
  try {
        $db = DemoDB::getConnection();
        $sql = "select name from class where id=:id";
        $stmt = $db->prepare($sql);
		$stmt->bindValue(":id", $id);		
        $ok = $stmt->execute();
		$result = array();
        if ($ok) {
			$row = $stmt->fetch(PDO::FETCH_ASSOC);
			if ($row != null) {
				return $row["name"];
			} 

		 }
   
      }
      catch (PDOException $e) {
         echo $e->getMessage();
      }
	  
}


?>