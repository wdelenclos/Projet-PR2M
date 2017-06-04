<?php
$URL = 'http://localhost/Projet.pr2m/dist/';
//--------------------------------------------------------------
require('config.php');// Recupération de la configuration
//--------------------------------------------------------------


// Utilitaire -------------------------------------
function console_print( $data ) {

    if ( is_array( $data ) )
        $output = "<script>console.log( '" . implode( ',', $data) . "' );</script>";
    else
        $output = "<script>console.log( '" . $data . "' );</script>";

    echo $output;
}

// Rootage -------------------------------
function routage(){ // Moteur de rooting
    $title = "";
    $label = "";
    $route = $_GET['p'];
    switch ($route) {
        case "":
            $label ="login";
            $title = "Connectez-vous";
            break;

        case "login":
            $label ="login";
            $title = "Connectez-vous";
            break;

        case "dashboard":
            $label ="content_dashboard";
            $title = "Liste des patients";
            break;

        case "compte":
            $label ="content_profile";
            $title = "Profil du praticien";
            break;

        case "nouveau":
            $label ="content_new";
            $title = "Nouveau patient";
            break;

        case "details":
            $label ="content_details";
            $title = "Details du patient";
            break;

        case "liste":
            $label ="content_list";
            $title = "Nouveau patient";
            break;

        case "logout":
            $label ="logout";
            $title = "Déconnecté";
            break;

        case "introuvable":
            $label ="403";
            $title = "Identifiant introuvable";
            break;

        default:
            $label ="404";
            $title = "Erreur 404";
            break;
    }
    if($title == null){
        $title = "404";
        $label = "404";
    }
    $exit = array($label, $title);
    return $exit;
}



function root($label, $title){

    if($label == "logout" ){
        session_destroy();
        header('Location: index.php?p=login&logout=true' );
    }

    if($label == "content_dashboard" || $label == "content_profile"  || $label == "content_new" || $label == "content_list" || $label == "content_details" ){
        include_once 'template/head.php';
        include_once 'template/nav.php';
    }
    if((@include 'template/'.$label.'.php') === false){
        include_once 'template/404.php';
    }
    else{
        include_once 'template/'.$label.'.php';
        if($label !== "login" && $label !== "404"&& $label !== "403" ){
            include_once 'template/footer.php';
        }
    }
}


// ------------------------   Fonction de crud praticien et patients

function addPraticien($bdd){

    try {
        $sql = "INSERT INTO `praticien`
        (id, identifiant, nom , prenom, email, patients)
        VALUES
        ( null, :identifiant, :nom ,:prenom, :email, :patients)
        ";
        console_print($_POST['sign_Name']);
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':identifiant', crc32($_POST['sign_Email']));
        $stmt->bindValue(':nom', $_POST['sign_Name']);
        $stmt->bindValue(':prenom', $_POST['sign_FirstName']);
        $stmt->bindValue(':email', $_POST['sign_Email']);
        $arr = array('a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5);
        $stmt->bindValue(':patients', json_encode($arr));
        $stmt->execute();
    }
    catch (PDOException $e) {

        $e->getMessage();
    }
    header('Location: ../index.php?n=100&p=dashboard&identifiant='.crc32($_POST['sign_Email']) );
}

function searchPraticien($bdd)
{

    try {
        $sql = 'SELECT id,identifiant, nom, prenom, email, patients
        FROM `praticien`
        WHERE identifiant = :identifiant';
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':identifiant',$_POST['identifiant']);
        $stmt->execute();
        $row = $stmt->fetchObject();
        $ide = $row->identifiant;
        if($ide == 0 || $ide == null){
            $boolean = false;
        }
        else{
            $boolean = true;
        }

    } catch (PDOException $e) {

        $e->getMessage();
    }
    return $boolean;
}

function addPatient($bdd)
{

    try {
        $sql = "INSERT INTO `patients` (`nom`, `prenom`, `date_naissance`, `lateralite`, `niveau`, `commentaire`, `test_emme`, `test_evip`, `test_dra`, `test_la_denomination`, `test_vtnv`, `test_la_designation`, `questionnaire`,  `praticien`)
            VALUES
            (:nom, :prenom, :date_naissance, :lateralite, :niveau, :commentaire, '', '', '', '','{}', '',  '',  :identifiant)
            ";
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':nom', $_POST['nom']);
        $stmt->bindValue(':prenom', $_POST['prenom']);
        $stmt->bindValue(':date_naissance', $_POST['date']);
        $stmt->bindValue(':niveau', $_POST['level']);
        $stmt->bindValue(':lateralite', $_POST['lateralite']);
        $stmt->bindValue(':commentaire', $_POST['commentaire']);
        $stmt->bindValue(':identifiant', $_POST['identifiant']);
        $stmt->execute();

        $sql = 'SELECT id
        FROM `patients`
        WHERE nom = :nom';
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':nom',$_POST['nom']);
        $stmt->execute();
        $row = $stmt->fetchObject();
        $id = $row->id;
        header('Location: ../index.php?n=200&p=details&identifiant='.$_POST['identifiant'].'&id='.$id );
    }
    catch( PDOException $Exception ) {
            var_dump($Exception->getMessage());
    }
}

function searchOnePatient($bdd)
{

    try {
        $sql = 'SELECT *
        FROM `patients`
        WHERE id = :id';
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':id',$_GET['id']);
        $stmt->execute();
        $row = $stmt->fetchObject();

    } catch (PDOException $e) {

        $e->getMessage();
    }
    return $row;
}
function updatePatient($bdd)
{
    $sql = "INSERT INTO `user`
        (id, pseudo, pw , adresse_mail)
        VALUES
        ( null, :pseudo, :pw ,:adresse_mail)
        ";
    $stmt = $bdd->prepare( $sql );
    $stmt->bindValue( ':pseudo' , htmlspecialchars($_POST['pseudo']) );
    $stmt->bindValue( ':pw' ,  sha1($_POST['pw'] ));
    $stmt->bindValue( ':adresse_mail' , htmlspecialchars($_POST['adresse_mail']) );
    $stmt->execute();
    header('Location:login.html');
}



// Listing des patients


function listAllPatient($bdd)
{
    try {
        $sql = 'SELECT *
        FROM `patients`
        WHERE praticien = :identifiant';
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':identifiant',$_GET['identifiant']);
        $stmt->execute();

        while ($row = $stmt->fetchObject()) {

            // Tesst du nombre de tests effectués
            $done = 0;
            if($row->test_emme !== "" ){
                $done++;
            }
            if($row->test_evip  !== "" ){
                $done++;
            }
            if($row->test_dra  !== "" ){
                $done++;
            }
            if($row->test_la_denomination  !== "" ){
                $done++;
            }
            if($row->test_vtnv !== "{}" ){
                $done++;
            }
            if($row->test_la_designation  !== ""  ){
                $done++;
            }

            $advencement = $done * 16.7;

            switch ($done){
                case 0:
                    $actionBtn = "<button type=\"button\" class=\"btn btn-default btn-xs\">En attente</button>";
                    $status = 'info';
                    break;
                case 6:
                    $actionBtn = "<button type=\"button\" class=\"btn btn-success btn-xs\">Terminé</button>";
                    $status = 'green';
                    break;
                default:
                    $actionBtn = "<button type=\"button\" class=\"btn btn-warning btn-xs\">En cours</button>";
                    $status = 'orange';
                    break;
            }

            echo('
                    
                
                 <tr>
                                <td>' . $row->id . '</td>
                                <td>
                                    <a>' . $row->nom . ' ' . $row->prenom . '</a>
                                    <br />
                                    <small>' . $row->date_naissance . '</small>
                                </td>
                                <td>
                                    <a>' . $row->niveau . ' - ' . $row->lateralite . '</a>
                                    <br />
                                    <small>' . $row->commentaire . '</small>
                                </td>
                                <td class="project_progress">
                                    <div class="progress progress_sm">
                                        <div class="progress-bar bg-'.$status.'" role="progressbar" data-transitiongoal="'.$advencement.'"></div>
                                    </div>
                                    <small>'.$done.'/6 tests effectués</small>
                                </td>
                                <td>
                                    
                                    '.$actionBtn.'
                                </td>
                                <td>
                                    <a href="?p=details&identifiant='.$_SESSION['identifiant'].'&id='.$row->id.'" class="btn btn-primary btn-xs"><i class="fa fa-chevron-right"></i> Voir </a>
                                    <a href="#" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> </a>
                                    <a href="#" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
                                </td>
                            </tr>
                
                ');


        };
        $stmt = null;


    } catch (PDOException $e) {

        echo $e->getMessage();
    }
}


function listWaitingPatient($bdd)
{
    try {
        $sql = 'SELECT *
        FROM `patients`
        WHERE praticien = :identifiant';
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':identifiant',$_GET['identifiant']);
        $stmt->execute();

        while ($row = $stmt->fetchObject()) {

            // Tesst du nombre de tests effectués
            $done = 0;
            if($row->test_emme !== "" ){
                $done++;
            }
            if($row->test_evip  !== "" ){
                $done++;
            }
            if($row->test_dra  !== "" ){
                $done++;
            }
            if($row->test_la_denomination  !== "" ){
                $done++;
            }
            if($row->test_vtnv !== "{}" ){
                $done++;
            }
            if($row->test_la_designation  !== ""  ){
                $done++;
            }

            if ($done !== 6){
                $advencement = $done * 16.7;

                switch ($done){
                    case 0:
                        $actionBtn = "<button type=\"button\" class=\"btn btn-default btn-xs\">En attente</button>";
                        $status = 'info';
                        break;
                    case 6:
                        $actionBtn = "<button type=\"button\" class=\"btn btn-success btn-xs\">Terminé</button>";
                        $status = 'green';
                        break;
                    default:
                        $actionBtn = "<button type=\"button\" class=\"btn btn-warning btn-xs\">En cours</button>";
                        $status = 'orange';
                        break;
                }

                echo('
                        
                    
                     <tr>
                                    <td>' . $row->id . '</td>
                                    <td>
                                        <a>' . $row->nom . ' ' . $row->prenom . '</a>
                                        <br />
                                        <small>' . $row->date_naissance . '</small>
                                    </td>
                                    <td>
                                        <a>' . $row->niveau . ' - ' . $row->lateralite . '</a>
                                        <br />
                                        <small>' . $row->commentaire . '</small>
                                    </td>
                                    <td class="project_progress">
                                        <div class="progress progress_sm">
                                            <div class="progress-bar bg-'.$status.'" role="progressbar" data-transitiongoal="'.$advencement.'"></div>
                                        </div>
                                        <small>'.$done.'/6 tests effectués</small>
                                    </td>
                                    <td>
                                        
                                        '.$actionBtn.'
                                    </td>
                                    <td>
                                        <a href="?p=details&identifiant='.$_SESSION['identifiant'].'&id='.$row->id.'" class="btn btn-primary btn-xs"><i class="fa fa-chevron-right"></i> Voir </a>
                                        <a href="#" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> </a>
                                        <a href="#" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i></a>
                                    </td>
                                </tr>
                    
                    ');
            }


        };
        $stmt = null;


    } catch (PDOException $e) {

        echo $e->getMessage();
    }
}



function countPatient($bdd)
{
    try {
        $sql = 'SELECT *
        FROM `patients`
        WHERE praticien = :identifiant';
        $stmt = $bdd->prepare($sql);
        $stmt->bindValue(':identifiant',$_GET['identifiant']);
        $stmt->execute();
        $patientsInfo = [];
        $patientsInfo[0] = 0; // Patients en cours
        $patientsInfo[1] = 0; // Patients en attente
        $patientsInfo[2] = 0; // Patients Terminés
        $patientsInfo[3] = 0; // Patients total
        while ($row = $stmt->fetchObject()) {




            // Tesst du nombre de tests effectués
            $done = 0;
            if($row->test_emme !== "" ){
                $done++;
            }
            if($row->test_evip  !== "" ){
                $done++;
            }
            if($row->test_dra  !== "" ){
                $done++;
            }
            if($row->test_la_denomination  !== "" ){
                $done++;
            }
            if($row->test_vtnv !== "{}" ){
                $done++;
            }
            if($row->test_la_designation  !== ""  ){
                $done++;
            }

            $advencement = $done * 16.7;

            if($done == 6){
                $patientsInfo[2]++;

            }
            if($done == 0){
                $patientsInfo[1]++;

            }
            if($done !== 0 && $done !== 6){
                $patientsInfo[0]++;

            }
            $patientsInfo[3]++;





        };
        $stmt = null;


    } catch (PDOException $e) {

        echo $e->getMessage();
    }
    return $patientsInfo;
}
