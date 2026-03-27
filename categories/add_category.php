<?php
include("../config/db.php");

if(isset($_POST['save'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $code = mysqli_real_escape_string($conn, $_POST['code']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    mysqli_query($conn, "INSERT INTO categories (category_name, category_code, description, status) VALUES ('$name','$code','$desc','$status')");

    header("Location: view_category.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AutoParts Pro | Add Category</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts: Inter for corporate typography matching dashboard -->
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

        /* Top bar with welcome (matching dashboard) */
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

        /* Form Card - elegant corporate styling */
        .form-container {
            max-width: 680px;
            background: #ffffff;
            border-radius: 32px;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0,0,0,0.02);
            padding: 32px 36px;
            transition: transform 0.2s;
            margin-top: 12px;
            border: 1px solid #eef2ff;
        }

        .form-header {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 28px;
            padding-bottom: 16px;
            border-bottom: 2px solid #f1f5f9;
        }
        .form-header i {
            font-size: 2rem;
            background: linear-gradient(135deg, #f97316, #facc15);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .form-header h2 {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: -0.3px;
            background: linear-gradient(135deg, #0f172a, #334155);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .input-group {
            margin-bottom: 24px;
        }
        .input-group label {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            font-size: 0.85rem;
            color: #334155;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
            text-transform: uppercase;
        }
        .input-group label i {
            color: #f97316;
            font-size: 1rem;
            width: 20px;
        }
        input, textarea, select {
            width: 100%;
            padding: 14px 16px;
            font-family: 'Inter', system-ui;
            font-size: 0.95rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 20px;
            background: #ffffff;
            transition: all 0.2s ease;
            outline: none;
            color: #0f172a;
            font-weight: 500;
        }
        input:focus, textarea:focus, select:focus {
            border-color: #f97316;
            box-shadow: 0 0 0 4px rgba(249,115,22,0.15);
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        .btn-save {
            background: linear-gradient(105deg, #f97316, #facc15);
            color: #0f172a;
            padding: 14px 28px;
            border: none;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            box-shadow: 0 8px 18px rgba(249,115,22,0.2);
            margin-top: 12px;
        }
        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -8px rgba(249,115,22,0.4);
            background: linear-gradient(105deg, #facc15, #f97316);
        }
        .back-link {
            text-align: center;
            margin-top: 24px;
        }
        .back-link a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: 0.2s;
        }
        .back-link a:hover {
            color: #ea580c;
            gap: 12px;
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
        .form-container {
            animation: fadeSlideUp 0.45s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 20px; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .form-container { padding: 24px; }
            .top-bar { flex-direction: column; align-items: flex-start; }
        }
        @media (max-width: 480px) {
            .form-header h2 { font-size: 1.4rem; }
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #eef2ff; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #f97316; border-radius: 10px; }
    </style>
</head>
<body>

<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-plus-circle"></i>
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
            <h1>Add New Category 📦</h1>
            <p><i class="fas fa-layer-group" style="color:#f97316;"></i> Organize your spare parts with structured categories</p>
        </div>
        <div class="weather-card">
            <i class="fas fa-cloud-sun fa-2x" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"></i>
            <div class="weather-info">
                <div class="weather-temp">28°C</div>
                <div class="weather-desc">Corporate · Sunny</div>
            </div>
        </div>
    </div>

    <div class="form-container">
        <div class="form-header">
            <i class="fas fa-tag"></i>
            <h2>Category Details</h2>
        </div>

        <form method="post">
            <div class="input-group">
                <label><i class="fas fa-pen-alt"></i> Category Name</label>
                <input type="text" name="name" placeholder="e.g., Engine Components, Brake Systems" required>
            </div>

            <div class="input-group">
                <label><i class="fas fa-barcode"></i> Category Code</label>
                <input type="text" name="code" placeholder="Unique identifier (e.g., ENG-01, BRK-02)" required>
            </div>

            <div class="input-group">
                <label><i class="fas fa-align-left"></i> Description</label>
                <textarea name="description" placeholder="Brief description of this category..."></textarea>
            </div>

            <div class="input-group">
                <label><i class="fas fa-toggle-on"></i> Status</label>
                <select name="status">
                    <option value="Active">Active</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>

            <button type="submit" name="save" class="btn-save">
                <i class="fas fa-save"></i> Save Category
            </button>

            <div class="back-link">
                <a href="view_category.php"><i class="fas fa-arrow-left"></i> Back to Categories List</a>
            </div>
        </form>
    </div>
</div>

<script>
    // Mobile toggle functionality (same as dashboard)
    const toggleBtn = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if(toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    // Auto-close sidebar when clicking a link on mobile (optional)
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