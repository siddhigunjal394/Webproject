<meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">



<?php
include 'connect.php';

if (isset($_GET['updateid'])) {
    $id = $_GET['updateid'];

    // Fetch existing user data
    $stmt = $con->prepare("SELECT name, email, mobile FROM crud WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($name, $email, $mobile);
    $stmt->fetch();
    $stmt->close();

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_name = $_POST['name'];
        $new_email = $_POST['email'];
        $new_mobile = $_POST['mobile'];

        // Update the user record
        $stmt = $con->prepare("UPDATE crud SET name=?, email=?, mobile=? WHERE id=?");
        $stmt->bind_param("sssi", $new_name, $new_email, $new_mobile, $id);
        
        if ($stmt->execute()) {
            header("Location: index.php"); // Redirect back to the main page after update
            exit();
        } else {
            echo "Error updating record.";
        }
        $stmt->close();
    }
} else {
    echo "Invalid request.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Update User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h2>Update User</h2>
        <form method="POST">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            <div class="mb-3">
                <label>Mobile</label>
                <input type="text" name="mobile" class="form-control" value="<?php echo htmlspecialchars($mobile); ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>










