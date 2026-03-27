<?php
session_start();
include("config/db.php");

if(!isset($_SESSION['user'])){
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

/* COUNTS */
$parts = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM spare_parts"))['total'];
$categories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM categories"))['total'];
$suppliers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM suppliers"))['total'];
$lowStock = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM spare_parts WHERE quantity<5"))['total'];

/* RECENT SALES */
$recentSales = mysqli_query($conn, "SELECT * FROM sales ORDER BY date DESC LIMIT 5");

/* LOW STOCK ITEMS */
$lowStockItems = mysqli_query($conn, "SELECT part_name, quantity FROM spare_parts WHERE quantity<5 ORDER BY quantity ASC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AutoParts Pro | Corporate Inventory Intelligence</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <!-- Google Fonts for modern corporate typography -->
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

        /* ---------- MODERN SIDEBAR (CORPORATE ELEGANCE) ---------- */
        .sidebar {
            width: 280px;
            height: 100vh;
            background: #0f172a;
            backdrop-filter: blur(0px);
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
            position: relative;
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

        /* Main content */
        .main-content {
            margin-left: 280px;
            padding: 32px 40px;
            min-height: 100vh;
            transition: all 0.3s;
        }

        /* Top bar with animation */
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

        /* Stats Grid (animated cards) */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: white;
            border-radius: 28px;
            padding: 22px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.03), 0 0 0 1px rgba(0,0,0,0.02);
            border: 1px solid rgba(226,232,240,0.8);
        }
        .stat-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 30px -12px rgba(0, 0, 0, 0.12);
            border-color: #facc1510;
        }

        .stat-left h3 {
            font-size: 2.4rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
            line-height: 1.2;
        }
        .stat-left p { color: #5b6e8c; font-weight: 500; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px;}

        .stat-icon {
            width: 54px;
            height: 54px;
            background: linear-gradient(145deg, #fef3c7, #ffedd5);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #f97316;
            font-size: 1.8rem;
            transition: 0.2s;
        }

        /* Charts row */
        .charts-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 28px;
            margin-bottom: 40px;
        }

        .chart-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 20px 20px 16px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.02), 0 0 0 1px #eef2ff;
            transition: all 0.25s;
        }
        .chart-card:hover { box-shadow: 0 20px 30px -12px rgba(0,0,0,0.08); transform: scale(1.01); }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            padding-bottom: 8px;
            border-bottom: 2px solid #f1f5f9;
        }
        .chart-header h3 { font-weight: 700; font-size: 1.1rem; color: #1e293b; letter-spacing: -0.2px; }

        canvas { max-height: 250px; width: 100% !important; }

        /* Tables row */
        .tables-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 28px;
            margin-bottom: 20px;
        }

        .data-table {
            background: #ffffff;
            border-radius: 28px;
            padding: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.02), 0 0 0 1px #eef2ff;
            transition: all 0.2s;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f1f5f9;
        }
        .table-header h3 { font-weight: 700; font-size: 1rem; color: #1e293b; }
        .table-header a { color: #f97316; font-weight: 600; font-size: 0.8rem; text-decoration: none; transition: 0.2s; }
        .table-header a:hover { color: #ea580c; text-decoration: underline; }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px 8px; text-align: left; font-size: 0.85rem; border-bottom: 1px solid #f0f4fa; }
        th { color: #5b6e8c; font-weight: 600; }
        td { color: #1f2a44; font-weight: 500; }

        .alert-badge {
            background: #fff1f0;
            color: #f97316;
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.75rem;
            font-weight: 700;
            display: inline-block;
        }
        .stock-warning { background: #fffaf5; border-left: 3px solid #f97316; transition: 0.1s; }

        /* Action Buttons (corporate) */
        .action-buttons {
            display: flex;
            gap: 18px;
            margin-bottom: 40px;
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

        /* mobile toggle */
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
            transition: 0.2s;
        }
        .menu-toggle:hover { transform: scale(1.05); }

        /* animations */
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-card, .chart-card, .data-table, .action-buttons .btn {
            animation: fadeSlideUp 0.45s ease-out backwards;
        }
        .stat-card:nth-child(1) { animation-delay: 0.05s; }
        .stat-card:nth-child(2) { animation-delay: 0.1s; }
        .stat-card:nth-child(3) { animation-delay: 0.15s; }
        .stat-card:nth-child(4) { animation-delay: 0.2s; }
        .chart-card:first-child { animation-delay: 0.2s; }
        .chart-card:last-child { animation-delay: 0.25s; }
        .data-table:first-child { animation-delay: 0.3s; }
        .data-table:last-child { animation-delay: 0.35s; }

        /* Responsive */
        @media (max-width: 1100px) {
            .stats-grid { grid-template-columns: repeat(2,1fr); gap: 20px;}
            .charts-row, .tables-row { grid-template-columns: 1fr; }
            .main-content { padding: 24px 24px; }
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; padding: 20px; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .stats-grid { grid-template-columns: 1fr; }
        }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #eef2ff; border-radius: 10px; }
        ::-webkit-scrollbar-thumb { background: #f97316; border-radius: 10px; }
    </style>
</head>
<body>

<button class="menu-toggle" id="menuToggle">
    <i class="fas fa-chart-simple"></i>
</button>

<!-- Sidebar refined -->
<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="logo">
            <i class="fas fa-car-side"></i>
            <span>AUTOPARTS PRO</span>
        </div>
        <div style="font-size: 12px; margin-top: 10px; opacity:0.7;">inventory intelligence</div>
    </div>
    <div class="nav-menu">
        <a href="dashboard.php" class="nav-item active"><i class="fas fa-chart-line"></i><span>Dashboard</span></a>
        <a href="categories/view_category.php" class="nav-item"><i class="fas fa-tags"></i><span>Categories</span></a>
        <a href="spare_parts/view_part.php" class="nav-item"><i class="fas fa-cogs"></i><span>Spare Parts</span></a>
        <a href="suppliers/view_supplier.php" class="nav-item"><i class="fas fa-truck"></i><span>Suppliers</span></a>
        <div class="nav-divider"></div>
        <a href="stock/stock_in.php" class="nav-item"><i class="fas fa-arrow-down"></i><span>Stock In</span></a>
        <a href="stock/stock_out.php" class="nav-item"><i class="fas fa-arrow-up"></i><span>Stock Out</span></a>
        <a href="stock/stock_history.php" class="nav-item"><i class="fas fa-history"></i><span>Stock History</span></a>
        <div class="nav-divider"></div>
        <a href="sales/view_sales.php" class="nav-item"><i class="fas fa-shopping-cart"></i><span>View Sales</span></a>
        <a href="sales/create_sale.php" class="nav-item"><i class="fas fa-plus-circle"></i><span>Create Sale</span></a>
        <a href="sales/export_excel.php" class="nav-item"><i class="fas fa-file-excel"></i><span>Excel Report</span></a>
        <div class="nav-divider"></div>
        <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a>
    </div>
</div>

<div class="main-content">
    <div class="top-bar">
        <div class="welcome-section">
            <h1>Welcome back, <?php echo htmlspecialchars($user); ?>! 👋</h1>
            <p><i class="fas fa-chart-pie" style="color:#f97316;"></i> Real-time inventory performance & actionable insights</p>
        </div>
        <div class="weather-card">
            <i class="fas fa-cloud-sun fa-2x" style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));"></i>
            <div class="weather-info">
                <div class="weather-temp">28°C</div>
                <div class="weather-desc">Corporate · Sunny</div>
            </div>
        </div>
    </div>

    <div class="action-buttons">
        <a href="sales/create_sale.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> New Sale</a>
        <a href="stock/stock_in.php" class="btn btn-outline"><i class="fas fa-arrow-down"></i> Stock In</a>
        <a href="stock/stock_out.php" class="btn btn-outline"><i class="fas fa-arrow-up"></i> Stock Out</a>
        <a href="sales/export_excel.php" class="btn btn-outline"><i class="fas fa-file-excel"></i> Export Report</a>
    </div>

    <div class="stats-grid">
        <div class="stat-card"><div class="stat-left"><h3><?php echo $parts; ?></h3><p>Total Parts</p></div><div class="stat-icon"><i class="fas fa-cog"></i></div></div>
        <div class="stat-card"><div class="stat-left"><h3><?php echo $categories; ?></h3><p>Categories</p></div><div class="stat-icon"><i class="fas fa-layer-group"></i></div></div>
        <div class="stat-card"><div class="stat-left"><h3><?php echo $suppliers; ?></h3><p>Suppliers</p></div><div class="stat-icon"><i class="fas fa-truck-moving"></i></div></div>
        <div class="stat-card"><div class="stat-left"><h3 style="color: <?php echo $lowStock > 0 ? '#f97316' : '#10b981'; ?>;"><?php echo $lowStock; ?></h3><p>Low Stock Alerts</p></div><div class="stat-icon" style="background: <?php echo $lowStock > 0 ? '#fff1f0' : '#e0f2e9'; ?>; color:<?php echo $lowStock > 0 ? '#f97316' : '#10b981'; ?>;"><i class="fas fa-exclamation-triangle"></i></div></div>
    </div>

    <!-- CHARTS SECTION -->
    <div class="charts-row">
        <div class="chart-card"><div class="chart-header"><h3><i class="fas fa-chart-simple"></i> Stock Flow (In vs Out)</h3><i class="fas fa-chart-line" style="color:#f97316;"></i></div><canvas id="stockChart"></canvas></div>
        <div class="chart-card"><div class="chart-header"><h3><i class="fas fa-chart-line"></i> Revenue Trend (Monthly)</h3><i class="fas fa-dollar-sign" style="color:#facc15;"></i></div><canvas id="salesChart"></canvas></div>
    </div>

    <div class="tables-row">
        <div class="data-table">
            <div class="table-header"><h3><i class="fas fa-receipt"></i> Recent Sales Transactions</h3><a href="sales/view_sales.php">View all →</a></div>
            <table>
                <thead>
                    <tr><th>Invoice #</th><th>Date</th><th>Amount (USD)</th></tr>
                </thead>
                <tbody>
                    <?php
                    $recentSales = mysqli_query($conn, "SELECT * FROM sales ORDER BY date DESC LIMIT 5");
                    if(mysqli_num_rows($recentSales) > 0) {
                        while($sale = mysqli_fetch_assoc($recentSales)) {
                            echo "<tr>";
                            echo "<td style='font-weight:600;'>INV-" . str_pad($sale['id'], 5, '0', STR_PAD_LEFT) . "</td>";
                            echo "<td>" . date('d M Y', strtotime($sale['date'])) . "</td>";
                            echo "<td style='color:#f97316; font-weight:700;'>$" . number_format($sale['total'], 2) . "</td>";
                            echo "</tr>";
                        }
                    } else { 
                        echo "<tr><td colspan='3' style='text-align:center; padding:28px;'>✨ No sales recorded yet</td></tr>"; 
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div class="data-table">
            <div class="table-header"><h3><i class="fas fa-box-open"></i> Critical Stock (Reorder Zone)</h3><a href="spare_parts/view_part.php">Manage stock →</a></div>
            <table>
                <thead>
                    <tr><th>Part Name</th><th>Remaining Qty</th><th>Priority</th></tr>
                </thead>
                <tbody>
                    <?php
                    $lowStockItems = mysqli_query($conn, "SELECT part_name, quantity FROM spare_parts WHERE quantity<5 ORDER BY quantity ASC LIMIT 5");
                    if(mysqli_num_rows($lowStockItems) > 0) {
                        while($item = mysqli_fetch_assoc($lowStockItems)) {
                            echo "<tr class='stock-warning'>";
                            echo "<td style='font-weight:500;'>".htmlspecialchars($item['part_name'])."</td>";
                            echo "<td><span class='alert-badge'><i class='fas fa-thermometer-half'></i> ".$item['quantity']." units</span></td>";
                            echo "<td style='color:#f97316;'><i class='fas fa-clock'></i> Reorder now</td>";
                            echo "</tr>";
                        }
                    } else { 
                        echo "<tr><td colspan='3' style='text-align:center; padding:28px;'><i class='fas fa-check-circle' style='color:#10b981;'></i> All stock levels are optimal</td></tr>"; 
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// MOBILE TOGGLE with sleek animation
const toggleBtn = document.getElementById('menuToggle');
const sidebar = document.getElementById('sidebar');
if(toggleBtn) {
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// ---------- CHART DATA FETCH ----------
<?php
// Build data for stock movement and sales by month
$monthsLabel = [];
$stockInVals = [];
$stockOutVals = [];
$stockQuery = mysqli_query($conn, "SELECT DATE_FORMAT(date,'%b') as month, MONTH(date) as mnum, SUM(quantity) as qty, type FROM stock GROUP BY MONTH(date), type ORDER BY mnum");
$tempIn = [];
$tempOut = [];
$tempMonths = [];

while($row = mysqli_fetch_assoc($stockQuery)) {
    $mon = $row['month'];
    if(!in_array($mon, $tempMonths)) $tempMonths[] = $mon;
    if($row['type'] == 'in') $tempIn[$mon] = $row['qty'];
    else $tempOut[$mon] = $row['qty'];
}
foreach($tempMonths as $m) {
    $monthsLabel[] = $m;
    $stockInVals[] = $tempIn[$m] ?? 0;
    $stockOutVals[] = $tempOut[$m] ?? 0;
}
if(empty($monthsLabel)) { $monthsLabel = ['Jan','Feb','Mar']; $stockInVals = [12,19,8]; $stockOutVals = [5,12,7]; }

$salesMonths = [];
$salesTotals = [];
$salesQ = mysqli_query($conn, "SELECT DATE_FORMAT(date,'%b') as month, MONTH(date) as mnum, SUM(total) as total FROM sales GROUP BY MONTH(date) ORDER BY mnum");
while($sRow = mysqli_fetch_assoc($salesQ)) {
    $salesMonths[] = $sRow['month'];
    $salesTotals[] = (float)$sRow['total'];
}
if(empty($salesMonths)) { $salesMonths = ['Jan','Feb','Mar']; $salesTotals = [1250, 3200, 4870]; }
?>

const stockLabels = <?php echo json_encode($monthsLabel); ?>;
const stockInData = <?php echo json_encode($stockInVals); ?>;
const stockOutData = <?php echo json_encode($stockOutVals); ?>;
const salesLabels = <?php echo json_encode($salesMonths); ?>;
const salesAmounts = <?php echo json_encode($salesTotals); ?>;

// Stock bar chart with corporate palette
const ctxStock = document.getElementById('stockChart').getContext('2d');
new Chart(ctxStock, {
    type: 'bar',
    data: {
        labels: stockLabels,
        datasets: [
            { label: 'Stock In', data: stockInData, backgroundColor: '#facc15', borderRadius: 12, barPercentage: 0.65, categoryPercentage: 0.8, borderSkipped: false },
            { label: 'Stock Out', data: stockOutData, backgroundColor: '#f97316', borderRadius: 12, barPercentage: 0.65, categoryPercentage: 0.8 }
        ]
    },
    options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { position: 'top', labels: { font: { weight: '600', size: 12 }, usePointStyle: true } }, tooltip: { backgroundColor: '#0f172a', titleColor: '#facc15' } },
        scales: { y: { beginAtZero: true, grid: { color: '#eef2ff' }, title: { display: true, text: 'Quantity', color: '#475569' } }, x: { grid: { display: false } } }
    }
});

// Sales line chart with gradient fill
const ctxSales = document.getElementById('salesChart').getContext('2d');
new Chart(ctxSales, {
    type: 'line',
    data: {
        labels: salesLabels,
        datasets: [{
            label: 'Total Sales ($)',
            data: salesAmounts,
            borderColor: '#f97316',
            backgroundColor: function(context) {
                const chart = context.chart;
                const {ctx, chartArea} = chart;
                if(!chartArea) return null;
                const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
                gradient.addColorStop(0, 'rgba(249,115,22,0.25)');
                gradient.addColorStop(1, 'rgba(250,204,21,0.02)');
                return gradient;
            },
            fill: true,
            tension: 0.4,
            pointRadius: 5,
            pointBackgroundColor: '#facc15',
            pointBorderColor: '#0f172a',
            pointBorderWidth: 2,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        plugins: { tooltip: { callbacks: { label: (ctx) => `$${ctx.raw.toFixed(2)}` } }, legend: { position: 'top' } },
        scales: { y: { beginAtZero: true, ticks: { callback: (val) => '$' + val }, grid: { color: '#f1f5f9' } } }
    }
});
</script>

</body>
</html>