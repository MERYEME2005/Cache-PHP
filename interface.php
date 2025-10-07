<?php
        include 'cache.php';
        if (isset($_POST['delete_key'])) {
            $key = $_POST['delete_key'];
            Cache::delete($key); 
            header("Location: " . $_SERVER['PHP_SELF']); // recharger la page
            exit;
        }
        if(isset($_POST['add_key'])){
        $key = $_POST['new_key'];
        $value = $_POST['new_value'];
        $ttl = (int)$_POST['new_ttl'];
        Cache::set($key, $value, $ttl);
        header("Location: ".$_SERVER['PHP_SELF']); // recharge la page pour voir le tableau mis à jour
        exit;
       }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cache Table</title>
    <style>
        body {
            font-family: Arial;
            margin: 20px;
            background-color: #fafafa;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: orange;
            color: white;
        }
        h1 {
            text-align: center;
            color: #333;
        }
    </style>
</head>
<body>
    <h2>Ajouter une nouvelle clé</h2>
    <form method="POST" onsubmit="return confirm('Ajouter cette clé ?');">
        <label>Clé: <input type="text" name="new_key" required></label>
        <label>Valeur: <input type="text" name="new_value" required></label>
        <label>Durée de vie (secondes): <input type="number" name="new_ttl" value="3600" required></label>
        <button type="submit" name="add_key">Ajouter</button>
    </form>
    <hr>
    <h1>Contenu du cache</h1>
    <table>
        <tr>
            <th>Clé</th>
            <th>Valeur</th>
            <th>Expiration</th>
            <th>Actions</th>
        </tr>
        <?php
        $file = "cache.json";

        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            foreach ($data as $key => $entry) {
                echo "<tr>
                        <td>" . htmlspecialchars($key) . "</td>
                        <td>" . htmlspecialchars($entry['value']) . "</td>
                        <td>" . date('Y-m-d H:i:s', $entry['expires']) . "</td>
                        <td>
                            <form method='POST' action='' onsubmit='return confirm(\"Supprimer cette clé ?\");'>
                                <input type='hidden' name='delete_key' value='" . htmlspecialchars($key) . "'>
                                <button type='submit' style='background:red; color:white; border:none; padding:5px 10px; cursor:pointer; border-radius:5px;'>
                                    Supprimer
                                </button>
                            </form>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>Aucune donnée trouvée</td></tr>";
        }
        ?>
    </table>
    
</body>
</html>
