<?php
include 'connect.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled CRUD Operation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }

        th {
            background: #007bff;
            color: white;
        }

        td {
            text-align: center;
        }

        .btn a {
            text-decoration: none;
            color: white;
        }

        .btn a:hover {
            color: #f8f9fa;
        }
    </style>
</head>
<body>

<div class="container my-5">
    <h2>CRUD User Data</h2>
    <button class="btn btn-primary mb-3">
        <a href="user.php" class="text-light text-decoration-none">Add User</a>
    </button>

    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col">SL No</th>
                <th scope="col">Name</th>
                <th scope="col">E-mail</th>
                <th scope="col">Mobile</th>
                <th scope="col">Password</th>
                <th scope="col">Operations</th>
            </tr>
        </thead>
        <tbody>

        <?php
        $sql = "SELECT * FROM `crud`";
        $result = mysqli_query($con, $sql);

        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $id = htmlspecialchars($row['id']);
                $name = htmlspecialchars($row['name']);
                $email = htmlspecialchars($row['email']);
                $mobile = htmlspecialchars($row['mobile']);
                $password = "******"; // Hide password

                echo '<tr>
                    <th scope="row">' . $id . '</th>
                    <td>' . $name . '</td>
                    <td>' . $email . '</td>
                    <td>' . $mobile . '</td>
                    <td>' . $password . '</td>
                    <td>
                        <button class="btn btn-primary btn-sm">
                            <a href="update.php?updateid=' . $id . '">Update</a>
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <a href="delete.php?deleteid=' . $id . '">Delete</a>
                        </button>
                    </td>
                </tr>';
            }
        }
        ?>

        </tbody>
    </table>
</div>

</body>
</html>
