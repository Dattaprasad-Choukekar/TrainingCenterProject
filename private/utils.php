<?php


function getClassData()
{
    try {
        $db = DemoDB::getConnection();
        $sql = "select * from class";
        $stmt = $db->prepare($sql);
        $ok = $stmt->execute();
        $result = array();
        if ($ok) {
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if ($row != null) {
                    $result[] = $row;
                }
            }
            return $result;
        } else {

        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
    }

}

function getClassDatabyId($id)
{
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


function loginTrainer($user_name, $password)
{
    try {
        $db = DemoDB::getConnection();
        $sql = "SELECT * FROM TRAINER where name=:name;";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":name", $user_name);
        $ok = $stmt->execute();
        
        if ($ok) {
            $nb = $stmt->rowCount();
           
            if ($nb == 0) {
                return false;
            }
             echo 'sssssssssssssss';
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row != null) {
                if ($user_name==$password) {
                    $_SESSION['login_user_id'] = $row["id"];
                	$_SESSION['login_user_type'] = "TRAINER";                       
                    return true;
                } else {
                    return false;
                }               
                
            } else {
               return false;
            }

        } else {
            return false;
        }
    }
    catch (PDOException $e) {
        print_r($e->getMessage());
        return false;
    }
}

function loginStudent($user_name, $password)
{
    try {
        $db = DemoDB::getConnection();
        $sql = "SELECT * FROM STUDENT where name=:name;";
        $stmt = $db->prepare($sql);
        $stmt->bindValue(":name", $user_name);
        $ok = $stmt->execute();
        if ($ok) {
            $nb = $stmt->rowCount();
            if ($nb == 0) {
                return false;
            }
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row != null) {
                if ($user_name==$password) {
                    $_SESSION['login_user_id'] = $row["student_id"];
                	$_SESSION['login_user_type'] = "STUDENT";                       
                    return true;
                } else {
                    return false;
                }               
                
            } else {
               return false;
            }

        } else {
            return false;
        }
    }
    catch (PDOException $e) {
        print_r($e->getMessage());
        return false;
    }
}



?>