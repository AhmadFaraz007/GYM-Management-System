<?php
require '../includes/db.php'; // contains your PDO connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['name']);  // frontend field = "name"
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']); // should be 'admin', 'trainer', or 'member'

    // Validate role
    if (!in_array($role, ['admin', 'trainer', 'member'])) {
        $error = "Invalid role selected.";
    } else {
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->rowCount() > 0) {
            $error = "Email already exists.";
        } else {
    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->execute([$full_name, $email, $hashedPassword, $role]);

            $success = "Registration successful! You can now login.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FlexFusion</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #ff4d4d;
            --secondary-color: #2c3e50;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            display: flex;
        }

        .register-image {
            flex: 1;
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)),
                        url('https://images.unsplash.com/photo-1534438327276-14e5300c3a48?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 40px;
            text-align: center;
        }

        .register-form {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .register-form h2 {
            color: var(--secondary-color);
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-control {
            height: 50px;
            border-radius: 25px;
            padding-left: 45px;
            border: 2px solid #eee;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .form-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .form-select {
            height: 50px;
            border-radius: 25px;
            padding-left: 45px;
            border: 2px solid #eee;
            transition: all 0.3s ease;
        }

        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: none;
        }

        .btn-register {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 25px;
            height: 50px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-register:hover {
            background-color: #ff3333;
            transform: translateY(-2px);
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
        }

        .login-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .alert {
            border-radius: 25px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
            }
            .register-image {
                display: none;
            }
            .register-form {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-image">
            <div>
                <h2>Join FlexFusion Today!</h2>
                <p>Start your fitness journey with us and transform your life.</p>
            </div>
        </div>
        <div class="register-form">
            <h2>Create Account</h2>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $success; ?>
                </div>
            <?php endif; ?>

    <form method="post" action="">
                <div class="form-group">
                    <i class="fas fa-user form-icon"></i>
                    <input type="text" class="form-control" name="name" placeholder="Full Name" required>
                </div>

                <div class="form-group">
                    <i class="fas fa-envelope form-icon"></i>
                    <input type="email" class="form-control" name="email" placeholder="Email Address" required>
                </div>

                <div class="form-group">
                    <i class="fas fa-lock form-icon"></i>
                    <input type="password" class="form-control" name="password" placeholder="Password" required>
                </div>

                <div class="form-group">
                    <i class="fas fa-user-tag form-icon"></i>
                    <select name="role" class="form-select" required>
            <option value="">-- Select Role --</option>
            <option value="admin">Admin</option>
            <option value="trainer">Trainer</option>
            <option value="member">Member</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-register w-100">Register</button>
    </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Login Now</a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
