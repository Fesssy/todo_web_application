<?php
include('database.php');
include('../view/header.html');

if (!loggedin()) {
	header("location:login.php");
}

$username = $_SESSION['username'];

?>


<!DOCTYPE html>
<html>

<head>
	<title> TODO </title>
	<link rel="stylesheet" href="../styles/card.css">
	<script src="https://kit.fontawesome.com/<yourcode>.js" crossorigin="anonymous"></script>
</head>

<body>

	<?php
	getTodoItems($username);
	?>

	<script>
		document.getElementById("username").innerHTML = "<?php echo $username; ?>";
		const input_card = "<form action='updatetask.php' method='post' style='width:100%'>" +
			"<div class='task-card' style='box-shadow:none; width:80%'>" +
			"<div class='task-card-content' style='height: auto;'>" +
			"<input class='title input-container' name='title' value='' placeholder=''/>" +
			"<input hidden name='taskid' value=''>" +
			"</div>" +
			"<div class='task-card-actions'>" +
			"<button class='check' type='submit' name='action' value='edit'>" +
			"<i class='fa-solid fa-check'></i>" +
			"</button>" +
			"<button class='cancel' onclick='cancel()'>" +
			"<i class='fa-solid fa-xmark'></i>" +
			"</button>" +
			"</div>" +
			"</div>" +
			"</form>";

		function deleteTask(taskid) {
			const xhttp = new XMLHttpRequest();
			xhttp.open("POST", "updatetask.php");
			xhttp.onload = function() {
				// Reload the page to reflect the changes
				location.reload();
				// pop up to confirm deletion
				alert("Task deleted successfully!");
			}
			let formdata = new FormData();
			formdata.append("taskid", taskid);
			formdata.append("action", "delete");
			xhttp.send(formdata);
		}

		function progressTask(taskId) {
			// Update the task status to "Progress"
			const status_code = 0;
			updateTaskStatus(taskId, status_code);
		}

		function doneTask(taskId) {
			// Update the task status to "Done"
			const status_code = 1;
			updateTaskStatus(taskId, status_code);
		}

		function pendingTask(taskId) {
			// Update the task status to "Pending"
			const status_code = 2;
			updateTaskStatus(taskId, status_code);
		}

		function updateTaskStatus(taskid, status_code) {
			// Send a request to update the task status in the database
			const xhttp = new XMLHttpRequest();
			xhttp.open("POST", "updatetask.php");
			xhttp.onload = function() {
				// Reload the page to reflect the changes
				location.reload();
				// pop up to confirm deletion
				alert("Task updated successfully!");
			}
			let formdata = new FormData();
			formdata.append("taskid", taskid);
			formdata.append("status_code", status_code);
			formdata.append("action", "update");
			xhttp.send(formdata);
		}

		function editTask(taskid) {
			const card = document.getElementById(taskid);

			const title = card.getElementsByClassName("title")[0].innerHTML;

			const input_card = "<form action='updatetask.php' method='post' style='width:100%'>" +
				"<div class='task-card' style='box-shadow:none; width:80%'>" +
				"<div class='task-card-content' style='height: auto;'>" +
				"<input class='title input-container' name='taskname'  placeholder='" + title + "'/>" +
				"<input hidden name='taskid' value='" + taskid + "'>" +
				"</div>" +
				"<div class='task-card-actions'>" +
				"<button class='check' type='submit' name='action' value='edit'>" +
				"<i class='fa-solid fa-check'></i>" +
				"</button>" +
				"<button class='cancel' onclick='cancel()'>" +
				"<i class='fa-solid fa-xmark'></i>" +
				"</button>" +
				"</div>" +
				"</div>" +
				"</form>";

			card.innerHTML = input_card;
		}
	</script>


</body>

</html>