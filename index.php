<html>
<head>
    <title>Tirage au sort du 28 avril 2022 - Conseil Consultatif de Quartiers et de Villages</title>
    <style>
        .center {
            margin-left: auto;
            margin-right: auto;
        }
        table td {
            text-align: center;
        }
    </style>
</head>
<body>
<h1>Tirage au sort du 28 avril 2022 - Conseil Consultatif de Quartiers et de Villages</h1>
<form method="post">
    <input type="hidden" name="tirage" value="1" />
    <p>Nombre de personnes tirée au sort : <input type="text" name="qte" value="100" /></p>
    <p><input id="quartier" type="checkbox" name="quartier" value="1"<?php if (isset($_POST["quartier"])) echo " checked"; ?> /> <label for="quartier">Répartition par quartier (nombre de personne total multipliée par le nombre de quartiers)</label></p>
    <p><input id="parite" type="checkbox" name="parite" value="1"<?php if (isset($_POST["parite"])) echo " checked"; ?> /> <label for="parite">Parité ?</label></p>
    <p><input id="age" type="checkbox" name="age" value="1"<?php if (isset($_POST["age"])) echo " checked"; ?> /> <label for="age">Répartition par classe d'âge ?</label></p>
    <p><input type="submit" value="Lancer le tirage" /></p>
</form>
<?php
if (isset($_POST["tirage"]))
    echo "<a href=\"/results/resultat.csv\">Télécharger le résultat</a>";
?>
</body>
</html>
<?php
ini_set('display_errors', '1');

if (isset($_POST["tirage"])) {
    $data = [];
    $result = [];
    $listeQuartiers = ["Quartier Montaigne", "Quartier Saint-Géréon", "Quartier Hopital", "Quartier Nord", "Quartier Coeur de ville", "Villages"];
    $compteQuartier = [0, 0, 0, 0, 0, 0];
    $compteParite = ['m' => 0, 'f' => 0,];
    $compteAge = ["- de 30 ans" => 0, "- de 40 ans" => 0, "- de 50 ans" => 0, "- de 60 ans" => 0, "- de 70 ans" => 0, "+ de 70 ans" => 0];

    $comptePariteQuartier = [];
    $compteAgeQuartier = [];
    for ($i=0; $i<count($listeQuartiers); $i++) {
        $comptePariteQuartier[] = $compteParite;
        $compteAgeQuartier[] = $compteAge;
    }

    // Lire le fichier de données en entrées (liste des habitants)
    $file = fopen("tirageAuSort.csv", "r");
    $lineNumber = 0;
    while (($line = fgetcsv($file, null, ";")) !== FALSE) {
        if ($lineNumber++ === 0)
            continue;

        $ligne = array();
        $ligne["civilite"] = $line[0];
        $ligne["nom"] = $line[1];
        $ligne["nom_usage"] = $line[2];
        $ligne["prenoms"] = $line[3];
        $ligne["date_naiss"] = $line[4];
        $ligne["num"] = $line[5];
        $ligne["voie"] = $line[6];
        $ligne["bat"] = $line[7];
        $ligne["app"] = $line[8];
        $ligne["compl_adr"] = $line[9];
        $ligne["cp"] = $line[10];
        $ligne["ville"] = $line[11];
        $ligne["quartier"] = $line[12];
        $data[] = $ligne;
    }
    fclose($file);
    echo "<p>" . count($data) . " lignes de données en entrée.</p>";

    // Effectuer une sélection aléatoire
    $totalAjoute=0;
    while (condition() ) {
        $resultatTirage = mt_rand(0, count($data) - 1);

        // Evite les doublons
        if (!isset($data[$resultatTirage]))
            continue;

        $line = $data[$resultatTirage];

        // Remplacer par MATCH sur un binaire ?
        if (isset($_POST["quartier"])) {
            if ($compteQuartier[array_search($line["quartier"], $listeQuartiers)] < $_POST['qte']) {
                $quartier = array_search($line["quartier"], $listeQuartiers);

                // Vérifie le critère de parité si sélectionné
                if (isset($_POST['parite']) &&
                    $comptePariteQuartier[$quartier][getSexe($line)] >= ($_POST['qte'] / count($compteParite))
                )
                    continue;

                // Vérifie le critère de classe d'âge si sélectionné
                if (isset($_POST['age']) &&
                    $compteAgeQuartier[$quartier][getClasseAge($line)] >= ($_POST['qte'] / count($compteAge))
                )
                    continue;

                $compteQuartier[$quartier]++;
                $comptePariteQuartier[$quartier][getSexe($line)]++;
                $compteAgeQuartier[$quartier][getClasseAge($line)]++;
                ajouterResultat($resultatTirage);
            }
        } else {
            // Vérifie le critère de parité si sélectionné
            if (isset($_POST['parite']) && $compteParite[getSexe($line)] >= ($_POST['qte'] / count($compteParite)))
                continue;

            // Vérifie le critère de classe d'âge si sélectionné
            if (isset($_POST['age']) && $compteAgeQuartier[getClasseAge($line)] >= ($_POST['qte'] / count($compteAge)))
                continue;

            ajouterResultat($resultatTirage);
        }
    }

    echo "<p>" . count($data) . " lignes de données non-sélectionnées.</p>";

    if (isset($_POST['quartier'])) {
        echo '<table border="1" class="center"><tr><th>Quartier</th>';
        echo '<th>Nbre de personne</th>';
        echo '<th>Homme</th>';
        echo '<th>Femme</th>';

        $ages = array_keys($compteAge);
        for ($i=0; $i<count($compteAge); $i++) {
            echo "<th>" . $ages[$i] . "</th>";
        }

        echo "</tr>";

        for ($i=0; $i<count($compteQuartier); $i++) {
            echo "<tr><td>" . $listeQuartiers[$i] . "</td>";
            echo "<td>" . $compteQuartier[$i] . "</td>";
            echo "<td>" . $comptePariteQuartier[$i]['m'] . "</td>";
            echo "<td>" . $comptePariteQuartier[$i]['f'] . "</td>";

            for ($j=0; $j<count($compteAge); $j++) {
                echo "<td>" . $compteAgeQuartier[$i][$ages[$j]] . "</td>";
            }

            echo "</tr>";
        }
        echo '</table>';
    }

    echo "<pre>Première ligne : "; var_dump($result[1]); echo "</pre>";

    // Enregistrer dans le fichier resultat.csv
    $fp = fopen('results/resultat.csv', 'w');
    if ($fp === false)
        die();

    // Ligne de titre
    fputcsv($fp, ["Civilite", "NOM","Nom_usage","Prenoms","date_nais","num","voie","bat","app","compl_adresse","code_postal","ville","Quartier"]);

    foreach($result as $line) {
        fputcsv($fp, $line);
    }

    fclose($fp);
}

function condition() {
    global $_POST, $listeQuartiers, $compteQuartier, $totalAjoute;

    if (!isset($_POST["quartier"]) && !isset($_POST["parite"]) && !isset($_POST["age"]))
        return $totalAjoute < $_POST["qte"];

    if (isset($_POST["quartier"])) {
        return array_sum($compteQuartier) < ($_POST["qte"] * count($listeQuartiers));
    }

    return false;
}

function ajouterResultat($resultatTirage) {
    global $data, $result, $totalAjoute;

    $result[] = $data[$resultatTirage];
    unset($data[$resultatTirage]);

    $totalAjoute++;
}

function getSexe($line) {
    return $line['civilite'] === 'M.'?'m':'f';
}

function getClasseAge($line) {
    $now = date_create(date("Y-m-d"));
    $date_naiss = strlen($line['date_naiss']) === 4 ? date_create_from_format("d/m/Y", "01/01/" . $line['date_naiss']) : date_create_from_format("d/m/Y", $line['date_naiss']);
    $age = date_diff($date_naiss, $now)->format('%y');
    return match(true) {
        $age < 30 => '- de 30 ans',
        $age < 40 => '- de 40 ans',
        $age < 50 => '- de 50 ans',
        $age < 60 => '- de 60 ans',
        $age < 70 => '- de 70 ans',
        default => '+ de 70 ans',
    };
}