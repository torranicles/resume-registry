<?php
    session_start();
    require_once 'pdo.php';
    if (!isset($_SESSION['name']) || strlen($_SESSION['name']) < 1) {
        $_SESSION['error'] ='Please login to continue.';
        header('Location: login.php');
        return;
    } else if (isset($_POST['cancel'])) {
        header('Location: index.php');
        return;
    }
    for ($x=1;$x<=9;$x++) {
        if (!isset($_POST['year'.$x])) continue;
        if (!isset($_POST['info'.$x])) continue;
        $year = $_POST['year'.$x];
        $info = $_POST['info'.$x];
        if (strlen($year) < 1 || strlen($info) <1) {
            $_SESSION['error'] = "All fields are required";
            header('Location: add.php');
            return;
        } else if (!is_numeric($year)) {
            $_SESSION['error'] = "Position year must be numeric";
            header('Location: add.php');
            return;
        } 
    }
    for ($x=1;$x<=9;$x++) {
        if (!isset($_POST['edu_year'.$x])) continue;
        if (!isset($_POST['edu_school'.$x])) continue;
        $year = $_POST['edu_year'.$x];
        $school = $_POST['edu_school'.$x];
        if (strlen($year) < 1 || strlen($school) < 1) {
            $_SESSION['error'] = "All fields are required";
            header('Location: add.php');
            return;
        } else if (!is_numeric($year)) {
            $_SESSION['error'] = "Year must be numeric";
            header('Location: add.php');
            return;
        } 
    }
    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) 
            && isset($_POST['headline']) && isset($_POST['summary'])){
        $query = 'INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)';
        $stmt = $pdo->prepare($query);
        if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 
                || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            $_SESSION['error'] = "All fields are required";
            header('Location: add.php');
            return;
        } else { 
            $stmt->execute(
                array(
                    ':uid' => $_SESSION['user_id'],
                    ':fn' => $_POST['first_name'],
                    ':ln' => $_POST['last_name'],
                    ':em' => $_POST['email'],
                    ':he' => $_POST['headline'],
                    ':su' => $_POST['summary']
                )
            );
            $rank_edu = 1;
            $eduProfile_id = $pdo->lastInsertId();
            for ($x=1;$x<=9;$x++) {
                if (!isset($_POST['edu_year'.$x])) continue; 
                if (!isset($_POST['edu_school'.$x])) continue;
                    $year = $_POST['edu_year'.$x];
                    $school = $_POST['edu_school'.$x];
                    $institution_id = false;
                    $stmt = $pdo-> prepare('SELECT institution_id FROM institution WHERE name = :name');
                    $stmt->execute(array(
                        ':name' => $school
                    ));
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($row) $institution_id =$row['institution_id'];
                    if (!$institution_id) {
                        $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:name)');
                        $stmt->execute(array(
                            ':name' => $school
                        ));
                        $institution_id =$pdo->lastInsertId();
                    };
                    $stmt = $pdo->prepare('INSERT INTO Education (profile_id,rank,year,institution_id) VALUES (:pid,:rank,:year,:iid)');
                    $stmt->execute(array(
                        ':pid' => $eduProfile_id,
                        ':rank' => $rank_edu,
                        ':year' => $year,
                        ':iid' => $institution_id
                    ));
                $rank_edu++;
            }
            $rank_pos = 1;
            $posProfile_id = $pdo->lastInsertId();
            for ($x=1;$x<=9;$x++) {
                if (!isset($_POST['year'.$x])) continue; 
                if (!isset($_POST['info'.$x])) continue;
                    $year = $_POST['year'.$x];
                    $info = $_POST['info'.$x];
                    $stmt = $pdo->prepare('INSERT INTO Position (profile_id,rank,year,description) VALUES (:pid,:rank,:year,:desc)');
                    $stmt->execute(array(
                        ':pid' => $posProfile_id,
                        ':rank' => $rank_pos,
                        ':year' => $year,
                        ':desc' => $info
                    ));
                $rank_pos++;
            }
            $_SESSION['success']= "Profile added";
            header('Location: index.php');
            return;
        }
    }
?>