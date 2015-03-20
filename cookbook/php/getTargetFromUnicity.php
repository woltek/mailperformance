<?php

//Ici, renseignez l'email dont vous voulez obtenir les valeurs des champs
$unicity = 'test@test.com';

//Utilisation de cURL pour remplir la requ�te
$req = curl_init();
curl_setopt($req,CURLOPT_URL,'http://v8.mailperformance.com/targets?unicity='.$unicity);
curl_setopt($req,CURLOPT_CUSTOMREQUEST,'GET');
curl_setopt($req, CURLOPT_RETURNTRANSFER, true);

//Mise en place du xKey et des options
curl_setopt($req, CURLOPT_HTTPHEADER, array(
'Content-Type: application/json',
'X-Key: ABCD1234ABCDEFGHIJKLMNOPQRSTUV'));

//Execution de la requ�te
$result = curl_exec($req);

//On ecrit le resultat dans la fenetre
echo $result;

curl_close($req);

