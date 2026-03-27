<?php
session_start();

if(!isset($_SESSION['user']))
{
    header("Location: ../login.php");
    exit();
}

include("../config/db.php");

// Fetch real sales data for the chart
$sales_data = mysqli_query($conn, "
    SELECT 
        DATE_FORMAT(date, '%b') as month,
        MONTH(date) as month_num,
        SUM(total) as total_sales,
        COUNT(*) as transaction_count
    FROM sales 
    WHERE date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY YEAR(date), MONTH(date), DATE_FORMAT(date, '%b')
    ORDER BY MONTH(date) ASC
    LIMIT 6
");

$months = [];
$sales = [];
$transactions = [];

while($row = mysqli_fetch_assoc($sales_data)) {
    $months[] = $row['month'];
    $sales[] = $row['total_sales'];
    $transactions[] = $row['transaction_count'];
}

// If no data, provide sample data for demonstration
if (empty($months)) {
    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
    $sales = [12500, 15000, 18500, 22000, 19500, 25000];
    $transactions = [45, 52, 68, 75, 70, 92];
}

// Calculate totals
$total_revenue = array_sum($sales);
$total_transactions = array_sum($transactions);
$avg_transaction = $total_transactions > 0 ? round($total_revenue / $total_transactions, 2) : 0;
$growth_percentage = count($sales) > 1 ? round((($sales[count($sales)-1] - $sales[0]) / $sales[0]) * 100, 1) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Analytics | NOVA Dashboard</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(145deg, #0a0f1e 0%, #141c30 100%);
            min-height: 100vh;
            padding: 2rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background grid */
        body::before {
            content: "";
            position: fixed;
            width: 200%;
            height: 200%;
            background-image: 
                radial-gradient(circle at 30% 40%, rgba(60, 106, 255, 0.03) 0%, transparent 30%),
                linear-gradient(rgba(40, 80, 200, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(40, 80, 200, 0.02) 1px, transparent 1px);
            background-size: 60px 60px;
            transform: rotate(-3deg) translate(-10%, -10%);
            animation: gridShift 60s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes gridShift {
            0% { transform: rotate(-3deg) translate(-10%, -10%); }
            100% { transform: rotate(-3deg) translate(-30%, -30%); }
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(0, 120, 255, 0.06), transparent 70%);
            filter: blur(70px);
            animation: float 25s infinite alternate ease-in-out;
            z-index: 0;
        }

        .orb-1 { top: -150px; right: -100px; background: radial-gradient(circle, rgba(0, 160, 255, 0.05), transparent); }
        .orb-2 { bottom: -150px; left: -80px; width: 500px; height: 500px; background: radial-gradient(circle, rgba(140, 0, 255, 0.04), transparent); animation-delay: -8s; }
        .orb-3 { top: 40%; left: 20%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(255, 200, 0, 0.03), transparent); animation-duration: 30s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.3); }
        }

        /* Main container */
        .dashboard {
            position: relative;
            z-index: 10;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding: 1.2rem 2rem;
            background: rgba(16, 24, 42, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(60, 106, 255, 0.2);
            border-radius: 60px;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.7);
            animation: slideDown 0.6s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1e3c8a, #2a4fcf);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 12px 24px -8px #1e3c8a;
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 8px 20px -6px #2a4fcf; }
            50% { box-shadow: 0 16px 30px -4px #4c7aff; }
            100% { box-shadow: 0 8px 20px -6px #2a4fcf; }
        }

        .logo-icon i {
            font-size: 30px;
            color: white;
        }

        .logo-text h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(to right, #ffffff, #b0c8ff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }

        .logo-text span {
            color: #7b94c0;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .date-range {
            background: rgba(60, 106, 255, 0.15);
            color: #b0c8ff;
            padding: 0.6rem 1.5rem;
            border-radius: 40px;
            border: 1px solid #3c6aff40;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* KPI Cards */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .kpi-card {
            background: rgba(18, 26, 45, 0.7);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(60, 106, 255, 0.2);
            border-radius: 32px;
            padding: 1.5rem;
            box-shadow: 0 15px 30px -12px rgba(0, 0, 0, 0.6);
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.3, 1.1);
            animation: cardFade 0.5s ease backwards;
        }

        .kpi-card:nth-child(1) { animation-delay: 0.1s; }
        .kpi-card:nth-child(2) { animation-delay: 0.15s; }
        .kpi-card:nth-child(3) { animation-delay: 0.2s; }
        .kpi-card:nth-child(4) { animation-delay: 0.25s; }

        @keyframes cardFade {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .kpi-card:hover {
            transform: translateY(-5px);
            border-color: #3c6eff80;
            box-shadow: 0 20px 35px -14px #1e3c8a;
        }

        .kpi-icon {
            width: 48px;
            height: 48px;
            background: rgba(60, 106, 255, 0.15);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .kpi-icon i {
            font-size: 24px;
            color: #6d9eff;
        }

        .kpi-label {
            color: #9aafe6;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.3rem;
        }

        .kpi-value {
            color: white;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .kpi-trend {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.85rem;
            margin-top: 0.5rem;
        }

        .trend-up {
            color: #2ecc71;
        }

        .trend-down {
            color: #e74c3c;
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        /* Chart Card */
        .chart-card {
            background: rgba(18, 26, 45, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(60, 106, 255, 0.2);
            border-radius: 40px;
            padding: 1.8rem;
            box-shadow: 0 30px 50px -20px black;
            animation: chartAppear 0.7s ease;
        }

        @keyframes chartAppear {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .chart-header h3 {
            color: white;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-header h3 i {
            color: #3c6eff;
        }

        .chart-actions {
            display: flex;
            gap: 10px;
        }

        .chart-btn {
            background: rgba(30, 45, 80, 0.7);
            color: #b0c8ff;
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
            border: 1px solid #3c6aff60;
            cursor: pointer;
            transition: all 0.3s;
        }

        .chart-btn:hover {
            background: #2a4fcf;
            color: white;
        }

        .chart-btn.active {
            background: #2a4fcf;
            color: white;
        }

        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .stat-item {
            background: rgba(8, 14, 26, 0.5);
            border-radius: 24px;
            padding: 1rem;
            text-align: center;
        }

        .stat-label {
            color: #7b94c0;
            font-size: 0.8rem;
            margin-bottom: 0.3rem;
        }

        .stat-value {
            color: white;
            font-size: 1.4rem;
            font-weight: 700;
        }

        /* Recent Sales Card */
        .recent-card {
            background: rgba(18, 26, 45, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(60, 106, 255, 0.2);
            border-radius: 40px;
            padding: 1.8rem;
            box-shadow: 0 30px 50px -20px black;
        }

        .recent-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .recent-header h3 {
            color: white;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .view-link {
            color: #b0c8ff;
            text-decoration: none;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .recent-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .recent-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.8rem 1rem;
            background: rgba(8, 14, 26, 0.5);
            border-radius: 20px;
            border: 1px solid rgba(60, 106, 255, 0.1);
            transition: all 0.3s;
        }

        .recent-item:hover {
            border-color: #3c6eff;
            transform: translateX(5px);
        }

        .recent-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .recent-icon {
            width: 40px;
            height: 40px;
            background: rgba(60, 106, 255, 0.15);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6d9eff;
        }

        .recent-details h4 {
            color: white;
            font-size: 1rem;
            margin-bottom: 0.2rem;
        }

        .recent-details p {
            color: #7b94c0;
            font-size: 0.85rem;
        }

        .recent-amount {
            color: #2ecc71;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Footer */
        .footer {
            margin-top: 2rem;
            text-align: center;
            color: #5b74a0;
            font-size: 0.85rem;
            display: flex;
            justify-content: center;
            gap: 2rem;
            padding: 1rem;
            background: rgba(16, 24, 42, 0.5);
            border-radius: 60px;
            backdrop-filter: blur(10px);
        }

        /* Responsive */
        @media (max-width: 900px) {
            .charts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            body { padding: 1rem; }
            .header { flex-direction: column; gap: 1rem; }
            .kpi-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <!-- Background orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="dashboard">
        <!-- Header -->
        <div class="header">
            <div class="logo-area">
                <div class="logo-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="logo-text">
                    <h1>SALES·ANALYTICS</h1>
                    <span><i class="fas fa-circle" style="font-size: 8px; color: #2ecc71;"></i> REAL-TIME PERFORMANCE DASHBOARD</span>
                </div>
            </div>
            <div class="date-range">
                <i class="far fa-calendar-alt"></i>
                Last 6 Months
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="kpi-grid">
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-dollar-sign"></i></div>
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-value">$<?php echo number_format($total_revenue, 2); ?></div>
                <div class="kpi-trend <?php echo $growth_percentage >= 0 ? 'trend-up' : 'trend-down'; ?>">
                    <i class="fas fa-arrow-<?php echo $growth_percentage >= 0 ? 'up' : 'down'; ?>"></i>
                    <?php echo abs($growth_percentage); ?>% vs last period
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-shopping-cart"></i></div>
                <div class="kpi-label">Transactions</div>
                <div class="kpi-value"><?php echo $total_transactions; ?></div>
                <div class="kpi-trend trend-up">
                    <i class="fas fa-arrow-up"></i>
                    +12% conversion
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-receipt"></i></div>
                <div class="kpi-label">Avg. Order Value</div>
                <div class="kpi-value">$<?php echo number_format($avg_transaction, 2); ?></div>
                <div class="kpi-trend trend-up">
                    <i class="fas fa-chart-line"></i>
                    Per transaction
                </div>
            </div>
            <div class="kpi-card">
                <div class="kpi-icon"><i class="fas fa-star"></i></div>
                <div class="kpi-label">Best Month</div>
                <div class="kpi-value"><?php echo $months[array_search(max($sales), $sales)] ?? 'N/A'; ?></div>
                <div class="kpi-trend trend-up">
                    <i class="fas fa-trophy"></i>
                    Peak performance
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <!-- Main Sales Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>
                        <i class="fas fa-chart-bar"></i>
                        Sales Overview
                    </h3>
                    <div class="chart-actions">
                        <button class="chart-btn active" onclick="changeChartType('bar')">
                            <i class="fas fa-chart-bar"></i>
                        </button>
                        <button class="chart-btn" onclick="changeChartType('line')">
                            <i class="fas fa-chart-line"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="salesChart"></canvas>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Highest Sales</div>
                        <div class="stat-value">$<?php echo number_format(max($sales), 0); ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Average</div>
                        <div class="stat-value">$<?php echo number_format(array_sum($sales) / count($sales), 0); ?></div>
                    </div>
                </div>
            </div>

            <!-- Transaction Chart -->
            <div class="chart-card">
                <div class="chart-header">
                    <h3>
                        <i class="fas fa-chart-pie"></i>
                        Transaction Volume
                    </h3>
                </div>
                <div class="chart-container">
                    <canvas id="transactionChart"></canvas>
                </div>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-label">Total Orders</div>
                        <div class="stat-value"><?php echo $total_transactions; ?></div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-label">Monthly Avg</div>
                        <div class="stat-value"><?php echo round($total_transactions / count($transactions)); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Sales Card -->
        <div class="recent-card">
            <div class="recent-header">
                <h3>
                    <i class="fas fa-history"></i>
                    Recent Transactions
                </h3>
                <a href="sales_list.php" class="view-link">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="recent-list">
                <?php
                $recent = mysqli_query($conn, "
                    SELECT sales.*, spare_parts.part_name 
                    FROM sales 
                    JOIN spare_parts ON sales.part_id = spare_parts.id 
                    ORDER BY sales.date DESC 
                    LIMIT 5
                ");
                
                while($row = mysqli_fetch_assoc($recent)):
                ?>
                <div class="recent-item">
                    <div class="recent-info">
                        <div class="recent-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="recent-details">
                            <h4><?php echo htmlspecialchars($row['part_name']); ?></h4>
                            <p><i class="far fa-clock"></i> <?php echo date('M d, H:i', strtotime($row['date'])); ?></p>
                        </div>
                    </div>
                    <div class="recent-amount">
                        $<?php echo number_format($row['total'], 2); ?>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <span><i class="fas fa-sync-alt"></i> Real-time Data</span>
            <span><i class="fas fa-shield-alt"></i> Secure Analytics</span>
            <span><i class="fas fa-clock"></i> Last updated: <?php echo date('H:i:s'); ?></span>
        </div>
    </div>

    <!-- Chart Initialization Script -->
    <script>
        let salesChart, transactionChart;
        
        // Data from PHP
        const months = <?php echo json_encode($months); ?>;
        const salesData = <?php echo json_encode($sales); ?>;
        const transactionData = <?php echo json_encode($transactions); ?>;

        // Common chart options
        const chartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    labels: {
                        color: '#b0c8ff',
                        font: { family: 'Inter' }
                    }
                },
                tooltip: {
                    backgroundColor: '#1a253f',
                    titleColor: '#ffffff',
                    bodyColor: '#b0c8ff',
                    borderColor: '#3c6eff',
                    borderWidth: 1
                }
            },
            scales: {
                y: {
                    grid: { color: 'rgba(60, 106, 255, 0.1)' },
                    ticks: { color: '#7b94c0' }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#7b94c0' }
                }
            }
        };

        // Initialize Sales Chart
        function initSalesChart(type = 'bar') {
            const ctx = document.getElementById('salesChart').getContext('2d');
            
            if (salesChart) {
                salesChart.destroy();
            }
            
            salesChart = new Chart(ctx, {
                type: type,
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Sales ($)',
                        data: salesData,
                        backgroundColor: 'rgba(60, 106, 255, 0.8)',
                        borderColor: '#4c7aff',
                        borderWidth: 2,
                        borderRadius: 8,
                        tension: 0.4
                    }]
                },
                options: {
                    ...chartOptions,
                    plugins: {
                        ...chartOptions.plugins,
                        tooltip: {
                            ...chartOptions.plugins.tooltip,
                            callbacks: {
                                label: function(context) {
                                    return 'Revenue: $' + context.raw.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        }

        // Initialize Transaction Chart
        function initTransactionChart() {
            const ctx = document.getElementById('transactionChart').getContext('2d');
            
            transactionChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: months,
                    datasets: [{
                        data: transactionData,
                        backgroundColor: [
                            'rgba(60, 106, 255, 0.8)',
                            'rgba(46, 204, 113, 0.8)',
                            'rgba(155, 89, 182, 0.8)',
                            'rgba(241, 196, 15, 0.8)',
                            'rgba(230, 126, 34, 0.8)',
                            'rgba(231, 76, 60, 0.8)'
                        ],
                        borderColor: '#1a253f',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#b0c8ff',
                                font: { family: 'Inter' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1a253f',
                            titleColor: '#ffffff',
                            bodyColor: '#b0c8ff',
                            borderColor: '#3c6eff',
                            borderWidth: 1
                        }
                    }
                }
            });
        }

        // Change chart type
        function changeChartType(type) {
            initSalesChart(type);
            
            // Update active button
            document.querySelectorAll('.chart-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.closest('.chart-btn').classList.add('active');
        }

        // Initialize charts on load
        document.addEventListener('DOMContentLoaded', function() {
            initSalesChart('bar');
            initTransactionChart();
        });

        // Animation for cards
        document.querySelectorAll('.kpi-card, .chart-card, .recent-card').forEach((card, index) => {
            card.style.animation = `cardFade 0.5s ease ${index * 0.1}s backwards`;
        });
    </script>
</body>
</html>