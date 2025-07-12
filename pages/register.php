<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Gong cha</title>
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
        .register-container {
            max-width: 520px;
            margin: 0;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 16px rgba(190,22,53,0.08);
            padding: 40px 32px 32px 32px;
            text-align: center;
        }
        .register-title {
            font-size: 2rem;
            color: #be1635;
            margin-bottom: 24px;
            font-weight: 700;
        }
        .register-form {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }
        .register-form input {
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 1rem;
            font-family: 'Georgia', serif;
        }
        .register-form button {
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
        .register-form button:hover {
            background: #a0122b;
        }
        .register-link {
            margin-top: 18px;
            font-size: 0.98rem;
        }
        .register-link a {
            color: #be1635;
            text-decoration: none;
            font-weight: 600;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php require_once '../components/header.php'; ?>
    <div class="register-container">
        <div class="register-title">Register</div>
        <form class="register-form" method="post" action="#">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <div class="register-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html> 