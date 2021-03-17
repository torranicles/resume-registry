<?php
    session_start();
    require_once 'pdo.php';
    if (isset($_POST['logout'])) {
        header('Location: index.php');
        return;
    }
?>

<!DOCTYPE html>
<html>
    <body>
        <h1>Profile Information</h1>
        <?php
            if (isset($_SESSION['success'])) {
                echo '<p style="color: green">'
                    . htmlentities($_SESSION['success'])
                    . '</p>';
                unset($_SESSION['success']);
            }
        ?>
        <?php 
            $data = $pdo->prepare('SELECT * FROM profile WHERE profile_id = :pid');
            $data->execute(array(
                ':pid' => $_REQUEST['profile_id']
            ));
            $row = $data->fetch(PDO::FETCH_ASSOC);
            while($row) {
                "<p>First Name:"
                .htmlentities($row['first_name'])
                ."</p><p>Last Name:"
                .htmlentities($row['last_name'])
                ."</p><p>Email:"
                .htmlentities($row['email'])
                ."</p><p>Headline:"
                .htmlentities($row['headline'])
                ."</p><p>Summary:"
                .htmlentities($row['summary'])
                ."</p>";
            };
            $pos = $pdo->query('SELECT * FROM Position');
            while ($row = $pos->fetch(PDO::FETCH_ASSOC)) {
                echo "<p>Position:\n <ul><li>"
                    .htmlentities($row['year'])
                    .":"
                    .htmlentities($row['description'])
                    ."</li></ul></p>";
            };
            $stmt = $pdo->prepare('SELECT year, name FROM education JOIN institution
                ON education.institution_id = Institution.institution_id WHERE profile_id = :prof
                ORDER BY rank');
            $stmt->execute(array(
                ':prof' => $_REQUEST['profile_id']
            ));
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<p>Education:\n <ul><li>"
                        .htmlentities($row['year'])
                        .":"
                        .htmlentities($row['name'])
                        ."</li></ul></p>";
            };
        ?>
        <a href="index.php">Done</a>
    </body>
</html>