<html>
<head>
    <title>Tirage au sort du 28 avril 2022 - Conseil Consultatif de Quartiers et de Villages</title>
</head>
<body>
<h1>Tirage au sort du 28 avril 2022 - Conseil Consultatif de Quartiers et de Villages</h1>
<form method="post">
    <input type="hidden" name="tirage" value="1" />
    <p>Nombre de personnes tirée au sort : <input type="text" name="qte" value="100" /></p>
    <p><input type="checkbox" name="quartier" value="1" checked /> Répartition par quartier des personnes sélectionnées</p>
    <p><input type="checkbox" name="parite" value="1" /> Parité des personnes sélectionnées ?</p>
    <p><input type="checkbox" name="age" value="1" /> Répartition par âge (par dizaine d'année) des personnes sélectionnées ?</p>
    <p><input type="submit" value="Lancer le tirage" /></p>
</form>
<?php
if (isset($_POST["tirage"]))
    echo "<a href=\"resultat.csv\">Télécharger le résultat</a>";
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
    $compteParite = [0, 0];
    $compteAge = ["- de 30 ans" => 0, "- de 40 ans" => 0, "- de 50 ans" => 0, "- de 60 ans" => 0, "- de 70 ans" => 0, "+ de 70 ans" => 0];

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
    echo "<p>$lineNumber lignes de données en entrée.</p>";

    // Effectuer une sélection aléatoire
    $totalLu=0;
    while (condition() ) {
        $line = $data[mt_rand(0, count($data) - 1)];

        // Remplacer par MATCH sur un binaire ?
        if ($_POST["quartier"]) {
            if ($compteQuartier[array_search($line["quartier"], $listeQuartiers)] < 100) {
                $compteQuartier[array_search($line["quartier"], $listeQuartiers)]++;
                $result[] = $line;
            }
        } else {
            $result[] = $line;
        }

        $totalLu++;
    }
    var_dump($result[1]);

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
    global $_POST, $listeQuartiers, $compteQuartier, $totalLu;

    if (!$_POST["quartier"] && !$_POST["parite"] && !$_POST["age"])
        return $totalLu < $_POST["qte"];

    if ($_POST["quartier"]) {
        return array_sum($compteQuartier) < ($_POST["qte"] * count($listeQuartiers));
    }

    return false;
}
