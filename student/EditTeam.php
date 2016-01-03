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
<title>Edit Team</title>
</head>

<body>
<?php require '../base/top.html'; ?>


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

<div class="row placeholders"></div>

<?php require '../base/top.html'; ?>

<h2 class="sub-header">Edit Team</h2>
<form id="form" class="form-horizontal" role="form" action="" method="post">
<div class="form-group  has-error">
  <label class="control-label col-sm-2" id="error_lbl_id"></label>

</div>
	<div class="form-group">
        <input type="hidden" id="class_id" value="<?=$_SESSION["login_user_class_id"] ?>"/>
         <input type="hidden" id="login_student_id" value="<?=$_SESSION["login_user_id"] ?>"/>
         <input type="hidden" id="curr_project_id" value=""/>
        
		<label class="control-label col-sm-2" for="name">Name:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="name" id="name_id"
				placeholder="Enter name" required>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2" for="summary">Summary:</label>
		<div class="col-sm-10">
			<input type="text" class="form-control" name="summary" id="summary_id"
				placeholder="Enter summary" required>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-2" for="project_id">Project Id:</label>
        
        
		<div class="col-sm-10">
            <label class="control-label col-sm-2" id="project_select_id" name="project_id" >Project Id:</label>
		</div>
	</div>
    <div class="form-group" >
		<label class="control-label col-sm-2" id="temp_res" for="Students">Students:</label>
		<div class="checkbox col-sm-10" id="student_select_div">

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
            $sql = "select id, title FROM project WHERE id not in (select project_id from team where team_owner_id=" . $_SESSION["login_user_id"].") and id not in (select project_id from team_membership where student_id="
            . $_SESSION["login_user_id"].")"
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



    

    
    
    
    
    
    function getStudentsOfClass(class_id, projectId) {
          $.ajax({
          
            type: "GET",
            url: "/TCP/WS/StudentResource.php?id=*&class_id=" + class_id,

			data:"",
            error: function (xhr, string) {
				console.log(xhr.status );
				console.log(xhr.statusText );
				console.log(xhr.responseText );
                $("#error_lbl_id").text("Error occured");
            },
           
            success: function (data) {
                  // Remove current studdent from it
                  var curr_std_id = $("#login_student_id").val();
                  delete data[curr_std_id];
                 getStudentsOfProject(data, projectId);
            }
          });
    };
    
    
    function getStudentsOfProject(student_data, projectId) {
        
          $.ajax({
          
            type: "GET",
            url: "/TCP/WS/TeamResource.php?id=*&project_id=" + projectId,

			data:"",
            error: function (xhr, string) {
				console.log(xhr.status );
				console.log(xhr.statusText );
				console.log(xhr.responseText );
                $("#error_lbl_id").text("Error occured");
            },
           
            success: function (data) {
                console.log(student_data);
                
                 for (var key in data) {
                     if (data.hasOwnProperty(key)) {
                            var team  = data[key];
                             //console.log(team);
                            if (student_data.hasOwnProperty(team.team_owner_id)) {
                                //console.log("-----------" + team.team_owner_id);
                                delete student_data[team.team_owner_id];
                            }
                            if (team.members) {
                               for (var ele in team.members) {
                                    if (student_data.hasOwnProperty(ele)) {
                                        
                                        delete student_data[ele];
                                     }
                               }
                            }
                     }  
                    
                } 
                $("#student_select_div").empty();
                for (var ele in student_data) {
                   // $("#student_select_div").text("");;
                    $("#student_select_div").append("<label><input  type='checkbox' name='members_id[]' value='" + student_data[ele]['student_id'] +"'>" +student_data[ele]['name']  +"</input></label>"       
                     );
                      //$("#temp_res").text($("#temp_res").text() + student_data[ele]["name"]);
                      
                }  
                console.log(student_data);  
            }
          });
    };
    

    
    

      $(document).ready(function () {  
        
                  $.ajax({
            // HTTP mthod
            type: "GET",
            url: "/TCP/WS/TeamResource.php?id=<?=$_GET['team_id']?>",
            beforeSend: function (xhr) {
             // xhr.setRequestHeader("Authorization", auth);
              //xhr.setRequestHeader('Authorization', 'Basic ' + btoa("admin" + ":" + "admin"));
            },
            // return type
			data:'',
            // error processing
            // xhr is the related XMLHttpRequest object
            error: function (xhr, string) {
				console.log(xhr.status );
				console.log(xhr.statusText );
				console.log(xhr.responseText );
                $("#error_lbl_id").text("Error occured: " + xhr.responseText);
                
				//var msg = (xhr.status == 404    ? "Person   not found": "Error : " + xhr.status + " " + xhr.statusText;
              //$("#message").html(msg);
            },
            // success processing (when 200,201, 204 etc)
            success: function (data) {
                
				$("#name_id").val(data['name']);
                	$("#summary_id").val(data['summary']);
                    	$("#curr_project_id").val(data['project_id']);
              //$("#name").val(data.name);
              //$("#message").html("Person loaded")
            }
          });
        
        
        
        
        
        
        $("#project_select_id").on('change', function(event) {
       
       var class_id = $("#class_id").val();
         var projectId = this.options[this.selectedIndex].value;
        console.log("Class ID: "+class_id);
        getStudentsOfClass(class_id, projectId);
        console.log("Project ID: "+projectId);
    });
    
        
        $('#project_select_id').trigger('change');
		  
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
                $("#error_lbl_id").text("Error occured: " + xhr.responseText);
                
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