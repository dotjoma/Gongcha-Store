<?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gong cha - Brewing Happiness!</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body, html {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Georgia', serif;
            overflow-x: hidden;
        }
        .landing-bg {
            position: relative;
            width: 100vw;
            height: 100vh;
            min-height: 600px;
            background: url('assets/banner.jpg') center center/cover no-repeat;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .landing-bg::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.45);
            z-index: 1;
            animation: backgroundPulse 4s ease-in-out infinite;
        }
        @keyframes backgroundPulse {
            0%, 100% {
                background: rgba(0,0,0,0.45);
            }
            50% {
                background: rgba(0,0,0,0.5);
            }
        }
        .landing-content {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
            width: 100%;
        }
        .landing-content h1 {
            font-size: 5vw;
            font-weight: 600;
            margin-bottom: 32px;
            letter-spacing: 2px;
            animation: fadeInUp 1.2s ease-out;
            opacity: 0;
            animation-fill-mode: forwards;
        }
        .see-more-btn {
            background: #fff;
            color: #222;
            font-weight: 700;
            border: none;
            border-radius: 30px;
            padding: 16px 40px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            animation: fadeInUp 1.2s ease-out 0.3s;
            opacity: 0;
            animation-fill-mode: forwards;
            transform: translateY(20px);
        }
        .see-more-btn:hover {
            background: #e50914;
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-10px);
            }
        }
        @keyframes pulse {
            0% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
            100% {
                transform: scale(1);
            }
        }
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        /* Scroll-triggered animations */
        .animate-on-scroll {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease-out;
        }
        
        .animate-on-scroll.animate {
            opacity: 1;
            transform: translateY(0);
        }
        
        .animate-on-scroll.slide-left {
            transform: translateX(-50px);
        }
        
        .animate-on-scroll.slide-left.animate {
            transform: translateX(0);
        }
        
        .animate-on-scroll.slide-right {
            transform: translateX(50px);
        }
        
        .animate-on-scroll.slide-right.animate {
            transform: translateX(0);
        }
        
        .animate-on-scroll.scale-in {
            transform: scale(0.8) translateY(30px);
        }
        
        .animate-on-scroll.scale-in.animate {
            transform: scale(1) translateY(0);
        }
        @media (max-width: 700px) {
            .landing-content h1 {
                font-size: 2.2rem;
            }
            .see-more-btn {
                padding: 12px 24px;
                font-size: 1rem;
            }
        }

        .gc-modal {
            display: none;
            position: fixed;
            z-index: 1001;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(24,23,28,0.85);
            align-items: center;
            justify-content: center;
        }
        .gc-modal.show {
            display: flex;
        }
        .gc-modal-content {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 2px 24px rgba(190,22,53,0.13);
            padding: 40px 36px 32px 36px;
            min-width: 340px;
            max-width: 95vw;
            width: 400px;
            text-align: center;
            position: relative;
            animation: gcModalFadeIn 0.4s ease-out;
        }
        @keyframes gcModalFadeIn {
            from { 
                transform: translateY(-50px) scale(0.9); 
                opacity: 0; 
            }
            to { 
                transform: translateY(0) scale(1); 
                opacity: 1; 
            }
        }
        .gc-modal-close {
            position: absolute;
            top: 18px; right: 22px;
            font-size: 2rem;
            color: #be1635;
            cursor: pointer;
            font-weight: 700;
        }
        .modal-title {
            font-size: 2rem;
            color: #be1635;
            margin-bottom: 24px;
            font-weight: 700;
        }
        .modal-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .modal-form input {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
            font-family: 'Georgia', serif;
        }
        .modal-form button {
            background: #be1635;
            color: #fff;
            border: none;
            border-radius: 6px;
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .modal-form button:hover {
            background: #a0122b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(190, 22, 53, 0.3);
        }
        .modal-form button:active {
            transform: translateY(0);
        }
        .modal-form input {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
            font-family: 'Georgia', serif;
            transition: all 0.3s ease;
        }
        .modal-form input:focus {
            outline: none;
            border-color: #be1635;
            box-shadow: 0 0 0 3px rgba(190, 22, 53, 0.1);
            transform: translateY(-1px);
        }
        .modal-link {
            margin-top: 18px;
            font-size: 0.98rem;
        }
        .modal-link a {
            color: #be1635;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
        }
        .modal-link a:hover {
            text-decoration: underline;
        }
        .modal-msg {
            margin-bottom: 10px;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
        }
        @media (max-width: 600px) {
            .gc-modal-content {
                min-width: 0;
                width: 95vw;
                padding: 24px 8vw 24px 8vw;
            }
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>
    <!-- Login Modal -->
    <div id="loginModal" class="gc-modal">
        <div class="gc-modal-content">
            <span class="gc-modal-close" onclick="closeModal('loginModal')">&times;</span>
            <div class="modal-title">Login</div>
            <form class="modal-form" method="post" action="#" autocomplete="off">
                <input type="email" name="email" placeholder="Email" required autocomplete="off">
                <input type="password" name="password" placeholder="Password" required autocomplete="off">
                <button type="submit">Login</button>
            </form>
            <div class="modal-link">
                Don't have an account? <a href="#" onclick="switchModal('loginModal','registerModal')">Register</a>
            </div>
        </div>
    </div>
    <!-- Register Modal -->
    <div id="registerModal" class="gc-modal">
        <div class="gc-modal-content">
            <span class="gc-modal-close" onclick="closeModal('registerModal')">&times;</span>
            <div class="modal-title">Register</div>
            <form class="modal-form" method="post" action="#" autocomplete="off">
                <input type="text" name="fname" placeholder="First Name" required autocomplete="off">
                <input type="text" name="lname" placeholder="Last Name" required autocomplete="off">
                <input type="email" name="email" placeholder="Email" required autocomplete="off">
                <input type="password" name="password" placeholder="Password" required autocomplete="off">
                <button type="submit">Register</button>
            </form>
            <div class="modal-link">
                Already have an account? <a href="#" onclick="switchModal('registerModal','loginModal')">Login</a>
            </div>
        </div>
    </div>
    <script>
        function openModal(id) {
            document.getElementById(id).classList.add('show');
        }
        function closeModal(id) {
            document.getElementById(id).classList.remove('show');
        }
        function switchModal(closeId, openId) {
            closeModal(closeId);
            setTimeout(function(){ openModal(openId); }, 180);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.gc-modal').forEach(function(modal) {
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) closeModal(modal.id);
                });
            });

            // AJAX for login
            document.querySelector('#loginModal .modal-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);
                data.append('action', 'login');
                fetch('includes/process_auth.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    let msg = form.querySelector('.modal-msg');
                    if (!msg) {
                        msg = document.createElement('div');
                        msg.className = 'modal-msg';
                        form.prepend(msg);
                    }
                    msg.textContent = res.message;
                    msg.style.color = res.success ? '#27ae60' : '#be1635';
                    if (res.success) {
                        // Redirect to clean URL without modal parameters
                        setTimeout(() => {
                            const cleanUrl = window.location.pathname;
                            window.location.href = cleanUrl;
                        }, 900);
                    }
                })
                .catch(() => alert('Network error.'));
            });

            // AJAX for register
            document.querySelector('#registerModal .modal-form').addEventListener('submit', function(e) {
                e.preventDefault();
                const form = this;
                const data = new FormData(form);
                data.append('action', 'register');
                fetch('includes/process_auth.php', {
                    method: 'POST',
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    let msg = form.querySelector('.modal-msg');
                    if (!msg) {
                        msg = document.createElement('div');
                        msg.className = 'modal-msg';
                        form.prepend(msg);
                    }
                    msg.textContent = res.message;
                    msg.style.color = res.success ? '#27ae60' : '#be1635';
                    if (res.success) setTimeout(() => switchModal('registerModal','loginModal'), 1200);
                })
                .catch(() => alert('Network error.'));
            });
        });

        // Scroll animations
        function animateOnScroll() {
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach(element => {
                const elementTop = element.getBoundingClientRect().top;
                const elementVisible = 150;
                
                if (elementTop < window.innerHeight - elementVisible) {
                    element.classList.add('animate');
                }
            });
        }

        // Add scroll event listener
        window.addEventListener('scroll', animateOnScroll);
        
        // Initial check for elements in view
        document.addEventListener('DOMContentLoaded', function() {
            animateOnScroll();
        });
    </script>
    <div class="landing-bg">
        <div class="landing-content">
            <h1>Brewing Happiness!</h1>
            <button class="see-more-btn">SEE MORE</button>
        </div>
    </div>
    <section class="party-section">
        <div class="party-container">
            <div class="party-left">
                <h2>
                    Reserve<br>
                    Your<br>
                    <span class="red-text">Gong cha<br>Party<br>Today</span>
                </h2>
                <button class="party-btn">BOOK NOW <span class="arrow">⭢</span></button>
            </div>
            <div class="party-right">
                <img src="assets/Gong-cha-Cheers.png" alt="Gong cha Cheers" class="party-img">
            </div>
        </div>
    </section>
    <style>
    .party-section {
        background: #fafafa;
        padding: 80px 0 40px 0;
        border-top: 2px solid #333;
        margin-top: 0;
        min-height: 70vh;
    }
    .party-container {
        max-width: 90vw;
        width: 92vw;
        margin: 0 auto;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        gap: 40px;
        padding: 32px 0 0 0;
    }
    .party-left {
        flex: 1 1 350px;
        max-width: 420px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: center;
        padding-left: 2vw;
    }
    .party-left h2 {
        font-size: 3.2rem;
        font-family: 'Georgia', serif;
        color: #3a3431;
        font-weight: 400;
        margin-bottom: 32px;
        line-height: 1.1;
        animation: slideInLeft 1s ease-out 0.5s;
        opacity: 1;
        animation-fill-mode: forwards;
    }
    .party-left .red-text {
        color: #be1635;
        font-weight: 500;
        font-size: 3.2rem;
        font-family: 'Georgia', serif;
        animation: pulse 2s ease-in-out infinite 2s;
    }
    .party-btn {
        background: #222;
        color: #fff;
        border: none;
        border-radius: 24px;
        padding: 12px 32px;
        font-size: 1rem;
        font-family: 'Georgia', serif;
        font-weight: 600;
        letter-spacing: 2px;
        margin-top: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        animation: bounceIn 1s ease-out 1s;
        opacity: 1;
        animation-fill-mode: forwards;
    }
    .party-btn .arrow {
        font-size: 1.1em;
        margin-left: 4px;
        transition: transform 0.3s ease;
    }
    .party-btn:hover {
        background: #be1635;
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 6px 20px rgba(190, 22, 53, 0.3);
    }
    .party-btn:hover .arrow {
        transform: translateX(5px);
    }
    .party-right {
        flex: 2 1 500px;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: slideInRight 1s ease-out 0.8s;
        opacity: 1;
        animation-fill-mode: forwards;
    }
    .party-img {
        max-width: 520px;
        width: 100%;
        height: auto;
        display: block;
        animation: float 3s ease-in-out infinite 2.5s;
    }
    @media (max-width: 900px) {
        .party-container {
            flex-direction: column;
            align-items: center;
            gap: 24px;
        }
        .party-left, .party-right {
            max-width: 100%;
            padding-left: 0;
        }
        .party-left h2, .party-left .red-text {
            font-size: 2.1rem;
        }
        .party-img {
            max-width: 320px;
        }
    }
    </style>
    <?php include 'components/footer.php'; ?>
</body>
</html>