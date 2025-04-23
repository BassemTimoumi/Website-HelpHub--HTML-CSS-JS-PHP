<?php
 mysql_connect("localhost","root","");
 mysql_select_db("helphub");


   
   
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



    mysql_query("insert into associations (nom, prenom, email, cin, pseudo, mot_de_passe,nom_association,adresse,identifiant_fiscal,logo) values('$nom','$prenom','$email','$cin','$pseudo','$password','$nom_association','$adresse','$identifiant_fiscal','$logo');");
   

   
    header("Location: login.html");
?>
