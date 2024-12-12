<?php
include 'core/dbConfig.php';

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $password, $email, $role);

    if ($stmt->execute()) {
        header("Location: login.php?message=registered_successfully");
        exit();
    } else {
        $error_message = "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gradient-custom-2 {
            background: linear-gradient(to right, #004db2, #0097b2, #00b283);
        }

        .btn-register{
         background-color: #0097b2;
         color: white;
        }

        .btn-register:hover{
            border-color: #0097b2;
            color:#0097b2;
        }

        .btn-outline-danger {
            color: #004db2;
            border-color: #004db2;
        }

        .btn-outline-danger:hover {
            background-color: #004db2;
            border: none;
            color: #fff;
        }

        @media (min-width: 768px) {
            .gradient-form {
                height: 100vh !important;
            }
        }
        @media (min-width: 769px) {
            .gradient-custom-2 {
                border-top-right-radius: .3rem;
                border-bottom-right-radius: .3rem;
            }
        }
    </style>
</head>
<body>
<section class="h-100 gradient-form" style="background-color: #eee;">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-xl-10">
        <div class="card rounded-3 text-black">
          <div class="row g-0">
            <div class="col-lg-6">
              <div class="card-body p-md-5 mx-md-4">

                <div class="text-center">
                  <img src="assets/FINDHIRE_Logo.png"
                    style="width: 185px;" alt="logo">
                </div>

                <form action="" method="POST">
                  <?php if (!empty($error_message)): ?>
                      <div class='alert alert-danger mb-4'><?= htmlspecialchars($error_message) ?></div>
                  <?php endif; ?>
                  <p>Create your account</p>

                  <div class="form-outline mb-4">
                    <input type="text" id="username" class="form-control" name="username" required placeholder="Enter your username" />
                  </div>

                  <div class="form-outline mb-4">
                    <input type="password" id="password" class="form-control" name="password" required placeholder="Enter your password" />
                  </div>

                  <div class="form-outline mb-4">
                    <input type="email" id="email" class="form-control" name="email" required placeholder="Enter your email" />
                  </div>

                  <div class="form-outline mb-4">
                    <select id="role" class="form-select" name="role" required>
                        <option value="" disabled selected>Role</option>
                        <option value="hr">HR</option>
                        <option value="applicant">Applicant</option>
                    </select>
                </div>


                  <div class="text-center pt-1 mb-5 pb-1">
                    <button class="btn btn-register btn-block fa-lg mb-1 px-5" name="register" type="submit">Register</button>
                  </div>

                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Already have an account?</p>
                    <a href="login.php" class="btn btn-outline-danger">Login</a>
                  </div>
                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">Welcome to FindHire!</h4>
                <p class="small mb-0">Join FindHire today and unlock endless opportunities to connect with top employers or discover exceptional talent! Sign up and take the first step toward achieving your goals.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
