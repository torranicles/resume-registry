<?php
    require_once 'pdo.php';
    session_start();
    if (!isset($_SESSION['name'])) {
        header('Location: login.php');
        return;
    }
    $stmt = $pdo->query('SELECT profile_id, first_name, last_name, headline FROM profile');
?>

<!DOCTYPE html>
<html>
    <body>
        <h1>Resume Registry</h1>
        <a href="add.php">
            Add new entry
        </a>
        <a href="logout.php">
            Logout
        </a>
        <table>
            <?php
                if (isset($_SESSION['error'])) {
                    echo '<p style="color: red">'
                        . htmlentities($_SESSION['error'])
                        . '</p>';
                    unset($_SESSION['error']);
                }
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                while ($row) {
                    echo '<tr><td><a href="view.php?profile_id='.$row['profile_id'].'">'
                        . htmlentities($row['first_name']." ".$row['last_name'])
                        . '<a/></td><td>'
                        . htmlentities($row['headline'])
                        . "</td><td>"
                        . '<a href="edit.php?profile_id='.$row['profile_id'].'">Edit</a>|'
                        . '<a href="delete.php?profile_id='.$row['profile_id'].'">Delete</a>'
                        . "</td></tr>";
                }
            ?>
        </table>
    </body>
</html>