<?php
include('private/session.php');

if($_SESSION['login_user_type']!=TRAINER){
header('Location: index.php'); // Redirecting To Login Page
}
?>



<html lang="en">
<head>
<?php require 'base/head.html';?>
<title>Projects List</title>
</head>

<body>
<?php require 'base/top.html';?>


<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">

<div class="row placeholders"></div>

<?php require 'base/top.html';?>

<!-- <h2 class="sub-header">List of Identities</h2> -->
<div class="table-responsive">
	<table class="table table-striped">
		<thead>
			<tr>
				<th>Id</th>
				<th>Title</th>
				<th>Subject</th>
				<th>Creation Date</th>
				<th>Deadline</th>
				<th>Edit</th>
				<th>Delete</th>
			</tr>
		</thead>

		<tbody>

				<tr id="" />"
			
					<td><c:out value="${identity.id}" /></td>
					<td><c:out value="${identity.firstName}" /></td>
					<td><c:out value="${identity.lastName}" /></td>
					<td><c:out value="${identity.email}" /></td>
					<fmt:formatDate value="${identity.birthDate}" pattern="yyyy-MM-dd"
						var="formattedBirthDate" />
					<td><c:out value="${formattedBirthDate}" /></td>
					<c:forEach var="attribute" items="${attributes}">
						<td><c:out value="${identity.attributes[attribute]}" /></td>
					</c:forEach>				
					<td><a
						href="updateIdentity?id=<c:out value="${identity.id}" />"><span
							class="glyphicon glyphicon-edit" aria-hidden="true"></span></a></td>
					<td><a
						href="deleteIdentity?id=<c:out value="${identity.id}" />"><span
							class="glyphicon glyphicon-remove" aria-hidden="true"></span></a></td>
				</tr>
			</c:forEach>
		</tbody>
	</table>
</div>



</div>
<?php require 'base/bottom.html';?>
</body>
</html>