<?php
session_start();

// Database connection
mysql_connect("localhost", "root", "") or die(mysql_error());
mysql_select_db("helphub") or die(mysql_error());

$user = $_SESSION['user'];
$success_message = '';
$error_message = '';

// Process profile update if submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    // Get and escape form data
    $pseudo = mysql_real_escape_string($_POST['pseudo']);
    $email = mysql_real_escape_string($_POST['email']);
    $adresse = mysql_real_escape_string($_POST['adresse']);
    $nom_association = mysql_real_escape_string($_POST['nom_association']);
    $cin = mysql_real_escape_string($_POST['cin']);
    $identifiant_fiscal = mysql_real_escape_string($_POST['identifiant_fiscal']);
    $id = $user['id'];

    // Start building the query
    $query = "UPDATE associations SET 
              pseudo = '$pseudo', 
              email = '$email', 
              adresse = '$adresse', 
              nom_association = '$nom_association', 
              cin = '$cin', 
              identifiant_fiscal = '$identifiant_fiscal'";

    // Handle password change if provided
    if (!empty($_POST['mot_de_passe'])) {
        $hashed_password = mysql_real_escape_string($_POST['mot_de_passe']);
        $query .= ", mot_de_passe = '$hashed_password'";
    }

    $query .= " WHERE id = $id";

    // Execute the update
    if (mysql_query($query)) {
        // Refresh user data
        $result = mysql_query("SELECT * FROM associations WHERE id = $id");
        $_SESSION['user'] = mysql_fetch_assoc($result);
        $user = $_SESSION['user'];
        
        $success_message = "Profil mis à jour avec succès!";
    } else {
        $error_message = "Erreur lors de la mise à jour: " . mysql_error();
    }
}

// Process project addition if submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['titre'])) {
    $titre = mysql_real_escape_string($_POST['titre']);
    $description = mysql_real_escape_string($_POST['description']);
    $date_limite = mysql_real_escape_string($_POST['date_limite']);
    $montant = floatval($_POST['montant_total_a_collecter']);
    $id_responsable = $user['id'];

    $query = "INSERT INTO projet 
              (titre, description, date_limite, montant_total_a_collecter, montant_total_collecte, id_responsable_association) 
              VALUES ('$titre', '$description', '$date_limite', $montant, 0, $id_responsable)";
    
    if (mysql_query($query)) {
        $success_message = "Projet ajouté avec succès!";
    } else {
        $error_message = "Erreur lors de l'ajout du projet: " . mysql_error();
    }
}

// Process project deletion if requested
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_project'])) {
    $project_id = (int)$_POST['delete_project'];
    
    // Verify project has 0 collected funds before deleting
    $check_query = "SELECT montant_total_collecte FROM projet WHERE id_projet = $project_id";
    $result = mysql_query($check_query);
    
    if ($result && $row = mysql_fetch_assoc($result)) {
        if ($row['montant_total_collecte'] == 0) {
            $delete_query = "DELETE FROM projet WHERE id_projet = $project_id";
            if (mysql_query($delete_query)) {
                $success_message = "Projet supprimé avec succès!";
                // Refresh projects list
                header("Location: dashboard_association.php");
                exit();
            } else {
                $error_message = "Erreur lors de la suppression: " . mysql_error();
            }
        } else {
            $error_message = "Impossible de supprimer un projet avec des dons collectés";
        }
    } else {
        $error_message = "Projet introuvable";
    }
}

// Get projects for this association with donor information
$projects = array();
$result = mysql_query("SELECT * FROM projet WHERE id_responsable_association = {$user['id']}");
while ($project = mysql_fetch_assoc($result)) {
    // Get donors for this project
    $donors = array();
    $donor_query = mysql_query("SELECT d.*, dp.montant_participation, dp.date_participation 
        FROM donateur_projet dp
        JOIN donateurs d ON dp.id_donateur = d.id
        WHERE dp.id_projet = {$project['id_projet']}
    ");
    
    while ($donor = mysql_fetch_assoc($donor_query)) {
        $donors[] = $donor;
    }
    
    $project['donors'] = $donors;
    $projects[] = $project;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Association</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-tabs .nav-link.active {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .error-message {
            color: red;
            margin-bottom: 15px;
        }
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        .donor-details {
            background-color: #f8f9fa;
            border-radius: 5px;
            padding: 10px;
            margin-top: 10px;
        }
        .donor-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #dee2e6;
        }
        .donor-row:last-child {
            border-bottom: none;
        }
        .toggle-donors {
            cursor: pointer;
            color: #0d6efd;
            text-decoration: underline;
        }
        .badge-donors {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Bienvenue, <?php echo htmlspecialchars($user['pseudo']); ?></h1>
            <a href="logout.php" class="btn btn-danger">Déconnexion</a>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <ul class="nav nav-tabs" id="dashboardTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button">Mes Projets</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button">Mon Profil</button>
            </li>
        </ul>

        <div class="tab-content border-top-0 border p-3" id="dashboardTabsContent">
            <!-- Projects Tab -->
            <div class="tab-pane fade show active" id="projects" role="tabpanel">
                <h4 class="mt-2 mb-4">Ajouter un nouveau projet</h4>
                <form method="post" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="titre" class="form-label">Titre du projet</label>
                                <input type="text" class="form-control" id="titre" name="titre" required>
                            </div>
                            <div class="mb-3">
                                <label for="date_limite" class="form-label">Date limite</label>
                                <input type="date" class="form-control" id="date_limite" name="date_limite" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="montant_total_a_collecter" class="form-label">Montant à collecter (TND)</label>
                                <input type="number" class="form-control" id="montant_total_a_collecter" name="montant_total_a_collecter" step="0.01" required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>

                <h4 class="mt-5">Vos projets existants</h4>
                <?php if (count($projects) > 0): ?>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Titre</th>
                                <th>Description</th>
                                <th>Date limite</th>
                                <th>Montant visé</th>
                                <th>Montant collecté</th>
                                <th>Dons</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($projects as $project): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($project['titre']); ?></td>
                                <td><?php echo htmlspecialchars($project['description']); ?></td>
                                <td><?php echo htmlspecialchars($project['date_limite']); ?></td>
                                <td><?php echo number_format($project['montant_total_a_collecter'], 2); ?> TND</td>
                                <td><?php echo number_format($project['montant_total_collecte'], 2); ?> TND</td>
                                <td>
                                    <span class="badge bg-primary badge-donors" 
                                          onclick="toggleDonors(<?php echo $project['id_projet']; ?>)">
                                        <?php echo count($project['donors']); ?> donateurs
                                    </span>
                                </td>
                                <td>
                                    <?php if ($project['montant_total_collecte'] == 0): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="delete_project" value="<?php echo $project['id_projet']; ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce projet?');">Supprimer</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm" disabled>Supprimer</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr id="donors-<?php echo $project['id_projet']; ?>" style="display:none;">
                                <td colspan="7">
                                    <div class="donor-details">
                                        <h5>Détails des donateurs</h5>
                                        <?php if (count($project['donors']) > 0): ?>
                                            <?php foreach ($project['donors'] as $donor): ?>
                                                <div class="donor-row">
                                                    <span><?php echo htmlspecialchars($donor['nom'] . ' ' . $donor['prenom']); ?></span>
                                                    <span><?php echo number_format($donor['montant_participation'], 2); ?> TND</span>
                                                    <span><?php echo date('d/m/Y H:i', strtotime($donor['date_participation'])); ?></span>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p>Aucun donateur pour ce projet</p>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-info mt-3">Aucun projet trouvé</div>
                <?php endif; ?>
            </div>

            <!-- Profile Tab -->
            <div class="tab-pane fade" id="profile" role="tabpanel">
                <h4 class="mt-2 mb-4">Mon Profil</h4>
                
                <form method="post" action="dashboard_association.php" enctype="multipart/form-data" onsubmit="return verif2();">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pseudo" class="form-label">Pseudo</label>
                                <input type="text" class="form-control" id="pseudo" name="pseudo" 
                                       value="<?php echo htmlspecialchars($user['pseudo']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="adresse" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="adresse" name="adresse" 
                                       value="<?php echo htmlspecialchars($user['adresse']); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="nom_association" class="form-label">Nom Association</label>
                                <input type="text" class="form-control" id="nom_association" name="nom_association" 
                                       value="<?php echo htmlspecialchars($user['nom_association']); ?>">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cin" class="form-label">CIN</label>
                                <input type="text" class="form-control" id="cin" name="cin" 
                                       value="<?php echo htmlspecialchars($user['cin']); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="identifiant_fiscal" class="form-label">Identifiant Fiscal</label>
                                <input type="text" class="form-control" id="identifiant_fiscal" name="identifiant_fiscal" 
                                       value="<?php echo htmlspecialchars($user['identifiant_fiscal']); ?>">
                            </div>
                            
                            <div class="mb-3">
                                <label for="mot_de_passe" class="form-label">Nouveau Mot de Passe (laisser vide pour ne pas changer)</label>
                                <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleDonors(projectId) {
            const donorRow = document.getElementById('donors-' + projectId);
            if (donorRow.style.display === 'none') {
                donorRow.style.display = 'table-row';
            } else {
                donorRow.style.display = 'none';
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>