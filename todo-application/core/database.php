<!DOCTYPE html>
<html>

<head>
    <style type="text/css" media="screen">
        input.largerCheckbox {
            width: 20px;
            height: 20px;
        }
    </style>
</head>

</html>

<?php
session_start();
if (isset($_POST['Delete'])) {
    if (!empty($_POST['check_list'])) {
        $tasks = $_POST['check_list'];
        $length = count($tasks);
        for ($i = 0; $i < $length; $i++) {
            deleteTodoItem($_SESSION['username'], $tasks[$i]);
        }
    }
} else if (isset($_POST['Save'])) {
    $conn = connectdatabase();
    $sql = "UPDATE todo.tasks SET done = 0";
    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);

    if (!empty($_POST['check_list'])) {
        $tasks = $_POST['check_list'];
        $length = count($tasks);
        if ($length > 0) {
            for ($i = 0; $i < $length; $i++) {
                updateTask($tasks[$i], 0);
            }
        }
    }
}

function connectdatabase()
{
    return mysqli_connect("127.0.0.1:3306", "root", "", "todo");
}

function loggedin()
{
    return isset($_SESSION['username']);
}

function logout()
{
    $_SESSION['error'] = "&nbsp; Succesfully logout !!";
    unset($_SESSION['username']);
}

function spaces($n)
{
    for ($i = 0; $i < $n; $i++)
        echo "&nbsp;";
}

function userexist($username)
{
    $conn = connectdatabase();
    $sql = "SELECT * FROM todo.users WHERE username = '" . $username . "'";
    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);

    if (!$result || mysqli_num_rows($result) == 0) {
        return false;
    }
    return true;
}

function validuser($username, $password)
{
    $conn = connectdatabase();
    $sql = "SELECT * FROM todo.users WHERE username = '" . $username . "'AND password = '" . $password . "'";
    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);

    if (!$result || mysqli_num_rows($result) == 0) {
        return false;
    }
    return true;
}

function error()
{
    if (isset($_SESSION['error'])) {
        echo $_SESSION['error'];
        unset($_SESSION['error']);
    }
}

function updatepassword($username, $password)
{
    $conn = connectdatabase();
    $sql = "UPDATE todo.users SET password = '" . $password . "' WHERE username = '" . $username . "';";
    $result = mysqli_query($conn, $sql);

    $_SESSION['error'] = "<br> &nbsp; Password Updated !! ";
    header('location:home.php');
}

function deleteaccount($username)
{
    $conn = connectdatabase();
    $sql = "DELETE FROM todo.tasks WHERE username = '" . $username . "';";
    $result = mysqli_query($conn, $sql);

    $sql = "DELETE FROM todo.users WHERE username = '" . $username . "';";
    $result = mysqli_query($conn, $sql);

    $_SESSION['error'] = "&nbsp; Account Deleted !! ";
    unset($_SESSION['username']);
    header('location:login.php');
}

function createUser($username, $password)
{
    if (!userexist($username)) {
        $conn = connectdatabase();
        $sql = "INSERT INTO todo.users (username, password) VALUES ('" . $username . "','" . $password . "')";
        $result = mysqli_query($conn, $sql);

        $_SESSION["username"] = $username;
        header('location:home.php');
    } else {
        $_SESSION['error'] = "&nbsp; Username already exists !! ";
        header('location:newuser.php');
    }
}

function isValid($username, $password, $usercaptcha)
{
    $capcode = $_SESSION['captcha'];

    if (!strcmp($usercaptcha, $capcode)) {
        if (validuser($username, $password)) {
            $_SESSION["username"] = $username;
            header('location:home.php');
        } else {
            $_SESSION['error'] = "&nbsp; Invalid Username or Password !! ";
            header('location:login.php');
        }
    } else {
        $_SESSION['error'] = "&nbsp; Invalid captcha code !! ";
        header('location:login.php');
    }
}

function getTodoItems($username)
{

    $conn = connectdatabase();

    $sql = "SELECT COUNT(tasks.taskid) FROM tasks WHERE username = '" . $username . "' AND done = 0";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);
    $inprogress = $row["COUNT(tasks.taskid)"];

    echo "<div style='display: flex; justify-content: space-around; margin: 10px 20px 0px 20px; font-size: 18px; color: #333'>
		<span style='color: black'>In progress ($inprogress)</span>
		<span style='cursor:pointer; color: black' onclick=\"location.href='addtask.php'\"><i class='fa-solid fa-circle-plus'></i>Add New Task</span>
	</div>";

    $sql = "SELECT * FROM tasks WHERE username = '" . $username . "' ORDER BY tasks.taskid DESC";

    $result = mysqli_query($conn, $sql);


    // echo "<form method='POST'>";
    // echo "<pre>";
    echo "<div class='task-card-container'>";

    if ($result and mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {

            $taskid = $row["taskid"];
            $taskname = $row["task"];
            if ($row["done"] == 0) {
                $status = "In-Progress";
            } elseif ($row["done"] == 1) {
                $status = "Done";
            } else {
                $status = "Pending";
            }

            echo "
                <div class='task-card' id='$taskid'>
                <div class='task-card-content'>
                    <span class='title'>$taskname</span>
                    <button class='status-btn' disabled>$status</button>
                </div>
                <div class='task-card-actions'>
                    <img src='../resources/edit-icon.png' alt='Edit' class='icon dropbtn' onclick='editTask($taskid)'/>
                    <img src='../resources/delete-icon.png' alt='Delete' class='icon' onclick='deleteTask($taskid)'/>
                    <div class='dropdown'>
                        <img src='../resources/more-icon.png' alt='More' class='icon'/>
                        <div class='dropdown-content'>" .
                ($row['done'] == 0 ? "<li class='inprogress check'>In-Progress</li>" : "<li class='inprogress' onclick='progressTask($taskid)'>In-Progress</li>") .
                ($row['done'] == 1 ? "<li class='done check'>Done</a>" : "<li class='done' onclick='doneTask($taskid)'>Done</li>") .
                ($row['done'] == 2 ? "<li class='pending check'>Pending</li>" : "<li class='pending' onclick='pendingTask($taskid)'>Pending</li>") . "
                        </div>
                    </div>
                </div>
                </div>";

            // if ($row['done']) {
            //     echo "<input type='checkbox' checked class='largerCheckbox' name='check_list[]' value='" . $row["taskid"] . "'>";
            // } else {
            //     echo "<input type='checkbox' class='largerCheckbox' name='check_list[]' value='" . $row["taskid"] . "'>";
            // }
            // echo "<td> " . $row["task"] . "</td>";
            // echo "<br>";
        }
    }
    echo "</div>";
    // echo "</pre> <hr>";
    // spaces(35);
    // echo "<input type='submit' name='Delete' value='Delete'/>";
    // spaces(10);
    // echo "<input type='submit' name='Save' value='Save'/>";
    // echo "</form>";
    // echo "<br><br>";
    mysqli_close($conn);
}

function addTodoItem($username, $todo_text)
{
    $conn = connectdatabase();
    $sql = "INSERT INTO todo.tasks(username, task, done) VALUES ('" . $username . "','" . $todo_text . "',0);";
    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);
}

function deleteTodoItem($username, $todo_id)
{
    $conn = connectdatabase();
    $sql = "DELETE FROM todo.tasks WHERE taskid = " . $todo_id . " and username = '" . $username . "';";
    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);
}

function updateTask($taskid, $status_code)
{
    $conn = connectdatabase();
    $sql = "UPDATE todo.tasks SET done = '$status_code' WHERE (taskid = '$taskid');";
    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);
}

function updateTaskName($taskid, $taskname)
{
    $conn = connectdatabase();
    $sql = "UPDATE todo.tasks SET task = '$taskname' WHERE (taskid = '$taskid');";
    $result = mysqli_query($conn, $sql);
    mysqli_close($conn);
}
?>