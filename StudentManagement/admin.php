<?php
require "db_connection.php";

// Access Control
if (!isset($_COOKIE['username'])) {
    header("Location: login.php");
    exit();
}

$user_session = $_COOKIE['username'];

// Handle Department Sorting
$filter_dept = $_GET['department'] ?? "";


// Update all students department
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_all'])) {

    $new_department = $_POST['new_department'];

    if (!empty($new_department)) {

        $update_query = "UPDATE students SET department = ?";
        $stmt = mysqli_prepare($connection, $update_query);
        mysqli_stmt_bind_param($stmt, "s", $new_department);
        mysqli_stmt_execute($stmt);

        // Refresh page after update
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}



// Retrieve Student Data
if ($filter_dept !== "") {
    $query = "SELECT * FROM students WHERE department = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $filter_dept);
    mysqli_stmt_execute($stmt);
    $data_set = mysqli_stmt_get_result($stmt);
} else {
    $data_set = mysqli_query($connection, "SELECT * FROM students");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f9f9f9; }
        .main-table { width: 100%; border-collapse: collapse; background: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .main-table th, .main-table td { padding: 12px; text-align: left; border: 1px solid #ddd; }
        .main-table th { background-color: #333; color: white; }
        .main-table tr:nth-child(even) { background-color: #f2f2f2; }
        
        .btn { padding: 8px 15px; text-decoration: none; border-radius: 5px; font-size: 14px; }
        .btn-add { background: #007bff; color: white; display: inline-block; margin-bottom: 20px; }
        .btn-update { background: #28a745; color: white; }
        .btn-remove { background: #dc3545; color: white; margin-left: 8px; }
        
        .filter-section { margin: 20px 0; padding: 15px; background: #eee; border-radius: 5px; }
    </style>
</head>

<body>

<header style="display: flex; justify-content: space-between; align-items: center;">
    <h1>Admin Dashboard</h1>
    <div>
        <span>Welcome : <strong><?= htmlspecialchars($user_session) ?></strong></span> | 
        <a href="logout.php">Sign Out</a>
    </div>
</header>

<hr>


<div class="filter-section">
    <form method="POST">
        <label><strong>Update all students departments :</strong></label>
        
        <select name="new_department" required>
    <option value="">-- Select Department --</option>
    <option value="Computer Science">Computer Science</option>
    <option value="Information Technology">Information Technology</option>
    <option value="Software Engineering">Software Engineering</option>
    <option value="Information Systems">Information Systems</option>
    <option value="Business Administration">Business Administration</option>
    <option value="Computer Engineering">Computer Engineering</option>
    <option value="Data Science">Data Science</option>
    <option value="Cyber Security">Cyber Security</option>
    <option value="Business Information Systems">Business Information Systems</option>
    <option value="Artificial Intelligence">Artificial Intelligence</option>
</select>

        <button type="submit" name="update_all" class="btn btn-update">
            Update All
        </button>
    </form>
</div>



<table class="main-table">
    <thead>
        <tr>
            <th>ID Number</th>
            <th>Name</th>
            <th>Email</th>
            <th>Department</th>
            <th>DOB</th>
            <th>Phone</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($student = mysqli_fetch_assoc($data_set)): ?>
            <tr>
                <td><?= $student['student_number'] ?></td>
                <td><?= $student['full_name'] ?></td>
                <td><?= $student['email'] ?></td>
                <td><?= $student['department'] ?></td>
                <td><?= $student['date_of_birth'] ?></td>
                <td><?= $student['phone_number'] ?></td>
                <td>
    <a href="update_data.php?student_number=<?= $student['student_number'] ?>" class="btn btn-update">Edit</a>
    <a href="delete_student.php?student_number=<?= $student['student_number'] ?>" 
       class="btn btn-remove" 
       onclick="return confirm('Delete this student?');">Delete</a>
    <a href="insert.html" class="btn btn-add" style="margin-left:8px;">Add</a>
</td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>

<?php mysqli_close($connection); ?>

</body>
</html>
