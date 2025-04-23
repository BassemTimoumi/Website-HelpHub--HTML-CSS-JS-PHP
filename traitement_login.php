<?php
mysql_connect("localhost","root","");
mysql_select_db("helphub");

session_start();


    $pseudo = $_POST['pseudo'];
    $password = $_POST['password'];
    $type = $_POST['type_utilisateur'];

    if ($type == 'association') {
        $res1=mysql_query("select * from associations where pseudo = '$pseudo'");
    } else {
        $res1=mysql_query("select * from donateurs where pseudo = '$pseudo'");
    }

    $user = mysql_fetch_assoc($res1);

    if ($user) {
        if ( $password === $user['mot_de_passe']) {
            $_SESSION['user'] = $user;
            $_SESSION['type'] = $type;
            header("Location: dashboard_" . $type . ".php");
        } else {
            echo "Mot de passe invalide !";
        }
    } else {
        echo "Utilisateur non trouvé !";
    }

?>