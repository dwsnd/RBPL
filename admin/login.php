<?php
session_start();
require_once '../includes/db.php';

// Check if already logged in
if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit();
}

// Handle login
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $admin = $result->fetch_assoc();
        if (password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id_admin'];
            $_SESSION['admin_nama'] = $admin['nama_lengkap'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Email tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin | Ling-Ling Pet Shop</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Poppins', sans-serif;
            margin: 0;
            overflow-x: hidden;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 20px;
        }

        .card {
            background: #fff;
            border: none;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: none;
            border: none;
            text-align: center;
            padding: 30px 20px 0;
        }

        .card-header img {
            width: 80px;
            margin-bottom: 15px;
        }

        .card-header h4 {
            color: #333;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .card-header p {
            color: #666;
            font-size: 14px;
        }

        .form-control {
            border-radius: 8px;
            padding: 12px;
            border: 1px solid #ddd;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #ff7f50;
        }

        .btn-login {
            background: #ff7f50;
            border: none;
            border-radius: 8px;
            padding: 12px;
            font-weight: 500;
            width: 100%;
            margin-top: 10px;
        }

        .btn-login:hover {
            background: #e5673d;
        }

        .input-group-text {
            background: none;
            border: 1px solid #ddd;
            border-right: none;
        }

        .input-group .form-control {
            border-left: none;
        }

        .input-group .form-control:focus {
            border-left: none;
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="card">
            <div class="card-header">
                <i class="fa-solid fa-paw text-orange-500" style="font-size: 48px; color: #ff7f50;"></i>
                <h4>Ling-Ling Pet Shop</h4>
                <p>Login Admin</p>
            </div>
            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" class="form-control" placeholder="Masukkan email" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password"
                                required>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>