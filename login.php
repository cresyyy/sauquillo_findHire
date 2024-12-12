<?php include 'core/dbConfig.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .gradient-custom-2 {
            background: linear-gradient(to right, #004db2, #0097b2, #00b283);
        }

        .btn-login{
         background-color: #0097b2;
         color: white;
        }

        .btn-login:hover{
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
                  <img src="assets/FINDHIRE_Logo.png" style="width: 185px;" alt="logo">
                </div>

                <form action="" method="POST">
                  <?php
                  // Display error messages dynamically
                  if (isset($error_message)) {
                      echo "<div class='alert alert-danger mb-4'>" . $error_message . "</div>";
                  }
                  
                if (isset($_GET['message']) && $_GET['message'] === 'registered_successfully') {
                    echo "<div class='alert alert-success'>Registration successful! Please login.</div>";
                }

                  ?>
                  <p>Please login to your account</p>

                  <div data-mdb-input-init class="form-outline mb-4">
                    <input type="text" id="form2Example11" class="form-control" name="username" required
                      placeholder="Username" />
                  </div>

                  <div data-mdb-input-init class="form-outline mb-4">
                    <input type="password" id="form2Example22" class="form-control" name="password" required
                    placeholder="Password" />
                  </div>

                  <div class="text-center pt-1 mb-5 pb-1">
                    <button data-mdb-button-init data-mdb-ripple-init class="btn btn-login btn-block fa-lg mb-3 px-5" name="login" type="submit">Log in</button>
                  </div>

                  <div class="d-flex align-items-center justify-content-center pb-4">
                    <p class="mb-0 me-2">Don't have an account?</p>
                    <a href="register.php" class="btn btn-outline-danger">Create new</a>
                  </div>

                </form>

              </div>
            </div>
            <div class="col-lg-6 d-flex align-items-center gradient-custom-2">
              <div class="text-white px-3 py-4 p-md-5 mx-md-4">
                <h4 class="mb-4">Your career journey starts here in FindHire!</h4>
                <p class="small mb-0"> Your ultimate partner in finding the perfect job or hiring the right talent! Log in now to continue your journey toward success.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
session_start();
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $role);
        $stmt->fetch();
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role'] = $role;

            if ($role == 'hr') {
                header("Location: hr_home.php");
            } elseif ($role == 'applicant') {
                header("Location: applicant_home.php");
            }
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "User not found.";
    }
    $stmt->close();
}
?>
