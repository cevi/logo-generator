<?php
    session_start();

    if (isset($_SESSION['login'])) {
        header('LOCATION:admin.php');
        die();
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv='content-type' content='text/html;charset=utf-8' />
        <title>Administrator Login</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    </head>
    <body>
        <div class="container">
            <h3 class="text-center">Login</h3>

            <?php
                if (isset($_POST['submit'])) {

                    require_once('DotEnv.php');

                    (new DotEnv(__DIR__ . '/.env'))->load();

                    $hashed_password = getenv('ADMIN_PASSWORD_HASH');
                    $username = $_POST['username']; $password = $_POST['password'];
                    if ($username === 'admin' && password_verify($password , $hashed_password)) {
                        $_SESSION['login'] = true;
                        header('LOCATION:admin.php');
                        die();
                    }

                    else {
                        echo "<div class='alert alert-danger'>Username and Password do not match.</div>";
                    }

            }
            ?>

            <form action="" method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" required>
                </div>
                <div class="form-group">
                    <label for="pwd">Password</label>
                    <input type="password" class="form-control" id="pwd" name="password" required>
                </div>
                <button type="submit" name="submit" class="btn btn-default">Login</button>
            </form>
        </div>
    </body>
</html>
