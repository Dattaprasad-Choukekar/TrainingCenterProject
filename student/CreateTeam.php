<?php
include ('../private/session.php');
require_once ("../private/utils.php");
require_once ("../WS/demodb.php");

if ($_SESSION['login_user_type'] != STUDENT) {
    header('Location: index.php'); // Redirecting To Login Page
}




try {
            $db = DemoDB::getConnection();
            $sql = "SELECT id FROM project WHERE id not in (select project_id from team where owner_id='" + $_SESSION["login_user_id"]
             + "');";
            //$sql = "SELECT id FROM project;";
            $stmt = $db->prepare($sql);
           // $stmt->bindValue(":team_owner_id", $_GET["team_owner_id"], PDO::PARAM_INT);
            $ok = $stmt->execute();
            if ($ok) {
                $nb = $stmt->rowCount();
                

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row != null) {
                       print_r($row);
                       echo 'dddd';
                    }
                }

            } else {
                //$this->exit_error(500, print_r($db->errorInfo(), true));
            }
        }
        catch (PDOException $e) {
            //$this->exit_error(500, $e->getMessage());
        }







?>



<html lang="en">
<head>
<?php require '../base/head.html'; ?>
<title>Create Team</title>
</head>

<body>
<?php require '../base/top.html'; ?>


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

<div class="row placeholders"></div>

<?php require '../base/top.html'; ?>

<h2 class="sub-header">Create Team</h2>
<form id="form" class="form-horizontal" role="form" action="" method="post">
<div class="form-group  has-error">
  <label class="control-label col-sm-2" id="error_lbl_id"></label>

</div>
	<div class="form-group">
		<label class="control-label col-sm-2" for="name">Name:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="name" id="name"
				placeholder="Enter name" required>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2" for="summary">Summary:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="summary" id="summary"
				placeholder="Enter summary" required>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2" for="project_id">Project Id:</label>
        
        
		<div class="col-sm-10">
        	<select class="form-control" id="project_id" name="project_id">
        <?php 
        $result = getProjectIds();
        if (empty($result)) {
            ?>
             <label class="control-label col-sm-2" id="error_lbl_id">You can not create any team!</label>
        <?php
        }
        
        foreach ($result as $value) {
                echo "<option  value='".$value['id'] ."'>".$value['title']. "</option>";
            }
        ?>
        </select>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-sm-offset-2 col-sm-10">
			<button id="submit" type="submit" class="btn btn-default">Submit</button>
		</div>
	</div>
</form>

<?php 


function getProjectIds() {
try {
            $db = DemoDB::getConnection();
            $sql = "select id, title FROM project WHERE id not in (select project_id from team where team_owner_id=" . $_SESSION["login_user_id"]." )"
            ." and class_id=" . $_SESSION["login_user_class_id"].";";
            //$sql = "SELECT id FROM project;";
            $stmt = $db->prepare($sql);
           // $stmt->bindValue(":team_owner_id", $_GET["team_owner_id"], PDO::PARAM_INT);
            $ok = $stmt->execute();
            if ($ok) {
                $nb = $stmt->rowCount();
                
                $result = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($row != null) {
                       array_push($result, $row);
                       //$result[$row["id"]][] =$row; 
                       //return $row;
                    }
                }
                return $result;

            } else {
                echo 'dddd';
                $erreur = $stmt->errorInfo();
                    print_r( $erreur);
                    // si doublon
                    if ($erreur[1] == 1062) {
                       print_r($erreur[1]);
                    } else {
                        print_r($erreur[2]);
                    }
                //$this->exit_error(500, print_r($db->errorInfo(), true));
            }
        }
        catch (PDOException $e) {
            //$this->exit_error(500, $e->getMessage());
        }

}

?>


</div>

<script>
      $(document).ready(function () {    
		  
        // Get data from server when click on Reload button
        $("#submit").click(function (event) {
		  event.preventDefault();
		  var body = $("#form").serialize();
		  body = body + "&team_owner_id=<?= $_SESSION['login_user_id'] ?>";
		  console.log(body);
          $.ajax({
            // HTTP mthod
            type: "PUT",
            url: "/TCP/WS/TeamResource.php?id=*",
            beforeSend: function (xhr) {
             // xhr.setRequestHeader("Authorization", auth);
              //xhr.setRequestHeader('Authorization', 'Basic ' + btoa("admin" + ":" + "admin"));
            },
            // return type
			data:body,
            // error processing
            // xhr is the related XMLHttpRequest object
            error: function (xhr, string) {
				console.log(xhr.status );
				console.log(xhr.statusText );
				console.log(xhr.responseText );
                $("#error_lbl_id").text("Error occured");
                
				//var msg = (xhr.status == 404    ? "Person   not found": "Error : " + xhr.status + " " + xhr.statusText;
              //$("#message").html(msg);
            },
            // success processing (when 200,201, 204 etc)
            success: function (data) {
                
				window.location="/TCP/student/MyTeams.php";
              //$("#name").val(data.name);
              //$("#message").html("Person loaded")
            }
          });
		  
		  
		  
		 
        }
		
		
		);


      });

    </script>
<?php require '../base/bottom.html'; ?>
</body>
</html>