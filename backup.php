<?php
date_default_timezone_set('Europe/Paris');
$backuptask = 'sql/backup_'.date("Y-m-d").'.sql';
?>
<html>
<header>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>JCD54 - Stock backup</title>
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
</header>
<body style="min-height:100%;position:relative;">
<div style="min-height:25%;"></div>
<div class="text-center">
    <?php phpinfo(); ?>
    <h1><i class="fa fa-5x fa-spinner fa-pulse"></i><br><br>Sauvegarde en cours...</h1>
</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
</body>
</html>
<?php
function dumpMySQL($serveur, $login, $password, $base, $mode, $pDossier)
//{
//    $connexion = mysql_connect($serveur, $login, $password);
//    mysql_select_db($base, $connexion);
//
//    $entete = "-- ----------------------\n";
//    $entete .= "-- dump de la base ".$base." au ".date("d-M-Y")."\n";
//    $entete .= "-- ----------------------\n\n\n";
//    $creations = "";
//    $insertions = "\n\n";
//
//    $listeTables = mysql_query("show tables", $connexion);
//    while($table = mysql_fetch_array($listeTables))
//    {
//        // si l'utilisateur a demandé la structure ou la totale
//        if($mode == 1 || $mode == 3)
//        {
//            $creations .= "-- -----------------------------\n";
//            $creations .= "-- creation de la table ".$table[0]."\n";
//            $creations .= "-- -----------------------------\n";
//            $listeCreationsTables = mysql_query("show create table ".$table[0], $connexion);
//            while($creationTable = mysql_fetch_array($listeCreationsTables))
//            {
//                $creations .= $creationTable[1].";\n\n";
//            }
//        }
//        // si l'utilisateur a demandé les données ou la totale
//        if($mode > 1)
//        {
//            $donnees = mysql_query("SELECT * FROM ".$table[0]);
//            $insertions .= "-- -----------------------------\n";
//            $insertions .= "-- insertions dans la table ".$table[0]."\n";
//            $insertions .= "-- -----------------------------\n";
//            while($nuplet = mysql_fetch_array($donnees))
//            {
//                $insertions .= "INSERT INTO ".$table[0]." VALUES(";
//                for($i=0; $i < mysql_num_fields($donnees); $i++)
//                {
//                    if($i != 0)
//                        $insertions .=  ", ";
//                    if(mysql_field_type($donnees, $i) == "string" || mysql_field_type($donnees, $i) == "blob")
//                        $insertions .=  "'";
//                    $insertions .= addslashes($nuplet[$i]);
//                    if(mysql_field_type($donnees, $i) == "string" || mysql_field_type($donnees, $i) == "blob")
//                        $insertions .=  "'";
//                }
//                $insertions .=  ");\n";
//            }
//            $insertions .= "\n";
//        }
//    }
//
//
//    $fichierDump = fopen($pDossier, "w");
//    fwrite($fichierDump, $entete);
//    fwrite($fichierDump, $creations);
//    fwrite($fichierDump, $insertions);
//    fclose($fichierDump);

}
//dumpMySQL("db602115006.db.1and1.com", "dbo602115006", "Pu1yw-4X", "db602115006", 3, "$backuptask");
//if(file_exists($backuptask)) { ?>
<!--    <meta http-equiv="refresh" content="4; URL=index.php">-->
<?php //} ?>
