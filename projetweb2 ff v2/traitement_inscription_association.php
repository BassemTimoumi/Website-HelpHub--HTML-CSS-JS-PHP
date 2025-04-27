<?php
try {
    $pdo = new PDO('mysql:host=localhost;dbname=helphub', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $nom_association = $_POST['nom_association'];
    $adresse = $_POST['adresse'];
    $identifiant_fiscal = $_POST['identifiant_fiscal'];
    $logo = $_POST['logo'];
    $pseudo = $_POST['pseudo'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("INSERT INTO associations (nom, prenom, email, cin, pseudo, mot_de_passe, nom_association, adresse, identifiant_fiscal, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nom, $prenom, $email, $cin, $pseudo, $password, $nom_association, $adresse, $identifiant_fiscal, $logo]);

    header("Location: login.html");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>