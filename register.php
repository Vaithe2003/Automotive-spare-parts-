<?php
session_start();
include("config/db.php");

$message = "";
$message_type = "";

if(isset($_POST['register'])){

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validation
    if(empty($username) || empty($email) || empty($password)) {
        $message = "Please fill in all fields";
        $message_type = "error";
    } elseif($password !== $confirm_password) {
        $message = "Passwords do not match";
        $message_type = "error";
    } elseif(strlen($password) < 6) {
        $message = "Password must be at least 6 characters";
        $message_type = "error";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Check if username exists
        $check_query = "SELECT * FROM users WHERE username='$username' OR email='$email'";
        $check_result = mysqli_query($conn, $check_query);
        
        if(mysqli_num_rows($check_result) > 0) {
            $message = "Username or email already exists";
            $message_type = "error";
        } else {
            $query = "INSERT INTO users (username, email, password) VALUES('$username','$email','$hashed_password')";
            
            if(mysqli_query($conn, $query)){
                $message = "Registration Successful! Please login.";
                $message_type = "success";
                // Clear form
                $_POST = array();
            } else {
                $message = "Database error: " . mysqli_error($conn);
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AutoParts Pro | Register</title>
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

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(100px, 50px) scale(1.3); }
        }

        .register-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 500px;
            padding: 20px;
            animation: fadeSlideUp 0.6s ease-out;
        }

        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .logo-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #f97316, #facc15);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 20px 35px -12px rgba(249,115,22,0.4);
        }

        .logo-icon i {
            font-size: 36px;
            color: #0f172a;
        }

        .logo-text h1 {
            font-size: 1.6rem;
            font-weight: 800;
            background: linear-gradient(135deg, #ffffff, #facc15);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-top: 12px;
        }

        .register-card {
            background: rgba(30, 41, 59, 0.8);
            backdrop-filter: blur(12px);
            border-radius: 32px;
            padding: 36px;
            box-shadow: 0 25px 45px -12px rgba(0, 0, 0, 0.5);
            border: 1px solid rgba(249,115,22,0.2);
        }

        .card-header {
            text-align: center;
            margin-bottom: 28px;
        }

        .card-header h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
        }

        .card-header p {
            color: #94a3b8;
            font-size: 0.85rem;
        }

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

        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            color: #a7f3d0;
            border-left: 3px solid #10b981;
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            color: #fecaca;
            border-left: 3px solid #ef4444;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #cbd5e1;
            font-size: 0.8rem;
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
            font-size: 1rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px 12px 44px;
            background: rgba(15, 23, 42, 0.8);
            border: 1.5px solid rgba(249,115,22,0.2);
            border-radius: 28px;
            font-size: 0.9rem;
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

        .register-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(105deg, #f97316, #facc15);
            border: none;
            border-radius: 40px;
            color: #0f172a;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 8px;
        }

        .register-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px -8px rgba(249,115,22,0.4);
            background: linear-gradient(105deg, #facc15, #f97316);
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(249,115,22,0.2);
        }

        .login-link p {
            color: #94a3b8;
            font-size: 0.85rem;
        }

        .login-link a {
            color: #f97316;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .login-link a:hover {
            color: #facc15;
            gap: 10px;
        }

        .password-hint {
            font-size: 0.7rem;
            color: #64748b;
            margin-top: 6px;
            margin-left: 12px;
        }

        @media (max-width: 480px) {
            .register-card {
                padding: 28px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>

    <div class="register-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="logo-text">
                <h1>Create Account</h1>
            </div>
        </div>

        <div class="register-card">
            <div class="card-header">
                <h2>Get Started</h2>
                <p>Join AutoParts Pro today</p>
            </div>

            <?php if($message != ""): ?>
                <div class="alert alert-<?php echo $message_type; ?>">
                    <i class="fas fa-<?php echo $message_type == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label><i class="fas fa-user"></i> Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="username" class="form-control" placeholder="Choose a username" 
                               value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-wrapper">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="Enter your email" 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-lock"></i> Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-key"></i>
                        <input type="password" name="password" class="form-control" placeholder="Create a password" required>
                    </div>
                    <div class="password-hint">
                        <i class="fas fa-info-circle"></i> Minimum 6 characters
                    </div>
                </div>

                <div class="form-group">
                    <label><i class="fas fa-check-circle"></i> Confirm Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-check"></i>
                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm your password" required>
                    </div>
                </div>

                <button type="submit" name="register" class="register-btn">
                    <i class="fas fa-user-plus"></i>
                    Register Account
                </button>
            </form>

            <div class="login-link">
                <p>Already have an account?</p>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Sign In</a>
            </div>
        </div>
    </div>
</body>
</html>