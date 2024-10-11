<?php
include('database.php');

if (!loggedin()) {
    header("location:login.php");
}

if (isset($_POST['add-task'])) {
    if (!empty($_POST['task-name'])) {
        addTodoItem($_SESSION['username'], $_POST['task-name']);
        header("location: home.php");
    }
}

include('../view/addtaskview.html');
