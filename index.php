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
            transition: background 0.2s, color 0.2s;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
        }
        .see-more-btn:hover {
            background: #e50914;
            color: #fff;
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
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>
    <!-- Login Modal -->
    <div id="loginModal" class="gc-modal">
        <div class="gc-modal-content">
            <span class="gc-modal-close" onclick="closeModal('loginModal')">&times;</span>
            <div class="modal-title">Login</div>
            <form class="modal-form" method="post" action="#">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
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
            <form class="modal-form" method="post" action="#">
                <input type="text" name="fname" placeholder="First Name" required>
                <input type="text" name="lname" placeholder="Last Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
            <div class="modal-link">
                Already have an account? <a href="#" onclick="switchModal('registerModal','loginModal')">Login</a>
            </div>
        </div>
    </div>
    <style>
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
        animation: gcModalFadeIn 0.2s;
    }
    @keyframes gcModalFadeIn {
        from { transform: translateY(-30px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
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
        transition: background 0.2s;
    }
    .modal-form button:hover {
        background: #a0122b;
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
    @media (max-width: 600px) {
        .gc-modal-content {
            min-width: 0;
            width: 95vw;
            padding: 24px 8vw 24px 8vw;
        }
    }
    </style>
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
    // Optional: Close modal on background click
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.gc-modal').forEach(function(modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) closeModal(modal.id);
            });
        });
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
    }
    .party-left .red-text {
        color: #be1635;
        font-weight: 500;
        font-size: 3.2rem;
        font-family: 'Georgia', serif;
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
        transition: background 0.2s, color 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .party-btn .arrow {
        font-size: 1.1em;
        margin-left: 4px;
    }
    .party-btn:hover {
        background: #be1635;
        color: #fff;
    }
    .party-right {
        flex: 2 1 500px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .party-img {
        max-width: 520px;
        width: 100%;
        height: auto;
        display: block;
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