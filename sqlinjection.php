<?php
include 'connect.php';

try {
    $file_db = new PDO('mysql:host=<localhost:3307></localhost:3307>;dbname=crudoperations;charset=utf8', 'root', '');
    $file_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$limit = 3; // Number of records per page
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Get search term securely
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch total count with search applied
$count_sql = 'SELECT COUNT(*) AS total FROM crud WHERE name LIKE :search OR email LIKE :search OR mobile LIKE :search';
$count_stmt = $file_db->prepare($count_sql);
$searchParam = "%$search%";
$count_stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
$count_stmt->execute();
$total_rows = $count_stmt->fetch(PDO::FETCH_ASSOC)['total'];
$total_pages = ceil($total_rows / $limit);

// Fetch paginated data with prepared statement
$sql = 'SELECT * FROM crud WHERE name LIKE :search OR email LIKE :search OR mobile LIKE :search LIMIT :offset, :limit';
$stmt = $file_db->prepare($sql);
$stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure CRUD with Search & Pagination</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #f4f4f4; font-family: Arial, sans-serif; }
        .container { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); }
        h2 { text-align: center; margin-bottom: 20px; }
        .table { background: white; border-radius: 10px; overflow: hidden; }
        .table th { background: #007bff; color: white; }
        .btn a { text-decoration: none; color: white; }
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
        .pagination a { padding: 5px 10px; text-decoration: none; border: 1px solid #007bff; color: #007bff; }
        .pagination a:hover { background: #007bff; color: white; }
        .disabled { pointer-events: none; color: gray; border-color: gray; }
    </style>
</head>
<body>

<div class="container my-5">
    <h2>Secure CRUD Operations</h2>

    <!-- Search Form -->
    <form method="GET" class="search-box mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search by name, email, or mobile..." value="<?= htmlspecialchars($search); ?>">
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
        <?php if ($users): ?>
            <?php foreach ($users as $user): ?>
                <tr>
                    <th scope="row"><?= htmlspecialchars($user['id']); ?></th>
                    <td><?= htmlspecialchars($user['name']); ?></td>
                    <td><?= htmlspecialchars($user['email']); ?></td>
                    <td><?= htmlspecialchars($user['mobile']); ?></td>
                    <td>******</td> <!-- Hide password -->
                    <td>
                        <button class="btn btn-primary btn-sm">
                            <a href="update.php?updateid=<?= htmlspecialchars($user['id']); ?>">Update</a>
                        </button>
                        <button class="btn btn-danger btn-sm">
                            <a href="delete.php?deleteid=<?= htmlspecialchars($user['id']); ?>">Delete</a>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr><td colspan="6" class="text-center">No records found.</td></tr>
        <?php endif; ?>
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
