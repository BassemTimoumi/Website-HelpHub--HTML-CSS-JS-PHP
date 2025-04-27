<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=helphub', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $pseudo = $_POST['pseudo'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("INSERT INTO donateurs (nom, prenom, email, cin, pseudo, mot_de_passe) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $cin, $pseudo, $password]);

    header("Location: login.html");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>