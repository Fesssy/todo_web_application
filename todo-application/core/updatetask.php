<?php
include('database.php');

if (isset($_POST['action'])) {
    if ($_POST['action'] == 'delete') {
        if (isset($_POST['taskid'])) {
            deleteTodoItem($_SESSION['username'], $_POST['taskid']);
        }
    }

    if ($_POST['action'] == 'update') {
        if (isset($_POST['taskid']) && isset($_POST['status_code'])) {
            updateTask($_POST['taskid'], $_POST['status_code']);
        }
    }

    if ($_POST['action'] == 'edit') {
        if (isset($_POST['taskid']) && isset($_POST['taskname'])) {
            updateTaskName($_POST['taskid'], $_POST['taskname']);
            header("location: home.php");
        }
    }
}
