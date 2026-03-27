<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

$data = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");

// Check if query was successful
if(!$data) {
    die("Error fetching categories: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AutoParts Pro | Categories</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts: Inter for corporate typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: #f5f9ff;
            overflow-x: hidden;
            color: #1e293b;
        }

        /* ---------- SIDEBAR (identical to dashboard) ---------- */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: #0f172a;
            position: fixed;
            left: 0;
            top: 0;
            color: #e2e8f0;
            transition: all 0.35s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            z-index: 1000;
            overflow-y: auto;
            box-shadow: 8px 0 32px rgba(0, 0, 0, 0.08);
            border-right: 1px solid rgba(255,255,255,0.05);
        }

        .sidebar-header {
            padding: 32px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 24px;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .logo i {
            font-size: 2rem;
            background: linear-gradient(135deg, #f97316, #facc15);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .logo span {
            background: linear-gradient(120deg, #ffffff, #facc15);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            font-weight: 700;
        }

        .nav-menu {
            padding: 0 18px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 16px;
            margin: 6px 0;
            color: #cbd5e1;
            text-decoration: none;
            border-radius: 14px;
            transition: all 0.25s ease;
            font-size: 0.95rem;
            font-weight: 500;
        }

        .nav-item i {
            width: 26px;
            font-size: 1.2rem;
            text-align: center;
        }

        .nav-item:hover {
            background: rgba(249, 115, 22, 0.12);
            color: #facc15;
            transform: translateX(4px);
        }

        .nav-item.active {
            background: linear-gradient(95deg, rgba(249,115,22,0.2), rgba(250,204,21,0.05));
            color: #facc15;
            border-left: 3px solid #f97316;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .nav-divider {
            height: 1px;
            background: rgba(255,255,255,0.08);
            margin: 18px 0;
        }

        /* Main content area */
        .main-content {
            margin-left: 280px;
            padding: 32px 40px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Top bar with welcome */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 20px;
            animation: fadeSlideUp 0.5s ease-out;
        }

        .welcome-section h1 {
            font-size: 1.9rem;
            font-weight: 700;
            background: linear-gradient(135deg, #0f172a, #334155);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.02em;
        }

        .welcome-section p {
            color: #475569;
            margin-top: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .weather-card {
            background: linear-gradient(115deg, #0f172a, #1e293b);
            padding: 12px 28px;
            border-radius: 48px;
            display: flex;
            align-items: center;
            gap: 18px;
            color: white;
            box-shadow: 0 12px 20px -12px rgba(0,0,0,0.2);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .weather-card:hover { transform: translateY(-3px); box-shadow: 0 20px 28px -12px rgba(0,0,0,0.25); }

        /* Action buttons row */
        .action-buttons {
            display: flex;
            gap: 18px;
            margin-bottom: 28px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 12px 28px;
            border-radius: 40px;
            font-weight: 600;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.25s ease;
            display: inline-flex;
            align-items: center;
            gap: 12px;
            letter-spacing: 0.2px;
        }
        .btn-primary {
            background: linear-gradient(105deg, #f97316, #facc15);
            color: #0f172a;
            box-shadow: 0 6px 14px rgba(249,115,22,0.25);
        }
        .btn-primary:hover { transform: translateY(-3px); box-shadow: 0 12px 22px rgba(249,115,22,0.35); background: linear-gradient(105deg, #facc15, #f97316);}
        .btn-outline {
            border: 1.5px solid #f97316;
            color: #f97316;
            background: white;
        }
        .btn-outline:hover { background: #fef3c7; transform: translateY(-2px); border-color: #facc15; }

        /* Stats summary cards */
        .stats-summary {
            display: flex;
            gap: 20px;
            margin-bottom: 32px;
            flex-wrap: wrap;
        }
        .stat-chip {
            background: white;
            border-radius: 60px;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03), 0 0 0 1px #eef2ff;
            font-weight: 600;
        }
        .stat-chip i {
            font-size: 1.2rem;
            color: #f97316;
        }
        .stat-chip span {
            color: #1e293b;
            font-size: 0.9rem;
        }
        .stat-chip .number {
            font-size: 1.3rem;
            font-weight: 800;
            color: #f97316;
            margin-left: 6px;
        }

        /* Data Table - Corporate Style */
        .data-table-wrapper {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.02), 0 0 0 1px #eef2ff;
            overflow-x: auto;
            animation: fadeSlideUp 0.45s ease-out;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 18px 20px;
            text-align: left;
            font-size: 0.9rem;
        }

        th {
            background: #f8fafc;
            color: #1e293b;
            font-weight: 700;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #eef2ff;
        }

        td {
            color: #334155;
            font-weight: 500;
            border-bottom: 1px solid #f0f4fa;
        }

        tr:hover td {
            background: #fefcf5;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 700;
        }
        .status-active {
            background: #e0f2e9;
            color: #0f6e3f;
        }
        .status-inactive {
            background: #fff1f0;
            color: #f97316;
        }

        .action-icons {
            display: flex;
            gap: 12px;
        }
        .action-icons a {
            color: #94a3b8;
            transition: 0.2s;
            font-size: 1.1rem;
        }
        .action-icons a:hover {
            color: #f97316;
            transform: scale(1.1);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #94a3b8;
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        /* Mobile toggle button */
        .menu-toggle {
            display: none;
            position: fixed;
            bottom: 24px;
            right: 24px;
            background: linear-gradient(145deg, #f97316, #facc15);
            border: none;
            width: 54px;
            height: 54px;
            border-radius: 30px;
            font-size: 24px;
            color: #0f172a;
            cursor: pointer;
            z-index: 1100;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .menu-toggle:hover { transform: scale(1.05); }

        /* Animation */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 20px; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .top-bar { flex-direction: column; align-items: flex-start; }
            th, td { padding: 14px 12px; }
        }

        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #eef2ff; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #f97316; border-radius: 10px; }
    </style>
</head>
<body>

<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-tags"></i>
</button>

<!-- Sidebar (consistent with dashboard) -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-car-side"></i>
            <span>AUTOPARTS PRO</span>
        </div>
        <div style="font-size: 12px; margin-top: 10px; opacity:0.7;">inventory intelligence</div>
    </div>
    <div class="nav-menu">
        <a href="../dashboard.php" class="nav-item"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
        <a href="view_category.php" class="nav-item active"><i class="fas fa-tags"></i><span>Categories</span></a>
        <a href="../spare_parts/view_part.php" class="nav-item"><i class="fas fa-cogs"></i><span>Spare Parts</span></a>
        <a href="../suppliers/view_supplier.php" class="nav-item"><i class="fas fa-truck"></i><span>Suppliers</span></a>
        <div class="nav-divider"></div>
        <a href="../stock/stock_in.php" class="nav-item"><i class="fas fa-arrow-down"></i><span>Stock In</span></a>
        <a href="../stock/stock_out.php" class="nav-item"><i class="fas fa-arrow-up"></i><span>Stock Out</span></a>
        <a href="../stock/stock_history.php" class="nav-item"><i class="fas fa-history"></i><span>Stock History</span></a>
        <div class="nav-divider"></div>
        <a href="../sales/view_sales.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span>Sales</span></a>
        <a href="../sales/create_sale.php" class="nav-item"><i class="fas fa-plus-circle"></i><span>Create Sale</span></a>
        <a href="../sales/export_excel.php" class="nav-item"><i class="fas fa-file-excel"></i><span>Excel Report</span></a>
        <div class="nav-divider"></div>
        <a href="../logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <div class="welcome-section">
            <h1>Product Categories 🏷️</h1>
            <p><i class="fas fa-layer-group" style="color:#f97316;"></i> Manage and organize your spare parts taxonomy</p>
        </div>
        <div class="weather-card">
            <i class="fas fa-cloud-sun fa-2x"></i>
            <div class="weather-info">
                <div class="weather-temp">28°C</div>
                <div class="weather-desc">Corporate · Sunny</div>
            </div>
        </div>
    </div>

    <div class="action-buttons">
        <a href="add_category.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add New Category</a>
        <a href="../dashboard.php" class="btn btn-outline"><i class="fas fa-chart-line"></i> Dashboard Overview</a>
    </div>

    <?php
    $totalCategories = mysqli_num_rows($data);
    
    // Check if status column exists before querying
    $statusExists = false;
    $activeCount = 0;
    $inactiveCount = 0;
    
    // First, check if status column exists in categories table
    $checkColumn = mysqli_query($conn, "SHOW COLUMNS FROM categories LIKE 'status'");
    if(mysqli_num_rows($checkColumn) > 0) {
        $statusExists = true;
        $activeQuery = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM categories WHERE status = 'Active'");
        if($activeQuery && mysqli_num_rows($activeQuery) > 0) {
            $activeCount = mysqli_fetch_assoc($activeQuery)['cnt'];
        }
        $inactiveQuery = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM categories WHERE status = 'Inactive'");
        if($inactiveQuery && mysqli_num_rows($inactiveQuery) > 0) {
            $inactiveCount = mysqli_fetch_assoc($inactiveQuery)['cnt'];
        }
    }
    
    // Reset data pointer for main loop
    mysqli_data_seek($data, 0);
    ?>

    <div class="stats-summary">
        <div class="stat-chip"><i class="fas fa-tags"></i><span>Total Categories</span><span class="number"><?php echo $totalCategories; ?></span></div>
        <?php if($statusExists) { ?>
        <div class="stat-chip"><i class="fas fa-check-circle" style="color:#10b981;"></i><span>Active</span><span class="number" style="color:#10b981;"><?php echo $activeCount; ?></span></div>
        <div class="stat-chip"><i class="fas fa-pause-circle" style="color:#f97316;"></i><span>Inactive</span><span class="number" style="color:#f97316;"><?php echo $inactiveCount; ?></span></div>
        <?php } ?>
    </div>

    <div class="data-table-wrapper">
        <?php if(mysqli_num_rows($data) > 0) { ?>
        <table>
            <thead>
                <tr>
                    <th><i class="fas fa-hashtag"></i> ID</th>
                    <th><i class="fas fa-tag"></i> Category Name</th>
                    <th><i class="fas fa-barcode"></i> Category Code</th>
                    <th><i class="fas fa-align-left"></i> Description</th>
                    <?php if($statusExists) { ?>
                    <th><i class="fas fa-toggle-on"></i> Status</th>
                    <?php } ?>
                    <th><i class="fas fa-cog"></i> Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($data)) { ?>
                <tr>
                    <td style="font-weight: 700; color: #f97316;">#<?php echo $row['id']; ?></td>
                    <td style="font-weight: 600;"><?php echo htmlspecialchars($row['category_name']); ?></td>
                    <td><code style="background:#f1f5f9; padding:4px 10px; border-radius:20px;"><?php echo htmlspecialchars($row['category_code']); ?></code></td>
                    <td style="max-width: 280px;"><?php echo htmlspecialchars($row['description'] ?: '—'); ?></td>
                    <?php if($statusExists) { ?>
                    <td>
                        <?php if(isset($row['status']) && $row['status'] == 'Active') { ?>
                            <span class="status-badge status-active"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Active</span>
                        <?php } else { ?>
                            <span class="status-badge status-inactive"><i class="fas fa-circle" style="font-size: 0.5rem;"></i> Inactive</span>
                        <?php } ?>
                    </td>
                    <?php } ?>
                    <td class="action-icons">
                        <a href="edit_category.php?id=<?php echo $row['id']; ?>" title="Edit Category"><i class="fas fa-edit"></i></a>
                        <a href="delete_category.php?id=<?php echo $row['id']; ?>" title="Delete Category" onclick="return confirm('Are you sure you want to delete this category? Related parts may be affected.');"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php } else { ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h3>No Categories Yet</h3>
                <p>Get started by adding your first product category.</p>
                <a href="add_category.php" class="btn btn-primary" style="margin-top: 20px; display: inline-flex;"><i class="fas fa-plus"></i> Create Category</a>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    // Mobile toggle functionality
    const toggleBtn = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    // Auto-close sidebar when clicking a link on mobile
    const navLinks = document.querySelectorAll('.nav-item');
    if(window.innerWidth <= 768) {
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                sidebar.classList.remove('active');
            });
        });
    }
</script>

</body>
</html>