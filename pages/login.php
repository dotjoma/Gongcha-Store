<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gong cha</title>
    <style>
        body {
            background: #18171c;
            font-family: 'Georgia', serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            max-width: 480px;
            margin: 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(190,22,53,0.08);
            padding: 40px 32px 32px 32px;
            text-align: center;
        }
        .login-title {
            font-size: 2rem;
            color: #be1635;
            margin-bottom: 24px;
            font-weight: 700;
        }
        .login-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .login-form input {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
            font-family: 'Georgia', serif;
        }
        .login-form button {
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
        .login-form button:hover {
            background: #a0122b;
        }
        .login-link {
            margin-top: 18px;
            font-size: 0.98rem;
        }
        .login-link a {
            color: #be1635;
            text-decoration: none;
            font-weight: 600;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php require_once '../components/header.php'; ?>
    <div class="login-container">
        <div class="login-title">Login</div>
        <form class="login-form" method="post" action="#">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <div class="login-link">
            Don't have an account? <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html> 