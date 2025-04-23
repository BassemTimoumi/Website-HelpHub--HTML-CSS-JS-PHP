<?php
mysql_connect("localhost","root","");
mysql_select_db("helphub");


   
   
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $cin = $_POST['cin'];
    $email = $_POST['email'];
    $pseudo = $_POST['pseudo'];
    $password = $_POST['password'];



    mysql_query("insert into donateurs (nom, prenom, email, cin, pseudo, mot_de_passe) values('$nom','$prenom','$email','$cin','$pseudo','$password');");
   

   
    header("Location: login.html");
    


?>