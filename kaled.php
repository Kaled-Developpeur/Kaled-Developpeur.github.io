 <?php

$serveur = "mysql-kaled.alwaysdata.net";
$login = "kaled";
$pass = "kaled269@outlook.fr";






$code=$_POST['code'];


if($code=='1'){

$image = $_POST["image"];
$kaled = './images';
$folder1 = $kaled . "/" . rand() . "_" . time() . ".png";

$nom = $_POST['nom'];
$nomPrenom = $_POST['nomPrenom'];
$password = $_POST['password'];

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);

    // Vérification de l'existence du mot de passe
    $requete = $connexion->prepare("SELECT password FROM inscription WHERE password = ?");
    $requete->execute([$password]);
    $resultat = $requete->fetchAll();

    if (!empty($resultat)) {
        echo 'Ce compte existe déjà. Veuillez saisir un autre mot de passe.';
    } else {
        // Vérification de l'existence du nom
        $requete = $connexion->prepare("SELECT nomPrenom FROM inscription WHERE nomPrenom = ?");
        $requete->execute([$nomPrenom]);
        $resultat = $requete->fetchAll();

        if (!empty($resultat)) {
            echo 'Ce nom existe déjà. Veuillez saisir un autre nom.';
        } else {
            // Enregistrement de l'image
            file_put_contents($folder1, base64_decode($image));
            $folder1 = substr($folder1, 1);

            // Insertion des données
            $insertion = "INSERT INTO inscription (nom, nomPrenom, password, image) VALUES (?, ?, ?, ?)";
            $stmt = $connexion->prepare($insertion);
            $stmt->execute([$nom, $nomPrenom, $password, $folder1]);

            echo 'kaled kaled kaled.';//inscription reussit
           
        }
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}







}else
if($code=='2'){

$nomPrenom = $_POST['nomPrenom'];
$password = $_POST['password'];

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);

    // Vérification de l'existence du mot de passe et récupération de nomPrenom
    $requete = $connexion->prepare("SELECT nomPrenom FROM inscription WHERE password = ?");
    $requete->execute([$password]);
    $resultat = $requete->fetch();

    if ($resultat) {
        $nomPrenomBDD = $resultat['nomPrenom'];

        if ($nomPrenomBDD == $nomPrenom) {
            $coupe = explode(" ", $nomPrenom);
            $nom = $coupe[0];
            $nom = "kaled_" . $nom;

            $connexion1 = new PDO("mysql:host=$serveur", $login, $pass);
            $requete = $connexion1->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME='$nom'");

            if ($requete->rowCount() > 0) {
                $connexion = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);
                $requete = $connexion->prepare("SELECT image FROM inscription WHERE nomPrenom= ?");
                $requete->execute([$nomPrenom]);
                $resultat = $requete->fetch();
                $nom = $resultat['image'];

                echo $nom;
            } else {
                $connexion = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);
                $requete = $connexion->prepare("SELECT image FROM inscription WHERE nomPrenom= ?");
                $requete->execute([$nomPrenom]);
                $resultat = $requete->fetch();
                $nom = 'kaled' . $resultat['image'];

                echo $nom;
            }
        } else {
            echo ''; // Faire quelque chose ici si le nomPrenom ne correspond pas au mot de passe
        }
    } else {
        echo ''; // Faire quelque chose ici si le mot de passe n'existe pas
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}








//Recuperation des publication
}else
if($code=='3'){
    
try {
    $connexion = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);

    $requete = $connexion->prepare("SELECT * FROM publication");
    $requete->execute();
    $resultat = $requete->fetchAll(PDO::FETCH_ASSOC);

    print(json_encode($resultat));
} catch (PDOException $e) {
    // Gérer les erreurs de connexion ou d'exécution de la requête ici
    echo "Erreur : " . $e->getMessage();
}



//Recuperation des publication Unitaire
}else
if($code=='303'){

$nomPrenom=$_POST['nomPrenom'];

$connexion = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);

$requete = $connexion->prepare("SELECT * FROM publication WHERE nomPrenom= '$nomPrenom' ");
$requete->execute();
$resultat = $requete->fetchall();


print (json_encode($resultat));

    











//publication
}else
if($code=="4"){

// Récupération des données POST
$nomPrenom = $_POST["nomPrenom"];
$texte = $_POST["password"];
$identifiant = $_POST["identifiant"];
$heure = $_POST["heure"];
$image1 = $_POST["image"];

// Vérification et traitement de l'image
if (strlen($image1) > 5000) {
    $kaled = './images';
    $folder1 = $kaled . "/" . rand() . "_" . time() . ".png";
    file_put_contents($folder1, base64_decode($image1));
    $folder1 = substr($folder1, 1);
} else {
    $folder1 = $_POST["image"];
}

// Connexion à la base de données
try {
    $connexion = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Requête préparée pour récupérer nom et image dans la base de données
    $requete = $connexion->prepare("SELECT nom, image FROM inscription WHERE nomPrenom = :nomPrenom");
    $requete->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $requete->execute();

    $resultat = $requete->fetchAll(); // Résultat stocké dans un tableau array.

    $nom = $resultat[0][0];
    $image = $resultat[0][1];

    // Requête préparée pour l'insertion dans la table publication
    $insertion = $connexion->prepare("INSERT INTO publication (nomPrenom, texte, image, heure, image1, identifiant, likes, liker)
             VALUES (:nomPrenom, :texte, :image, :heure, :folder1, :identifiant, '0', 'false')");
    $insertion->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $insertion->bindParam(':texte', $texte, PDO::PARAM_STR);
    $insertion->bindParam(':image', $image, PDO::PARAM_STR);
    $insertion->bindParam(':heure', $heure, PDO::PARAM_STR);
    $insertion->bindParam(':folder1', $folder1, PDO::PARAM_STR);
    $insertion->bindParam(':identifiant', $identifiant, PDO::PARAM_STR);
    $insertion->execute();

    echo 'Publication réussie';

    // Création de la table $identifiant
    $table = "CREATE TABLE $identifiant (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nomPrenom VARCHAR(20),
        commentaire TEXT,
        profil VARCHAR(200),
        identifiant VARCHAR(200)
    )";
    $connexion->exec($table);

    // Création de la table $identifiantlike
    $identifiantlike = $identifiant . "like";
    $table_like = "CREATE TABLE $identifiantlike (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nomPrenom VARCHAR(20),
        image VARCHAR(200),
        type VARCHAR(30)

    )";
    $connexion->exec($table_like);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}







// Pour les likes
}else
if ($code == "3461") {
    $nom = $_POST['nom'];
    $nomPrenom = $_POST['nomPrenom'];
    $identifiant = $_POST['identifiant'];
    $type = $_POST['type'];
    $image = $_POST['image'];
    $nom1 = $_POST['nom1'];

    // Insertion des likes dans la table Notifications
    $nom1 = "kaled_" . $nom1;
    $connexion_like = new PDO("mysql:host=$serveur;dbname=$nom1", $login, $pass);
    $insertion_notification = $connexion_like->prepare("INSERT INTO Notifications (nomPrenom, image, type)
             VALUES (:nomPrenom, :image, 'like')");
    $insertion_notification->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $insertion_notification->bindParam(':image', $image, PDO::PARAM_STR);
    $insertion_notification->execute();

    // Insertion des likes dans la table correspondante
    $identifiant_like = $identifiant . "like";
    $connexion_like = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);
    $insertion_like = $connexion_like->prepare("INSERT INTO $identifiant_like (nomPrenom, image,type)
             VALUES (:nomPrenom, :image, :type)");
    $insertion_like->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $insertion_like->bindParam(':image', $image, PDO::PARAM_STR);
    $insertion_like->bindParam(':type', $type, PDO::PARAM_STR);
    $insertion_like->execute();

    // Mise à jour du nombre de likes dans la table publication
    $connexion_update = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);
    $requete_update = $connexion_update->prepare("SELECT liker, likes FROM publication WHERE identifiant = :identifiant");
    $requete_update->bindParam(':identifiant', $identifiant, PDO::PARAM_STR);
    $requete_update->execute();
    $resultat_update = $requete_update->fetchAll();
    $resultat1 = $resultat_update[0]['liker'];
    $resultat2 = $resultat_update[0]['likes'];



    $nomPrenom=$nomPrenom.$type;
    $like = strval(intval($resultat2) + 1);
    $liker = $resultat1 . $nomPrenom;

    $requete_update = $connexion_update->prepare("UPDATE publication SET liker = :liker, likes = :like WHERE identifiant = :identifiant");
    $requete_update->bindParam(':liker', $liker, PDO::PARAM_STR);
    $requete_update->bindParam(':like', $like, PDO::PARAM_STR);
    $requete_update->bindParam(':identifiant', $identifiant, PDO::PARAM_STR);
    $requete_update->execute();

    echo 'Code bien modifié';
}


//recherche
else
if($code=="5"){

$nom=$_POST['nom'];
$nomPrenom=$_POST['nomPrenom'];
$connexion = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);

$requete = $connexion->prepare("SELECT image FROM inscription WHERE nomPrenom= '$nomPrenom'");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
$taille = count($resultat);
if($taille==0){
echo"";
}else{
$nom="kaled_".$nom;
$connexion1 = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);

$requete1 = $connexion1->prepare("SELECT  biographie FROM biographie");
$requete1->execute();
$resultat1 = $requete1->fetchall();

$nom1= $resultat1[0][0];




$nom= $resultat[0][0];

$af=$nom1;
$af.="-";
$af.=$nom;

echo $af;
}












//envoyer demande
}else
if($code=="6"){

$nouveau = "1";
$demande = $_POST["demande"];
$nom = $_POST["nom"];
$nom = "kaled_" . $nom;
$nomPrenom = $_POST["nomPrenom"];
$image = $_POST["image"];
$ya = "non";

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$nom", $login, $pass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérification si l'utilisateur est déjà ami
    $requete1 = $connexion->prepare("SELECT nom FROM Amis");
    $requete1->execute();
    $resultat = $requete1->fetchAll();
    $taille = count($resultat);

    // Vérification de l'existence du nomPrenom
    for ($i = 0; $i < $taille; $i++) {
        $resultat1 = $resultat[$i][0];
        if (strpos($nomPrenom, $resultat1) !== false) {
            // Déjà amis
            $ya = "oui";
            break;
        }
    }

    // Vérification pour enregistrer ou pas
    if ($ya == "non") {
        $requete_update = $connexion->prepare("UPDATE demande SET nouveau = 1 WHERE id = 1");
        $requete_update->execute();

        $insertion30 = $connexion->prepare("INSERT INTO demande (demande, nomPrenom, image, nouveau)
             VALUES (:demande, :nomPrenom, :image, :nouveau)");
        $insertion30->bindParam(':demande', $demande, PDO::PARAM_STR);
        $insertion30->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
        $insertion30->bindParam(':image', $image, PDO::PARAM_STR);
        $insertion30->bindParam(':nouveau', $nouveau, PDO::PARAM_STR);
        $insertion30->execute();

        echo "Reussite";
    } else {
        echo "Vous êtes déjà amis";
    }
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}



}else
if($code=="7"){

$nom=$_POST['nom'];//mon nom
$nom="kaled_".$nom;
$nouveau1="sal";

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);

$requete = $connexion->prepare("SELECT nouveau FROM demande WHERE id=1 ");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.

$nouveau= $resultat[0][0];
if($nouveau=="1"){
//$requete1="UPDATE demande SET nouveau=2 WHERE id=1";
//$connexion->exec($requete1);


$requete1 = $connexion->prepare("SELECT nomPrenom FROM demande ORDER BY id DESC LIMIT 0,1");
$requete1->execute();
$resultat1 = $requete1->fetchall();//resultat stoquée dans un Tableau Array.
$nouveau1= $resultat1[0][0];

}
$af=$nouveau;
$af.=",";
$af.=$nouveau1;

echo $af;









//Recuperation des invitations

}else
   if($code=="10"){

$nom=$_POST['nom'];
$nom="kaled_".$nom;
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM demande ");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
print (json_encode($resultat));





//on recupere les Notifications ici ET on les envoies

}else
   if($code=="10112"){
$nom=$_POST['nom'];
$nom="kaled_".$nom;
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM Notifications ");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
print (json_encode($resultat));








//changer nouveau en 3 si l utilisateur a ouvert le message 
}else
   if($code=="11"){


$nom=$_POST["nom"];
$nom="kaled_".$nom;
$nomPrenom=$_POST["nomPrenom"];//de la personne en question
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete1="UPDATE demande SET nouveau=3 WHERE nomPrenom='$nomPrenom'";
$connexion->exec($requete1);














//Acceptation de l invitation

// 0 pour moi,1 pour lui

}else
   if($code=="15"){

$nom=$_POST["nom"];//mon nom
$nom="kaled_".$nom;
$son_nom=$_POST["son_nom"];//de la personne en question
$nomPrenom=  $_POST["nomPrenom"];//nom et prenom de la personne en question
$nomPrenom1= $_POST["nomPrenom1"];//nom et prenom de moi meme

//creation de la table avec le nom de l'envoyeur de message  dans ma base de données
//d'abord On recupere l' image de la personne en question
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$connexion10 = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);
$requete = $connexion10->prepare("SELECT image FROM inscription   WHERE nomPrenom='$nomPrenom' ");
$requete->execute();

$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
$image= $resultat[0][0];
echo $image;


//on enregistre le message dans sa base de donnée
$insertion= "INSERT INTO Messages (nomPrenom,mine,message,recu,vue,nombre,image)
             VALUES ('$nomPrenom','1','Bienvenue à Vous deux!','0','0','1','$image')";
$connexion->exec($insertion);
echo'message bien enregistre';

$table = " CREATE TABLE $son_nom(
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
messages TEXT,
mine  VARCHAR(5),
vue  VARCHAR(5),
type  VARCHAR(25),
reponse  VARCHAR(250),
heure  VARCHAR(50)
)";
$connexion->exec($table);

//Insertion du message Unitaire obligatoir dans ma base sur la Table Message et sur la Table 
//avec le nom de la personne que j' ai accepté la demande.





//on enregistre le nom et l image aussi dans mes amis
//mais on recupere aussi le bio 

//pour recuperer sa biographie

$son_nom="kaled_".$son_nom;//car c est lui que je veux enregistrer dans mes amis
$connexion1 = new PDO("mysql:host=$serveur;dbname=$son_nom",$login,$pass);
$requete1 = $connexion1->prepare("SELECT  biographie FROM biographie");
$requete1->execute();
$resultat1 = $requete1->fetchall();
$bio= $resultat1[0][0];


$son_nom=$_POST["son_nom"];//apres son nom retourne
$insertion= "INSERT INTO Amis (nom,image,bio)
             VALUES ('$son_nom','$image','$bio')";
$connexion->exec($insertion);



//pour lui (la personne que j'ai accepté l' invitation )




//creation de la table avec le nom de l'envoyeur de message  dans ma base de données


$son_nom="kaled_".$son_nom;

$connexion = new PDO("mysql:host=$serveur;dbname=$son_nom",$login,$pass);
//d'abord On recupere l' image de la personne en question
$connexion10 = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);
$requete = $connexion10->prepare("SELECT image FROM inscription   WHERE nomPrenom= '$nomPrenom1' ");
$requete->execute();

$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
$image= $resultat[0][0];


$insertion= "INSERT INTO Messages (nomPrenom,mine,message,recu,vue,nombre,image)
             VALUES ('$nomPrenom1','1','Bienvenue à Vous deux!','0','0','1','$image')";
$connexion->exec($insertion);
echo'message2 bien enregistree';




$nom=$_POST["nom"];
$table = " CREATE TABLE $nom(
id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
messages TEXT,
mine  VARCHAR(5),
vue  VARCHAR(5),
type  VARCHAR(25),
reponse  VARCHAR(250),
heure  VARCHAR(50)
)";
$connexion->exec($table);


$nom="kaled_".$nom;
//on enregistre mon nom aussi dans ses amis
$connexion1 = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete1 = $connexion1->prepare("SELECT  biographie FROM biographie");
$requete1->execute();
$resultat1 = $requete1->fetchall();
$bio= $resultat1[0][0];

$nom=$_POST["nom"];
$insertion= "INSERT INTO Amis (nom,image,bio)
             VALUES ('$nom','$image','$bio')";
$connexion->exec($insertion);







//on efface aussi la demande
$nom=$_POST["nom"];//mon nom
$nom="kaled_".$nom;
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
//Je supprime la demande 
$requete1 = $connexion->prepare("DELETE FROM demande WHERE nomPrenom='$nomPrenom'");
$requete1->execute();






//recuperation des messages et des contacts
}else
   if($code=="16"){

$nom=$_POST["nom"];//mon nom
$nom="kaled_".$nom;
$nomPrenom=$_POST["nomPrenom"];//mon nomPrenom

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM Messages ");
$requete->execute();
$resultat = $requete->fetchall();
$taille = count($resultat);

for ($i=0 ; $i<$taille ; $i++){
$resultat1 = $resultat[$i][1];
$part=explode(" ",$resultat1);
$nom1=$part[0];
$nom1="kaled_".$nom1;

$connexion = new PDO("mysql:host=$serveur;dbname=$nom1",$login,$pass);
$requete1 = $connexion->prepare("UPDATE Messages SET recu=1 WHERE nomPrenom='$nomPrenom'");
$requete1->execute();

}


print (json_encode($resultat));



//ici on va aussi enregistrer l heure

















//changement de biographie unitaire
}else
   if($code=="19"){

$nom = $_POST["nom"];
$bio = $_POST["bio"];
$pays = $_POST["pays"];
$adresse = $_POST["adresse"];
$age = $_POST["age"];
$nom = "kaled_" . $nom;

// Assurez-vous d'avoir les informations de connexion définies (serveur, login, pass)

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=$nom", $login, $pass);

    // Utilisez des paramètres dans la requête préparée
    $requete1 = $connexion->prepare("UPDATE biographie SET biographie=:bio, pays=:pays, adresse=:adresse, age=:age");

    // Liez les valeurs aux paramètres
    $requete1->bindParam(':bio', $bio, PDO::PARAM_STR);
    $requete1->bindParam(':pays', $pays, PDO::PARAM_STR);
    $requete1->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $requete1->bindParam(':age', $age, PDO::PARAM_INT);

    // Exécutez la requête préparée
    $requete1->execute();

    // Fermez la connexion
    $connexion = null;
} catch (PDOException $e) {
    // Gérez les erreurs de la base de données
    echo "Erreur: " . $e->getMessage();
}





//Recuperation de la biographie de la personne que j ai cherché.
}else
   if($code=="20"){

$nom=$_POST["nom"];//mon nom
$nom="kaled_".$nom;


$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM biographie ");
$requete->execute();
$resultat = $requete->fetchall();


print (json_encode($resultat));















//Vues sur un message cliquer 
}else
   if($code=="25"){

$nom12=$_POST["nom"];//mon nom
$nom="kaled_".$nom12;
$nomPrenom=$_POST["nomPrenom"];//son nom prenom
$nomPrenom1=$_POST["nomPrenom1"];//mon nomPrenom

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete1 = $connexion->prepare("UPDATE Messages SET nombre=0 WHERE nomPrenom='$nomPrenom'");
$requete1->execute();




$part=explode(" ",$nomPrenom);
$nom11=$part[0];
$nom1="kaled_".$nom11;

$connexion = new PDO("mysql:host=$serveur;dbname=$nom1",$login,$pass);
$requete1 = $connexion->prepare("UPDATE Messages SET vue=1 WHERE nomPrenom='$nomPrenom1'");
$requete1->execute();







//ici on recupere on enregistre le vue aussi sur son mesage dans le table de son nom
$connexion = new PDO("mysql:host=$serveur;dbname=$nom1",$login,$pass);
$requete1 = $connexion->prepare("UPDATE $nom12 SET vue='1' WHERE id=(SELECT MAX(id) FROM $nom12)");
$requete1->execute();



//ici on recupere le statut  en ligne
$connexion = new PDO("mysql:host=$serveur;dbname=$nom1",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM statut ");
$requete->execute();
$resultat = $requete->fetchall();

print (json_encode($resultat));








//pour la recuperation des message
}else
   if($code=="30"){

$nom=$_POST["nom"];//mon nom
$nom="kaled_".$nom;
$sonNom=$_POST["sonNom"];// son nom



$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM $sonNom ");
$requete->execute();
$resultat = $requete->fetchall();


print (json_encode($resultat));












//code pour l envoie de notification pour les nouveaux messages
}else
   if($code=="100"){
    
$nom=$_POST['nom'];//mon nom
$nom="kaled_".$nom;

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM nouveau_messages ");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
print (json_encode($resultat));

















//enregistrement des images 

}else
   if($code=="125"){

$nom=$_POST["nom"];
$type=$_POST["type"];
$nom="kaled_".$nom;

$image=$_POST["image"];
$kaled='./images';
$folder1=$kaled."/".rand()."_".time().".png";

file_put_contents($folder1,base64_decode($image));

$folder1= substr($folder1, 1);

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$insertion= "INSERT INTO images (image,type)
             VALUES ('$folder1','$type')";
$connexion->exec($insertion);

echo'image bien enregistré';
 











//Modification du mot de passe image
}else
   if($code=="126"){

$nom=$_POST["nom"];
$codek=$_POST["password"];
$nom="kaled_".$nom;

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete1 = $connexion->prepare("UPDATE code SET codek='$codek' ");
$requete1->execute();
echo'code bien Modifié';




















//verification code image
}else
   if($code=="127"){

//recuperation du mot de passe
$nom=$_POST["nom"];
$nom="kaled_".$nom;
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT codek FROM code ");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
$code=$resultat[0][0];
echo $code;













//recuperation des liens des images 
}else
   if($code=="128"){
$nom=$_POST["nom"];
$type=$_POST["type"];
$nom="kaled_".$nom;

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT image FROM images WHERE type= '$type'");
$requete->execute();
$resultat = $requete->fetchall();//
print (json_encode($resultat));












//recuperation des commentaire
}else
if($code=='129'){

$identifiant=$_POST["identifiant"];
    

$connexion = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);

$requete = $connexion->prepare("SELECT * FROM $identifiant ");
$requete->execute();
$resultat = $requete->fetchall();

print (json_encode($resultat));

 












//pour les Story (enregistrement)
}else
if($code=="70"){
$nomPrenom = $_POST["nomPrenom"]; // Mon nom et prénom
$nom = $_POST["nom"]; // Mon nom seul
$heure = $_POST["heure"]; // Pour l'heure
$identifiant = $_POST["identifiant"]; // Identifiant
$texte = $_POST["texte"]; // Le texte de l'image si c'est une image, sinon la couleur de fond pour les storystexte
$nom = "kaled_" . $nom;

// Enregistrement de l'image du Story
$image1 = $_POST["image"];

if (strlen($image1) > 2000) {
    $kaled = './images';
    $folder1 = $kaled . "/" . rand() . "_" . time() . ".png";
    file_put_contents($folder1, base64_decode($image1));
    echo 'Story mis à jour';
    $folder1 = substr($folder1, 1);
} else {
    $folder1 = $_POST["image"]; // Texte du Story si le Story est du texte
}

// Récupération de ma photo de profil
try {
    $connexion10 = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);
    $requete = $connexion10->prepare("SELECT image FROM inscription WHERE nomPrenom = :nomPrenom");
    $requete->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $requete->execute();

    $resultat = $requete->fetchAll(); // Résultat stocké dans un tableau Array.
    $image = $resultat[0][0];

    // Ajout dans mes Storys
    $connexion = new PDO("mysql:host=$serveur;dbname=$nom", $login, $pass);
    $insertion = $connexion->prepare("INSERT INTO mStorys (nomPrenom, image, heure, identifiant, profil, texte)
             VALUES (:nomPrenom, :folder1, :heure, :identifiant, :image, :texte)");
    $insertion->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $insertion->bindParam(':folder1', $folder1, PDO::PARAM_STR);
    $insertion->bindParam(':heure', $heure, PDO::PARAM_STR);
    $insertion->bindParam(':identifiant', $identifiant, PDO::PARAM_STR);
    $insertion->bindParam(':image', $image, PDO::PARAM_STR);
    $insertion->bindParam(':texte', $texte, PDO::PARAM_STR);
    $insertion->execute();

    // Création de la table identifiant du Story
    $table = "CREATE TABLE $identifiant (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nomPrenom VARCHAR(20),
        image VARCHAR(200),
        coeur VARCHAR(200)
    )";
    $connexion->exec($table);

    // Ajout dans Storys
    $insertion = $connexion->prepare("INSERT INTO Storys (nomPrenom, image, nombre, texte)
             VALUES (:nomPrenom, :folder1, '1', :texte)");
    $insertion->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $insertion->bindParam(':folder1', $folder1, PDO::PARAM_STR);
    $insertion->bindParam(':texte', $texte, PDO::PARAM_STR);
    $insertion->execute();

    // Ajout dans Storys de mes amis
    $connexion = new PDO("mysql:host=$serveur;dbname=$nom", $login, $pass);
    $requete = $connexion->prepare("SELECT nom FROM Amis ");
    $requete->execute();
    $resultat = $requete->fetchAll(); // Résultat stocké dans un tableau Array.
    $taille = count($resultat); // Taille du tableau.

    // Chaque nom, on va ajouter dans ses Storys la photo et mon nomPrenom
    for ($i = 0; $i < $taille; $i++) {
        $amiNom = $resultat[$i][0];
        $amiNom = "kaled_" . $amiNom;

        $connexionAmi = new PDO("mysql:host=$serveur;dbname=$amiNom", $login, $pass);
        $insertionAmi = $connexionAmi->prepare("INSERT INTO Storys (nomPrenom, image, nombre, texte)
             VALUES (:nomPrenom, :folder1, '1', :texte)");
        $insertionAmi->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
        $insertionAmi->bindParam(':folder1', $folder1, PDO::PARAM_STR);
        $insertionAmi->bindParam(':texte', $texte, PDO::PARAM_STR);
        $insertionAmi->execute();
    }

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}




//Recuperation des Storys
}else
if($code=="71"){
$nom =$_POST['nom'];
$nom="kaled_".$nom;

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);;
$requete = $connexion->prepare("SELECT * FROM Storys ");
$requete->execute();
$resultat = $requete->fetchall();

print (json_encode($resultat));



//recuperation des Story unitaire
}else
   if($code=="999"){

$nom=$_POST['nom'];
$nom="kaled_".$nom;
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM mStorys ");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
print (json_encode($resultat));








//enregistrement de mon commentaire
}else
if($code=='130'){

$nomPrenom = $_POST["nomPrenom"];
$commentaire = $_POST["commentaire"];
$identifiant = $_POST["identifiant"];
$identifiantc = $_POST["identifiantc"];

$nom = $_POST['nom']; // Le nom de la personne qui a partagé la publication
$imagek = $_POST['image']; // Mon image à moi qui a commenté

try {
    $connexion = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);
    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération de l'image de moi qui a commenté
    $requete = $connexion->prepare("SELECT image FROM inscription WHERE nomPrenom = :nomPrenom");
    $requete->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $requete->execute();
    $resultat = $requete->fetchAll();
    $image = $resultat[0][0];

    // Insertion du commentaire
    $insertion = $connexion->prepare("INSERT INTO $identifiant (nomPrenom, commentaire, profil, identifiant)
             VALUES (:nomPrenom, :commentaire, :image, :identifiantc)");
    $insertion->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $insertion->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
    $insertion->bindParam(':image', $image, PDO::PARAM_STR);
    $insertion->bindParam(':identifiantc', $identifiantc, PDO::PARAM_STR);
    $insertion->execute();

    // Création de la table de l'identifiant de son commentaire
    $table_connexion7 = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);
    $table = "CREATE TABLE $identifiantc (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        nomPrenom VARCHAR(20),
        commentaire TEXT,
        profil VARCHAR(200)
    )";
    $table_connexion7->exec($table);

    // Enregistrement des likes dans les notifications
    $nom1 = "kaled_" . $nom;
    $connexion_notifications = new PDO("mysql:host=$serveur;dbname=$nom1", $login, $pass);
    $insertion_notifications = $connexion_notifications->prepare("INSERT INTO Notifications (nomPrenom, image, type)
             VALUES (:nomPrenom, :imagek, 'com')");
    $insertion_notifications->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $insertion_notifications->bindParam(':imagek', $imagek, PDO::PARAM_STR);
    $insertion_notifications->execute();
} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}



//vues sur les Storys
}else
if($code=='1006'){
$nom=$_POST["nom"];//son nom seul
$nomPrenom=$_POST["nomPrenom"];//mon nomPrenom
$identifiant=$_POST["identifiant"];
$nom="kaled_".$nom;

$ya="non";//si oui c est qu il y a son nom
$coeur=$_POST["coeur"];//si l utilisateur a juste vue le Story ,c' est le k juste qui sera envoyer
//sinon le mot coeur.


if($coeur=="coeur"){
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete2 = $connexion->prepare("UPDATE $identifiant SET coeur='coeur' WHERE nomPrenom='$nomPrenom'");
$requete2->execute();
}else{

//d'abord On recupere l' image de moi qui a lu la Story
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
//ici on verifie d abord Si l utilisateur n'a pas deja vue la Story
$requete1 = $connexion->prepare("SELECT nomPrenom FROM  $identifiant ");
$requete1->execute();
$resultat = $requete1->fetchall();//resultat stoquée dans un Tableau Array.
$taille = count($resultat); //Taille du Tableau.


//verification de l existance du nomPrenom
for ($i=0 ; $i<$taille ; $i++){
$resultat1 = $resultat[$i][0];

if($resultat1== $nomPrenom){
//ici ca veut dire que le nom Existe
$ya="oui";
break;
}

}




//ici on fais une verification pour enregistrer ou pas

if($ya=="non"){

$connexion10 = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);
$requete = $connexion10->prepare("SELECT image FROM inscription   WHERE nomPrenom='$nomPrenom' ");
$requete->execute();

$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
$image= $resultat[0][0];
echo $image;


//on  inserre l'image et le nom dans  la table idemtofiant 
$insertion= "INSERT INTO $identifiant (nomPrenom,image)
             VALUES ('$nomPrenom','$image')";

$connexion->exec($insertion);

echo "vues enregistre";

}else{

echo "le nom Existe deja";


}


}


















//recuperation des Vues
}else
if($code=="1007"){
$nom =$_POST['nom'];//mon nom
$identifiant=$_POST["identifiant"];
$nom="kaled_".$nom;

$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);;
$requete = $connexion->prepare("SELECT * FROM  $identifiant");
$requete->execute();
$resultat = $requete->fetchall();

print (json_encode($resultat));










//recuperation des persones qui ont likés les pubs.............
}else
if($code=="10119"){
$identifiant=$_POST["identifiant"];
$identifiant .="like";

$connexion = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM  $identifiant");
$requete->execute();
$resultat = $requete->fetchall();
print (json_encode($resultat));









//recuperation des Utilisateurs
}else
if($code=="1008"){
$connexion = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);
$requete = $connexion->prepare("SELECT nomPrenom,image FROM  inscription");
$requete->execute();
$resultat = $requete->fetchall();

print (json_encode($resultat));







//reponse aux commentaires
}else
if($code=='1010'){

$nomPrenom = $_POST["nomPrenom"];
$commentaire = $_POST["commentaire"];
$identifiant = $_POST["identifiant"];



try {
    $connexion = new PDO("mysql:host=$serveur;dbname=kaled_base", $login, $pass);

    // Récupération de l'image de l'utilisateur qui a commenté
    $requete10 = $connexion->prepare("SELECT image FROM inscription WHERE nomPrenom = :nomPrenom");
    $requete10->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $requete10->execute();

    $resultat = $requete10->fetchAll(); // Résultat stocké dans un tableau Array.
    $image = $resultat[0][0];

    // Utilisation d'une requête préparée pour l'insertion
    $requeteInsertion = $connexion->prepare("INSERT INTO $identifiant (nomPrenom, commentaire, profil) VALUES (:nomPrenom, :commentaire, :image)");
    
    // Liaison des valeurs aux paramètres
    $requeteInsertion->bindParam(':nomPrenom', $nomPrenom, PDO::PARAM_STR);
    $requeteInsertion->bindParam(':commentaire', $commentaire, PDO::PARAM_STR);
    $requeteInsertion->bindParam(':image', $image, PDO::PARAM_STR);

    // Exécution de la requête préparée
    $requeteInsertion->execute();

    // Fermeture de la connexion
    $connexion = null;
} catch (PDOException $e) {
    // Gestion des erreurs de la base de données
    echo "Erreur: " . $e->getMessage();
}




//recuperation des Reponses au commentaire
}else
if($code=='1011'){

$identifiant=$_POST["identifiant"];
    
$connexion = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);

$requete = $connexion->prepare("SELECT * FROM $identifiant ");
$requete->execute();
$resultat = $requete->fetchall();

print (json_encode($resultat));






//pour les notifications a afficher dans le Toast
}else
   if($code=="10013"){
$nom=$_POST['nom'];
$nom="kaled_".$nom;
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM Notifications ORDER BY id DESC LIMIT 1");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
print (json_encode($resultat));







//recuperation de la liste des gif
}else
if($code=='1598'){
    
$connexion = new PDO("mysql:host=$serveur;dbname=kaled_base",$login,$pass);

$requete = $connexion->prepare("SELECT * FROM gifs ");
$requete->execute();
$resultat = $requete->fetchall();

print (json_encode($resultat));





//recuperation de la liste des amis
}else
if($code=='20130'){
    
$nom=$_POST['nom'];
$nom="kaled_".$nom;
$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM Amis");
$requete->execute();
$resultat = $requete->fetchall();//resultat stoquée dans un Tableau Array.
print (json_encode($resultat));















}else
if($code=='3721'){
    

$nom=$_POST["nom"];//mon nom
$nom="kaled_".$nom;
$sonNom=$_POST["sonNom"];// son nom
$id=$_POST["id"];
$id=$id+1;



$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete = $connexion->prepare("SELECT * FROM $sonNom WHERE id>=$id");
$requete->execute();
$resultat = $requete->fetchall();

print (json_encode($resultat));







//pour mettre qu on est en ligne

}else
if($code=='2745'){
    

$nom=$_POST["nom"];//mon nom
$nom="kaled_".$nom;
$date=$_POST["date"];// la date 
$heure=$_POST["heure"];//en minute




$connexion = new PDO("mysql:host=$serveur;dbname=$nom",$login,$pass);
$requete1="UPDATE statut SET date=$date WHERE id=1";
$connexion->exec($requete1);

$requete1="UPDATE statut SET heure=$heure WHERE id=1";
$connexion->exec($requete1);




}else{

echo "oui ça marche plutot bien";
}





?>
