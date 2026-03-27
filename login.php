<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include("config/db.php");

$error = "";

if(isset($_POST['login'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1){

        $row = mysqli_fetch_assoc($result);

        if(password_verify($password, $row['password'])){

            $_SESSION['user'] = $row['username'];
            header("Location: dashboard.php");
            exit();

        } else {
            $error = "Incorrect Password";
        }

    } else {
        $error = "User Not Found";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AutoParts Pro | Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background */
        body::before {
            content: "";
            position: fixed;
            width: 200%;
            height: 200%;
            background-image: 
                radial-gradient(circle at 30% 40%, rgba(249,115,22,0.08) 0%, transparent 50%),
                linear-gradient(rgba(249,115,22,0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(249,115,22,0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            transform: rotate(-5deg) translate(-20%, -20%);
            animation: gridShift 40s linear infinite;
            pointer-events: none;
            z-index: 0;
        }

        @keyframes gridShift {
            0% { transform: rotate(-5deg) translate(-20%, -20%); }
            100% { transform: rotate(-5deg) translate(-30%, -30%); }
        }

        /* Floating orbs */
        .orb {
            position: fixed;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle at 30% 30%, rgba(249,115,22,0.1), transparent 70%);
            filter: blur(80px);
            animation: float 20s infinite alternate ease-in-out;
            z-index: 0;
        }

        .orb-1 { top: -150px; right: -100px; background: radial-gradient(circle, rgba(250,204,21,0.1), transparent); }
        .orb-2 { bottom: -150px; left: -80px; width: 500px; height: 500px; background: radial-gradient(circle, rgba(249,115,22,0.08), transparent); animation-delay: -8s; }
        .orb-3 { top: 40%; left: 20%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(250,204,21,0.06), transparent); animation-duration: 30s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.3); }
        }

        /* Login Container */
        .login-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            padding: 20px;
            animation: fadeSlideUp 0.6s ease-out;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Logo Section */
        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f97316, #facc15);
            border-radius: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 35px -12px rgba(249,115,22,0.4);
            animation: pulse 3s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 8px 20px -6px rgba(249,115,22,0.4); }
            50% { box-shadow: 0 20px 35px -8px rgba(249,115,22,0.6); }
            100% { box-shadow: 0 8px 20px -6px rgba(249,115,22,0.4); }
        }

        .logo-icon i {
            font-size: 42px;
            color: #0f172a;
        }

        .logo-text {
            margin-top: 16px;
        }

        .logo-text h1 {
            font-size: 1.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #facc15);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: -0.5px;
        }

        .logo-text p {
            color: #94a3b8;
            font-size: 0.85rem;
            margin-top: 6px;
        }

        /* Login Card */
        .login-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            border-radius: 32px;
            padding: 40px 36px;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(249,115,22,0.2);
        }

        .card-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .card-header h2 {
            font-size: 1.8rem;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .card-header p {
            color: #94a3b8;
            font-size: 0.9rem;
        }

        /* Alert Messages */
        .alert {
            padding: 12px 16px;
            border-radius: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            color: #fecaca;
            border-left: 3px solid #ef4444;
        }

        .alert-error i {
            color: #ef4444;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 24px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-size: 0.85rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #f97316;
            font-size: 1.1rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px 14px 48px;
            background: rgba(15, 23, 42, 0.8);
            border: 1.5px solid rgba(249,115,22,0.2);
            border-radius: 28px;
            font-size: 0.95rem;
            color: white;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            border-color: #f97316;
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 4px rgba(249,115,22,0.1);
        }

        .form-control::placeholder {
            color: #64748b;
        }

        /* Login Button */
        .login-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(105deg, #f97316, #facc15);
            border: none;
            border-radius: 40px;
            color: #0f172a;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }

        .login-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -8px rgba(249,115,22,0.4);
            background: linear-gradient(105deg, #facc15, #f97316);
        }

        /* Register Link */
        .register-link {
            text-align: center;
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid rgba(249,115,22,0.2);
        }

        .register-link p {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .register-link a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .register-link a:hover {
            color: #facc15;
            gap: 10px;
        }

        /* Features Section */
        .features {
            display: flex;
            justify-content: center;
            gap: 24px;
            margin-top: 32px;
            flex-wrap: wrap;
        }

        .feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #94a3b8;
            font-size: 0.75rem;
        }

        .feature-item i {
            color: #f97316;
            font-size: 0.9rem;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-card {
                padding: 32px 24px;
            }
            .logo-icon {
                width: 60px;
                height: 60px;
            }
            .logo-icon i {
                font-size: 32px;
            }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-car-side"></i>
            </div>
            <div class="logo-text">
                <h1>AUTOPARTS PRO</h1>
                <p>Corporate Inventory Intelligence</p>
            </div>
        </div>

        <div class="login-card">
            <div class="card-header">
                <h2>Welcome Back</h2>
                <p>Sign in to access your dashboard</p>
            </div>

            <?php if($error != ""): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" class="form-control" placeholder="Enter your username" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                    </div>
                </div>

                <button type="submit" name="login" class="login-btn">
                    <i class="fas fa-arrow-right-to-bracket"></i>
                    Sign In
                </button>
            </form>

            <div class="register-link">
                <p>Don't have an account?</p>
                <a href="register.php"><i class="fas fa-user-plus"></i> Create Account</a>
            </div>
        </div>

        <div class="features">
            <div class="feature-item">
                <i class="fas fa-chart-line"></i>
                <span>Real-time Analytics</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-cogs"></i>
                <span>Inventory Management</span>
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                <span>Secure Access</span>
            </div>
        </div>
    </div>
</body>
</html>