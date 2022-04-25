<html>
<head>
    <title>Tirage au sort - Conseil Consultatif de Quartiers et de Villages du 28 avril 2022</title>
</head>
<body>
<h1>Tirage au sort - Conseil Consultatif de Quartiers et de Villages du 28 avril 2022</h1>
<form method="post">
    <input type="hidden" name="tirage" value="1" />
    <input type="submit" value="Lancer le tirage" />
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
    for ($i=0; $i<100; $i++) {
        $result[] = $data[mt_rand(0, count($data) - 1)];
    }
    var_dump($result[1]);

    // Enregistrer dans le fichier resultat.csv
    $fp = fopen('results/resultat.csv', 'w');
    if ($fp === false)
        die();

    // Ligne de titre
    fputcsv($fp, ["Civilite", "NOM","Nom_usage","Prenoms","date_nais","num","voie","bat","app","compl_adresse","code_postal","ville","Quartier"]);

    foreach($result as $line)
    {
        fputcsv($fp, $line);
    }

    fclose($fp);
}
