<?php
include 'connect.php'; 

$limit = 3; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get search term
$search = isset($_GET['search']) ? mysqli_real_escape_string($con, $_GET['search']) : '';

// SQL Query: Fetch records with search and pagination
if ($search) {
    $sql = "SELECT * FROM `crud` 
            WHERE `name` LIKE '%$search%' 
            OR `email` LIKE '%$search%' 
            OR `mobile` LIKE '%$search%'
            LIMIT $offset, $limit";
    $count_sql = "SELECT COUNT(*) AS total FROM `crud` 
                  WHERE `name` LIKE '%$search%' 
                  OR `email` LIKE '%$search%' 
                  OR `mobile` LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM `crud` LIMIT $offset, $limit";
    $count_sql = "SELECT COUNT(*) AS total FROM `crud`";
}

$result = mysqli_query($con, $sql);
$count_res = mysqli_query($con, $count_sql);
$total_rows = mysqli_fetch_assoc($count_res)['total'];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD with Search & Pagination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f4f4f4; font-family: Arial, sans-serif; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        table { background: white; border-radius: 10px; overflow: hidden; }
        th { background: #007bff; color: white; }
        td { text-align: center; }
        .btn a { text-decoration: none; color: white; }
        .btn a:hover { color: #f8f9fa; }
        .search-box { margin-bottom: 20px; }
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
        .pagination a { padding: 5px 10px; text-decoration: none; border: 1px solid #007bff; color: #007bff; }
        .pagination a:hover { background: #007bff; color: white; }
        .disabled { pointer-events: none; color: gray; border-color: gray; }
    </style>
</head>
<body>

<div class="container my-5">
    <h2>CRUD Operations with Search & Pagination</h2>

    <!-- Search Form -->
    <form method="GET" class="search-box">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or mobile..." value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit" class="btn btn-primary">Search</button>
        </div>
    </form>

    <button class="btn btn-success mb-3">
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
        if ($result && mysqli_num_rows($result) > 0) {
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
        } else {
            echo '<tr><td colspan="6" class="text-center">No records found.</td></tr>';
        }
        ?>

        </tbody>
    </table>

    <!-- Pagination -->
    <ul class="pagination">
        <li><a class="<?= ($page == 1) ? 'disabled' : '' ?>" href="?page=1&search=<?= urlencode($search) ?>">First</a></li>
        <li><a class="<?= ($page == 1) ? 'disabled' : '' ?>" href="<?= ($page > 1) ? '?page='.($page - 1).'&search='.urlencode($search) : '#' ?>">Previous</a></li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li><a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>" class="<?= ($i == $page) ? 'disabled' : '' ?>"><?= $i ?></a></li>
        <?php endfor; ?>

        <li><a class="<?= ($page == $total_pages) ? 'disabled' : '' ?>" href="<?= ($page < $total_pages) ? '?page='.($page + 1).'&search='.urlencode($search) : '#' ?>">Next</a></li>
        <li><a class="<?= ($page == $total_pages) ? 'disabled' : '' ?>" href="?page=<?= $total_pages ?>&search=<?= urlencode($search) ?>">Last</a></li>
    </ul>

</div>

</body>
</html>
