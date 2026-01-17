<?php
session_start();
include '../db/db_conn.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
// Fetch requests joined with pet details
$sql = "SELECT r.*, p.name as pet_name, p.image as pet_image, p.species 
        FROM adoption_requests r 
        JOIN pets p ON r.pet_id = p.id 
        WHERE r.user_id = $user_id 
        ORDER BY r.request_date DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Adoption Requests</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/home.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .requests-container { 
            max-width: 1000px; 
            margin: 3rem auto; 
            padding: 0 1.5rem; 
        }
        .page-header {
            margin-bottom: 2.5rem;
            text-align: center;
        }
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            color: #111827;
            margin-bottom: 0.5rem;
        }
        .page-subtitle {
            color: #6b7280;
            font-size: 1.1rem;
        }
        
        .request-grid {
            display: grid;
            gap: 1.5rem;
        }

        .request-card { 
            background: white; 
            border-radius: 16px; 
            padding: 1.5rem; 
            display: flex; 
            gap: 1.5rem; 
            align-items: center; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.02); 
            border: 1px solid #e5e7eb;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .request-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(0,0,0,0.05);
            border-color: #d1d5db;
        }

        .req-img { 
            width: 120px; 
            height: 120px; 
            border-radius: 12px; 
            object-fit: cover; 
            flex-shrink: 0;
        }
        
        .req-info { 
            flex: 1; 
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        
        .req-header { 
            display: flex; 
            justify-content: space-between; 
            align-items: flex-start; 
            margin-bottom: 0.5rem; 
        }
        
        .req-pet-name { 
            font-size: 1.5rem; 
            font-weight: 700; 
            color: #111827; 
        }
        
        .req-species {
            font-size: 0.9rem;
            color: #6b7280;
            font-weight: 500;
            background: #f3f4f6;
            padding: 0.2rem 0.6rem;
            border-radius: 6px;
            margin-left: 0.5rem;
            vertical-align: middle;
        }

        .req-date { 
            color: #9ca3af; 
            font-size: 0.9rem; 
            font-weight: 500;
        }
        
        .req-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .req-status { 
            display: inline-flex; 
            align-items: center;
            padding: 0.4rem 1rem; 
            border-radius: 50px; 
            font-size: 0.9rem; 
            font-weight: 600; 
            text-transform: capitalize;
        }
        
        .status-pending { background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; }
        .status-approved { background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; }
        .status-rejected { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .status-cancelled { background: #f3f4f6; color: #4b5563; border: 1px solid #e5e7eb; }
        
        .btn-cancel-req { 
            background: white; 
            color: #dc2626; 
            border: 1px solid #fee2e2; 
            padding: 0.6rem 1.2rem; 
            border-radius: 8px; 
            cursor: pointer; 
            font-weight: 600; 
            font-size: 0.9rem;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }
        
        .btn-cancel-req:hover { 
            background: #fee2e2; 
            border-color: #fecaca;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .empty-icon { font-size: 4rem; margin-bottom: 1rem; display: block; }
        .empty-text { color: #6b7280; font-size: 1.1rem; margin-bottom: 2rem; }

        @media (max-width: 640px) {
            .request-card { flex-direction: column; text-align: center; padding: 2rem; }
            .req-img { width: 100%; height: 200px; margin-bottom: 1rem; }
            .req-header { flex-direction: column; align-items: center; gap: 0.5rem; }
            .req-details { flex-direction: column; gap: 1rem; }
            .req-pet-name { font-size: 1.25rem; }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="nav-container">
            <div class="nav-brand"><span class="brand-name">Pet Adoption Center</span></div>
            <div class="nav-actions">
                <a href="home.php" class="btn-secondary">Back to Home</a>
            </div>
        </div>
    </nav>

    <div class="requests-container">
        <div class="page-header">
            <h1 class="page-title">My Adoption Requests</h1>
            <p class="page-subtitle">Track the status of your adoption applications</p>
        </div>
        
        <?php if (isset($_SESSION['message'])): ?>
            <div style="background: #ecfdf5; color: #065f46; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #a7f3d0; display: flex; align-items: center; gap: 0.5rem;">
                <span>✅</span> <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: #fef2f2; color: #991b1b; padding: 1rem; border-radius: 12px; margin-bottom: 2rem; border: 1px solid #fecaca; display: flex; align-items: center; gap: 0.5rem;">
                <span>⚠️</span> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="request-grid">
            <?php if ($result && $result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="request-card">
                        <img src="<?php echo !empty($row['pet_image']) ? htmlspecialchars($row['pet_image']) : 'https://place-puppy.com/300x300'; ?>" class="req-img" alt="Pet">
                        <div class="req-info">
                            <div class="req-header">
                                <div>
                                    <span class="req-pet-name"><?php echo htmlspecialchars($row['pet_name']); ?></span>
                                    <span class="req-species"><?php echo ucfirst($row['species']); ?></span>
                                </div>
                                <span class="req-date">Applied on <?php echo date('M d, Y', strtotime($row['request_date'])); ?></span>
                            </div>
                            
                            <div class="req-details">
                                <?php 
                                    $status = strtolower($row['status']);
                                    $statusClass = 'status-' . $status;
                                    $statusIcon = '';
                                    if($status == 'pending') $statusIcon = '⏳';
                                    if($status == 'approved') $statusIcon = '🎉';
                                    if($status == 'rejected') $statusIcon = '❌';
                                    if($status == 'cancelled') $statusIcon = '🚫';
                                ?>
                                <span class="req-status <?php echo $statusClass; ?>">
                                    <span style="margin-right: 6px;"><?php echo $statusIcon; ?></span>
                                    <?php echo ucfirst($status); ?>
                                </span>

                                <?php if ($status === 'pending'): ?>
                                    <form action="cancel_adoption.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this request? This action cannot be undone.');">
                                        <input type="hidden" name="request_id" value="<?php echo $row['request_id']; ?>">
                                        <button type="submit" class="btn-cancel-req">Cancel Request</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <span class="empty-icon">🐾</span>
                    <h3>No requests found</h3>
                    <p class="empty-text">You haven't submitted any adoption requests yet.</p>
                    <a href="home.php#categories" class="btn-secondary" style="background: linear-gradient(135deg, #FF1B6B, #45CAFF); color: white; border: none; padding: 0.8rem 1.5rem;">Browse Pets</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>