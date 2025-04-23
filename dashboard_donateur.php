<?php
session_start();

// Database connection
mysql_connect("localhost", "root", "") or die(mysql_error());
mysql_select_db("helphub") or die(mysql_error());

// Check if donor is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$donateur = $_SESSION['user'];
$success_message = '';
$error_message = '';
$search_keyword = '';

// Get search keyword if submitted
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['search'])) {
    $search_keyword = mysql_real_escape_string($_GET['search_keyword']);
}

// Process donation if submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['donate'])) {
    $project_id = (int)$_POST['project_id'];
    $amount = (float)$_POST['amount'];
    $donor_id = $donateur['id'];
    
    // Verify project is still active
    $project_query = "SELECT * FROM projet 
                     WHERE id_projet = $project_id 
                     AND date_limite >= CURDATE() 
                     AND montant_total_collecte < montant_total_a_collecter";
    $project_result = mysql_query($project_query);
    
    if ($project_result && $project = mysql_fetch_assoc($project_result)) {
        $remaining = $project['montant_total_a_collecter'] - $project['montant_total_collecte'];
        
        // Validate amount
        if ($amount > 0 && $amount <= $remaining) {
            // Start transaction
            mysql_query("START TRANSACTION");
            
            // 1. Record the donation
            $insert_query = "INSERT INTO donateur_projet 
                           (id_projet, id_donateur, montant_participation, date_participation) 
                           VALUES ($project_id, $donor_id, $amount, NOW())";
            
            // 2. Update project collected amount
            $update_query = "UPDATE projet 
                           SET montant_total_collecte = montant_total_collecte + $amount 
                           WHERE id_projet = $project_id";
            
            if (mysql_query($insert_query) && mysql_query($update_query)) {
                mysql_query("COMMIT");
                $success_message = "Merci pour votre don de " . number_format($amount, 2) . " TND!";
            } else {
                mysql_query("ROLLBACK");
                $error_message = "Erreur lors du traitement de votre don";
            }
        } else {
            $error_message = "Montant invalide. Le montant restant à collecter est " . number_format($remaining, 2) . " TND";
        }
    } else {
        $error_message = "Projet non disponible pour donation";
    }
}

// Build base query for active projects
$base_query = "SELECT * FROM projet 
              WHERE date_limite >= CURDATE() 
              AND montant_total_collecte < montant_total_a_collecter";

// Add search condition if keyword exists
if (!empty($search_keyword)) {
    $base_query .= " AND description LIKE '%$search_keyword%'";
}

// Complete query with sorting
$query = $base_query . " ORDER BY date_limite ASC";

// Get active projects
$projects = array();
$result = mysql_query($query);
while ($row = mysql_fetch_assoc($result)) {
    $projects[] = $row;
}

// Get donor's donation history
$donation_history = array();
$history_query = "SELECT p.titre, p.description, SUM(dp.montant_participation) as total_don, 
                 COUNT(dp.id) as nombre_dons 
                 FROM donateur_projet dp
                 JOIN projet p ON dp.id_projet = p.id_projet
                 WHERE dp.id_donateur = {$donateur['id']}
                 GROUP BY p.id_projet";
$history_result = mysql_query($history_query);
while ($row = mysql_fetch_assoc($history_result)) {
    $donation_history[] = $row;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord Donateur</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .project-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .progress {
            height: 25px;
            margin-bottom: 15px;
        }
        .progress-bar {
            background-color: #28a745;
        }
        .history-card {
            background-color: #f8f9fa;
            border-left: 4px solid #6c757d;
        }
        .nav-tabs .nav-link.active {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .search-box {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Bienvenue, <?php echo htmlspecialchars($donateur['prenom'] . ' ' . $donateur['nom']); ?></h1>
            <a href="logout.php" class="btn btn-danger">Déconnexion</a>
        </div>

        <?php if (!empty($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <ul class="nav nav-tabs" id="donorTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="projects-tab" data-bs-toggle="tab" data-bs-target="#projects" type="button">Projets Actifs</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button">Mes Dons</button>
            </li>
        </ul>

        <div class="tab-content border-top-0 border p-3" id="donorTabsContent">
            <!-- Active Projects Tab -->
            <div class="tab-pane fade show active" id="projects" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Projets Actifs</h2>
                    <form method="get" class="search-box">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search_keyword" 
                                   placeholder="Rechercher dans les descriptions" 
                                   value="<?php echo htmlspecialchars($search_keyword); ?>">
                            <button class="btn btn-outline-secondary" type="submit" name="search">Rechercher</button>
                            <?php if (!empty($search_keyword)): ?>
                                <a href="dashboard_donateur.php" class="btn btn-outline-danger">Annuler</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
                
                <?php if (count($projects) > 0): ?>
                    <div class="row">
                        <?php foreach ($projects as $project): 
                            $progress = ($project['montant_total_collecte'] / $project['montant_total_a_collecter']) * 100;
                            $remaining = $project['montant_total_a_collecter'] - $project['montant_total_collecte'];
                        ?>
                        <div class="col-md-6">
                            <div class="project-card">
                                <h3><?php echo htmlspecialchars($project['titre']); ?></h3>
                                <p><?php echo htmlspecialchars($project['description']); ?></p>
                                <p><strong>Date limite:</strong> <?php echo htmlspecialchars($project['date_limite']); ?></p>
                                
                                <div class="progress">
                                    <div class="progress-bar" role="progressbar" 
                                         style="width: <?php echo $progress; ?>%" 
                                         aria-valuenow="<?php echo $progress; ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?php echo number_format($progress, 1); ?>%
                                    </div>
                                </div>
                                
                                <p>
                                    <strong>Collecté:</strong> <?php echo number_format($project['montant_total_collecte'], 2); ?> TND / 
                                    <strong>Objectif:</strong> <?php echo number_format($project['montant_total_a_collecter'], 2); ?> TND
                                    <br>
                                    <strong>Reste à collecter:</strong> <?php echo number_format($remaining, 2); ?> TND
                                </p>
                                
                                <form method="post" class="mt-3">
                                    <input type="hidden" name="project_id" value="<?php echo $project['id_projet']; ?>">
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" name="amount" 
                                               min="1" max="<?php echo $remaining; ?>" 
                                               step="0.01" placeholder="Montant (TND)" required>
                                        <button class="btn btn-success" type="submit" name="donate">Faire un don</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        <?php if (!empty($search_keyword)): ?>
                            Aucun projet actif trouvé avec le mot-clé "<?php echo htmlspecialchars($search_keyword); ?>"
                        <?php else: ?>
                            Aucun projet actif disponible pour le moment
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Donation History Tab -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <h2 class="mb-4">Historique de Mes Dons</h2>
                
                <?php if (count($donation_history) > 0): ?>
                    <div class="row">
                        <?php foreach ($donation_history as $donation): ?>
                        <div class="col-md-6">
                            <div class="project-card history-card">
                                <h3><?php echo htmlspecialchars($donation['titre']); ?></h3>
                                <p><?php echo htmlspecialchars($donation['description']); ?></p>
                                <p class="fw-bold">Total donné: <?php echo number_format($donation['total_don'], 2); ?> TND</p>
                                <p>Nombre de contributions: <?php echo $donation['nombre_dons']; ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Vous n'avez pas encore effectué de dons</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>