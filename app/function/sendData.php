<?php
ini_set('display_errors', 1);
// Enregistrer les erreurs dans un fichier de log
ini_set('log_errors', 1);
// Nom du fichier qui enregistre les logs (attention aux droits à l'écriture)
ini_set('error_log', dirname(__file__) . '/log_error_php.txt');
// Afficher les erreurs et les avertissements
error_reporting(E_ALL & ~E_NOTICE);

require ('../config.php');
require ('../connect.php');
require ('../function.php');



if($_POST['p']=='0'){

	if($_GET['t']=='EMME'){

	}
	else if($_GET['t']=='EVIP'){
		var_dump($_POST);
	}
	else if($_GET['t']==''){

	}
	else if($_GET['t']==''){

	}
	else if($_GET['t']==''){

	}
	else if($_GET['t']==''){

	}
	else{

	}

}
else if($_GET['p']=='1'){

	if($_GET['t']==''){

	}
	else{
		header('Location: ../index.php?n=530&p=dashboard&identifiant='.$_POST['identifiant'] );
	}
}
else if($_GET['p']=='2'){
	if($_GET['t']==''){

	}
	else if($_GET['t']==''){

	}
	else if($_GET['t']==''){

	}
	else if($_GET['t']==''){

	}
	else if($_GET['t']==''){

	}
	else if($_GET['t']==''){

	}
	else{
		header('Location: ../index.php?n=520&p=dashboard&identifiant='.$_POST['identifiant'] );
	}


}
else {
	var_dump($_POST);
}