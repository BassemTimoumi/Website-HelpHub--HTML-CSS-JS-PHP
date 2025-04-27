<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=helphub', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    session_start();

    $pseudo = $_POST['pseudo'];
    $password = $_POST['password'];
    $type = $_POST['type_utilisateur'];

    if ($type == 'association') {
        $stmt = $pdo->prepare("SELECT * FROM associations WHERE pseudo = ?");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM donateurs WHERE pseudo = ?");
    }

    $stmt->execute([$pseudo]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($password === $user['mot_de_passe']) {
            $_SESSION['user'] = $user;
            $_SESSION['type'] = $type;
            header("Location: dashboard_" . $type . ".php");
        } else {
            echo "Mot de passe invalide !";
        }
    } else {
        echo "Utilisateur non trouvé !";
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>