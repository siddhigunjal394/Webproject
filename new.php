<?php
session_start();
include 'connect.php';

// Handle Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// If the user is logged in, show CRUD operations
if (isset($_SESSION['user'])) {
    $user_email = $_SESSION['user'];

    // Handle Delete User
    if (isset($_GET['deleteid'])) {
        $id = $_GET['deleteid'];
        $stmt = $con->prepare("DELETE FROM crud WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }

    // Handle Search
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $sql = $search ? "SELECT * FROM crud WHERE name LIKE ? OR email LIKE ? OR mobile LIKE ?" : "SELECT * FROM crud";
    $stmt = $con->prepare($sql);
    if ($search) {
        $param = "%$search%";
        $stmt->bind_param("sss", $param, $param, $param);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>CRUD with Authentication</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>
    <body>
        <div class="container my-5">
            <h2>Welcome, <?php echo htmlspecialchars($user_email); ?>!</h2>
            <a href="index.php?logout=true" class="btn btn-danger mb-3">Logout</a>

            <!-- Search Form -->
            <form method="GET" class="mb-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search users..."
                        value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>

            <button class="btn btn-success mb-3">
                <a href="user.php" class="text-light text-decoration-none">Add User</a>
            </button>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>SL No</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Operations</th>
                    </tr>
                </thead>
                <tbody>

                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['name']}</td>
                            <td>{$row['email']}</td>
                            <td>{$row['mobile']}</td>
                            <td>
                                <a href='update.php?updateid={$row['id']}' class='btn btn-primary btn-sm'>Update</a>
                                <a href='index.php?deleteid={$row['id']}' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure?\");'>Delete</a>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='text-center'>No records found.</td></tr>";
                }
                ?>

                </tbody>
            </table>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// Handle Registration
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check if email exists
    $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        // Insert new user
        $stmt = $con->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $password);
        if ($stmt->execute()) {
            $_SESSION['user'] = $email;
            header("Location: index.php");
            exit();
        } else {
            $error = "Registration failed. Try again!";
        }
    }
}

// Handle Login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $stmt = $con->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user'] = $email;
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login & Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f4f4f4;
        }
        .form-container {
            width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="text-center">Register</h2>
        <form method="POST">
            <input type="text" name="name" class="form-control mb-3" placeholder="Name" required>
            <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" name="register" class="btn btn-success w-100">Register</button>
        </form>
        <hr>
        <h2 class="text-center">Login</h2>
        <form method="POST">
            <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
            <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>
            <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        </form>
        <?php if (isset($error)) echo "<p class='text-danger text-center mt-2'>$error</p>"; ?>
    </div>
</body>
</html>
