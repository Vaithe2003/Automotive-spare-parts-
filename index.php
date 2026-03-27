<?php
session_start();

/* If user logged in go to dashboard */
if(isset($_SESSION['user']))
{
    $redirect = "dashboard.php";
    $message = "Welcome back! Redirecting to your dashboard...";
} else {
    $redirect = "login.php";
    $message = "Redirecting to login page...";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOVA Inventory | Enterprise System</title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(145deg, #0a0f1e 0%, #141c30 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated background grid */
        body::before {
            content: "";
            position: absolute;
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
            position: absolute;
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
        .splash-container {
            position: relative;
            z-index: 100;
            max-width: 600px;
            width: 90%;
            animation: containerAppear 1.2s cubic-bezier(0.23, 1, 0.32, 1);
        }

        @keyframes containerAppear {
            0% { opacity: 0; transform: scale(0.9) translateY(30px); }
            50% { opacity: 0.5; transform: scale(1.02) translateY(-5px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        /* Glass card */
        .splash-card {
            background: rgba(18, 26, 45, 0.7);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(60, 106, 255, 0.2);
            border-radius: 60px;
            padding: 3.5rem 3rem;
            box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.8), 
                        0 0 0 1px rgba(60, 106, 255, 0.1) inset;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Animated border gradient */
        .splash-card::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, 
                #3c6eff, 
                #2ecc71, 
                #e74c3c, 
                #f1c40f, 
                #9b59b6, 
                #3c6eff);
            background-size: 400% 400%;
            border-radius: 62px;
            z-index: -2;
            animation: gradientShift 8s ease infinite;
            opacity: 0.3;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .splash-card::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(18, 26, 45, 0.9);
            backdrop-filter: blur(16px);
            border-radius: 58px;
            z-index: -1;
        }

        /* Logo animation */
        .logo-wrapper {
            margin-bottom: 2.5rem;
            position: relative;
            display: inline-block;
        }

        .logo-icon {
            width: 120px;
            height: 120px;
            background: linear-gradient(135deg, #1e3c8a, #2a4fcf);
            border-radius: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 20px 40px -10px #1e3c8a;
            animation: logoPulse 3s infinite, logoFloat 5s ease-in-out infinite;
            position: relative;
        }

        @keyframes logoPulse {
            0% { box-shadow: 0 15px 30px -8px #2a4fcf; }
            50% { box-shadow: 0 25px 50px -5px #4c7aff; }
            100% { box-shadow: 0 15px 30px -8px #2a4fcf; }
        }

        @keyframes logoFloat {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .logo-icon i {
            font-size: 60px;
            color: white;
            animation: logoSpin 10s linear infinite;
        }

        @keyframes logoSpin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .logo-text {
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(to right, #ffffff, #b0c8ff, #6d9eff, #ffffff);
            background-size: 300% auto;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            animation: textGradient 4s linear infinite;
            letter-spacing: -1px;
        }

        @keyframes textGradient {
            0% { background-position: 0% center; }
            100% { background-position: 200% center; }
        }

        .logo-sub {
            color: #7b94c0;
            font-size: 1.1rem;
            margin-top: 0.5rem;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        /* Loading spinner */
        .spinner-container {
            margin: 3rem 0;
            position: relative;
        }

        .spinner {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            position: relative;
        }

        .spinner-circle {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid rgba(60, 106, 255, 0.1);
            border-top-color: #3c6eff;
            border-right-color: #6d9eff;
            border-bottom-color: #2ecc71;
            border-left-color: #e74c3c;
            animation: spinnerRotate 1.5s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
        }

        @keyframes spinnerRotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(720deg); }
        }

        .spinner-pulse {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 40px;
            height: 40px;
            background: rgba(60, 106, 255, 0.2);
            border-radius: 50%;
            animation: pulseRing 2s ease-out infinite;
        }

        @keyframes pulseRing {
            0% { transform: translate(-50%, -50%) scale(0.5); opacity: 0.5; }
            80% { transform: translate(-50%, -50%) scale(1.8); opacity: 0; }
            100% { transform: translate(-50%, -50%) scale(2); opacity: 0; }
        }

        /* Message */
        .message {
            color: #b0c8ff;
            font-size: 1.2rem;
            margin: 2rem 0;
            animation: messagePulse 2s ease-in-out infinite;
        }

        @keyframes messagePulse {
            0% { opacity: 0.7; transform: scale(1); }
            50% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 0.7; transform: scale(1); }
        }

        .message i {
            color: #3c6eff;
            margin-right: 10px;
        }

        /* Progress bar */
        .progress-container {
            width: 80%;
            margin: 2rem auto;
            height: 6px;
            background: rgba(8, 14, 26, 0.7);
            border-radius: 10px;
            overflow: hidden;
            position: relative;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, #3c6eff, #6d9eff, #2ecc71, #e74c3c, #f1c40f, #3c6eff);
            background-size: 300% 100%;
            width: 0%;
            border-radius: 10px;
            animation: progressFill 2.5s ease-out forwards, progressShimmer 2s linear infinite;
        }

        @keyframes progressFill {
            0% { width: 0%; }
            20% { width: 30%; }
            40% { width: 45%; }
            60% { width: 65%; }
            80% { width: 85%; }
            100% { width: 100%; }
        }

        @keyframes progressShimmer {
            0% { background-position: 0% 50%; }
            100% { background-position: 300% 50%; }
        }

        /* Company footer */
        .company-footer {
            margin-top: 3rem;
            color: #5b74a0;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .footer-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 0.5rem 1rem;
            background: rgba(8, 14, 26, 0.5);
            border-radius: 40px;
            border: 1px solid #3c6eff20;
        }

        .footer-item i {
            color: #3c6eff;
        }

        /* Particles */
        .particles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(60, 106, 255, 0.3);
            border-radius: 50%;
            animation: particleFloat 8s ease-in-out infinite;
        }

        @keyframes particleFloat {
            0% { transform: translateY(0) translateX(0) scale(1); opacity: 0.3; }
            50% { transform: translateY(-50px) translateX(20px) scale(1.5); opacity: 0.8; }
            100% { transform: translateY(-100px) translateX(-20px) scale(1); opacity: 0.3; }
        }

        /* Redirect counter */
        .redirect-counter {
            margin-top: 1rem;
            color: #5b74a0;
            font-size: 0.9rem;
        }

        .counter-number {
            color: #3c6eff;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Responsive */
        @media (max-width: 600px) {
            .splash-card {
                padding: 2rem 1.5rem;
                border-radius: 40px;
            }
            .logo-icon {
                width: 80px;
                height: 80px;
            }
            .logo-icon i {
                font-size: 40px;
            }
            .logo-text {
                font-size: 2rem;
            }
            .company-footer {
                flex-direction: column;
                gap: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Background orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <!-- Floating particles -->
    <div class="particles">
        <?php for($i = 0; $i < 20; $i++): ?>
            <div class="particle" style="top: <?php echo rand(0, 100); ?>%; left: <?php echo rand(0, 100); ?>%; animation-delay: <?php echo rand(0, 5); ?>s;"></div>
        <?php endfor; ?>
    </div>

    <!-- Main splash screen -->
    <div class="splash-container">
        <div class="splash-card">
            <!-- Logo -->
            <div class="logo-wrapper">
                <div class="logo-icon">
                    <i class="fas fa-industry"></i>
                </div>
                <div class="logo-text">NOVA</div>
                <div class="logo-sub">INVENTORY SUITE</div>
            </div>

            <!-- Loading spinner -->
            <div class="spinner-container">
                <div class="spinner">
                    <div class="spinner-circle"></div>
                    <div class="spinner-pulse"></div>
                </div>
            </div>

            <!-- Message -->
            <div class="message">
                <i class="fas fa-<?php echo isset($_SESSION['user']) ? 'tachometer-alt' : 'sign-in-alt'; ?>"></i>
                <?php echo $message; ?>
            </div>

            <!-- Progress bar -->
            <div class="progress-container">
                <div class="progress-bar" id="progressBar"></div>
            </div>

            <!-- Redirect counter -->
            <div class="redirect-counter">
                Redirecting in <span class="counter-number" id="counter">3</span> seconds...
            </div>

            <!-- Company footer -->
            <div class="company-footer">
                <div class="footer-item">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Connection</span>
                </div>
                <div class="footer-item">
                    <i class="fas fa-bolt"></i>
                    <span>Enterprise Grade</span>
                </div>
                <div class="footer-item">
                    <i class="fas fa-clock"></i>
                    <span><?php echo date('Y'); ?> © NOVA Systems</span>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for countdown and redirect -->
    <script>
        (function() {
            const redirectUrl = '<?php echo $redirect; ?>';
            const messageElement = document.getElementById('message');
            let secondsLeft = 3;
            const counterElement = document.getElementById('counter');

            // Update countdown
            const countdownInterval = setInterval(function() {
                secondsLeft--;
                if (counterElement) {
                    counterElement.textContent = secondsLeft;
                }
                
                if (secondsLeft <= 0) {
                    clearInterval(countdownInterval);
                    window.location.href = redirectUrl;
                }
            }, 1000);

            // Animate progress bar
            const progressBar = document.getElementById('progressBar');
            let width = 0;
            const progressInterval = setInterval(function() {
                if (width >= 100) {
                    clearInterval(progressInterval);
                } else {
                    width += 33.33; // 100% over 3 seconds
                    if (progressBar) {
                        progressBar.style.width = width + '%';
                    }
                }
            }, 1000);

            // Prefetch the next page for faster loading
            const prefetchLink = document.createElement('link');
            prefetchLink.rel = 'prefetch';
            prefetchLink.href = redirectUrl;
            document.head.appendChild(prefetchLink);

            // Fallback in case countdown fails
            setTimeout(function() {
                window.location.href = redirectUrl;
            }, 3500);

            // Add some interactive particle movement
            document.addEventListener('mousemove', function(e) {
                const particles = document.querySelectorAll('.particle');
                const mouseX = e.clientX / window.innerWidth;
                const mouseY = e.clientY / window.innerHeight;
                
                particles.forEach((particle, index) => {
                    const speed = index * 0.02;
                    const x = (mouseX - 0.5) * 30 * speed;
                    const y = (mouseY - 0.5) * 30 * speed;
                    particle.style.transform = `translate(${x}px, ${y}px)`;
                });
            });

            // Console message for developers
            console.log('%c🚀 NOVA Inventory System', 'color: #3c6eff; font-size: 16px; font-weight: bold;');
            console.log('%cRedirecting to: ' + redirectUrl, 'color: #2ecc71; font-size: 12px;');
        })();
    </script>
</body>
</html>