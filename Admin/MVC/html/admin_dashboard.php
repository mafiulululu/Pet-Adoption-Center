<?php
session_start();
include '../../../User/MVC/db/db_conn.php';

// Security Check

if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header("Location: ../../../User/MVC/html/login.php");
    exit();
}

// Fetch Users
$users = $conn->query("SELECT * FROM users ORDER BY created_at DESC");

// Fetch Pets
$pets = $conn->query("SELECT * FROM pets ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Pet Adoption Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">
    <style>
        .admin-container { padding: 2rem; max-width: 1400px; margin: 0 auto; }
        .admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        .section-card { background: var(--white); border-radius: 16px; padding: 2rem; margin-bottom: 2rem; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .data-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .data-table th, .data-table td { padding: 1rem; text-align: left; border-bottom: 1px solid var(--border-color); }
        .data-table th { font-weight: 600; color: var(--gray-600); background: var(--gray-50); }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.85rem; border-radius: 6px; text-decoration: none; display: inline-block; }
        .btn-add { background: var(--primary); color: white; padding: 0.8rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; }
        .btn-delete { background: #fee2e2; color: #dc2626; }
        .badge { padding: 0.25rem 0.6rem; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge-active { background: #d1fae5; color: #059669; }
        .badge-blocked { background: #fee2e2; color: #dc2626; }
        .badge-available { background: #dbeafe; color: #2563eb; }
        .badge-adopted { background: #f3f4f6; color: #4b5563; }
    </style>
</head>
<body>

    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand">
                <span class="brand-name">Admin Dashboard</span>
            </div>
            <div class="nav-actions">
                <a href="home.php" class="btn-secondary">View Site</a>
                <a href="../../../User/MVC/html/login.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </nav>

    <div class="admin-container">
        
        <!-- Pets Management -->
        <div class="section-card">
            <div class="admin-header">
                <h2>Manage Pets</h2>
                <a href="add_pet.php" class="btn-add">+ Add New Pet</a>
            </div>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Species</th>
                            <th>Breed</th>
                            <th>Status</th>
                            <th>Last Update</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($pet = $pets->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $pet['pet_id']; ?></td>
                            <td>
                                <img src="<?php echo !empty($pet['image']) ? htmlspecialchars($pet['image']) : 'https://placekitten.com/50/50'; ?>" 
                                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            </td>
                            <td><?php echo htmlspecialchars($pet['name']); ?></td>
                            <td><?php echo htmlspecialchars($pet['species']); ?></td>
                            <td><?php echo htmlspecialchars($pet['breed']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($pet['adoption_status']); ?>">
                                    <?php echo ucfirst($pet['adoption_status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($pet['updated_at'])); ?></td>
                            <td>
                                <a href="delete_pet.php?id=<?php echo $pet['pet_id']; ?>" 
                                   class="btn-sm btn-delete"
                                   onclick="return confirm('Are you sure you want to delete this pet?');">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Users Management -->
        <div class="section-card">
            <div class="admin-header">
                <h2>Manage Users</h2>
            </div>
            <div style="overflow-x: auto;">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $users->fetch_assoc()): ?>
                        <tr>
                            <td>#<?php echo $user['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($user['status']); ?>">
                                    <?php echo ucfirst($user['status']); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>