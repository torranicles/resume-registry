<?php 
    session_start();
    require_once 'pdo.php';
    if (isset($_POST['cancel'])) {
        header('Location: index.php');
        return;
    };
    if (isset($_POST['username']) && isset($_POST['password'])) {
        if (strlen($_POST['username']) < 1 || strlen($_POST['password']) < 1) {
            $_SESSION['error'] = 'Username and password are required.';
            header('Location: login.php');
            return;
        } else {
            $hash = hash('md5', $_POST['password']);
            $stmt = $pdo->prepare('SELECT user_id, name from users WHERE username = :user AND passowrd = :pw');
            $stmt->execute(array(
                ':user' => $_POST['username'],
                ':pw' => $hash
            ));
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $_SESSION['name'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];
                header('Location: index.php');
                return;
            } else {
                $_SESSION['error'] = 'Incorrect username or password.';
                header('Location: login.php');
                return;
            }
        }
    }
?>

<!DOCTYPE html>
<html>
    <body>
        <?php
            if (isset($_SESSION['error'])) {
                echo "<p style='color: red'>"
                    . htmlentities($_SESSION['error'])
                    . "</p>";
                unset($_SESSION['error']);
            }
        ?>
        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username">
            <label for="password">Password:</label>
            <input type="text" name="password">
            <input type="submit" value="Log in">
            <input type="submit" name="cancel" value="Cancel">
        </form>
    </body>
</html>