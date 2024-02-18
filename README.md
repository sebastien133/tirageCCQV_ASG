# tirageCCQV_ASG

0 - Géocoder les adresses

https://adresse.data.gouv.fr/csv

1 - Ouvrir QGIS

Charger la carte des quartiers : Layer / Add a layer => Vectoriel layer
Charger les données des habitants : Layer / Add a layer => Text delimited layer

=> Bien charger le fichier en EPSG:4326 - WGS 84 sinon cela ne fonctionne pas...

2 - Vecteur / OUtil de gestion de base de données / Joindre les attributs par localisation

Sélectionner le tableau des habitants, puis joindre les datas des quartiers. Type de jointure un à un.