<?php
session_start();
require __DIR__.'/vendor/autoload.php';

use Mailjet\Client;
use Mailjet\Resources;
// use PDO;
// use Exception;

try {
    $bdd = new PDO('mysql:host=localhost;dbname=bd_stock', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['nom'], $_POST['prenom'], $_POST['email'], $_POST['telephone'], $_POST['reservationNumber'], $_POST['submit'])) {
        $nom = htmlspecialchars($_POST['nom']);
        $prenom = htmlspecialchars($_POST['prenom']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $telephone = htmlspecialchars($_POST['telephone']);
        $reservationNumber = htmlspecialchars($_POST['reservationNumber']);

        $etat = 0;
        $depart = $_SESSION['depart'];
        $arrivee = $_SESSION['arrivee'];
        $date = $_SESSION['date'];
        $idVoyage = $_SESSION['idVoyage'];
        $prix = $_SESSION['prix'];

        // Insertion dans la base de données
        $requete = "INSERT INTO reservation (nom, prenom, telephone, email, idVoyage, Etat, Numero_reservation) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $bdd->prepare($requete);
        $stmt->execute([$nom, $prenom, $email, $telephone, $idVoyage, $etat, $reservationNumber]);

        // Configuration de l'API Mailjet
        $mj = new Client('f163a8d176afbcb29aae519bf6c5e181', 'bf285777b4d59f84a43855ae1b40f96d', true, ['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => 'akuetche55@gmail.com',
                        'Name' => 'Easy travel',
                    ],
                    'To' => [
                        [
                            'Email' => $email,
                            'Name' => $nom,
                        ],
                    ],
                    'Subject' => 'Confirmation de Réservation',
                    'HTMLPart' => "<h1>Reçu de réservation</h1>
                    <div class='voyageur'>
                        <div class='infos-voyageur'>
                            <p>Numéro réservation : $idVoyage</p>
                            <p>Compagnie : Général Voyage</p>
                            <p>Passager : $nom $prenom</p>
                            <p>Téléphone : $telephone</p>
                            <p>Numero Ref : $reservationNumber</p>
                        </div>
                        <div class='header-picture'>
                            <img src='logo général.jpg' alt='logo site' />
                        </div>
                    </div>"
                ],
            ],
        ];

        // Envoi de l'email
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if ($response->success()) {
            echo 'Email sent successfully.';
        } else {
            echo 'Failed to send email: ' . $response->getData()['ErrorMessage'];
        }

        echo "<meta http-equiv='refresh' content='10;url=Accueil.php'>";
        exit;
    }
} catch (Exception $e) {
    echo 'Échec de connexion : ' . $e->getMessage();
}
?>

<style>
.container {
    height: 100px;
    width: 600px;
    background-color: green;
    color: white;
    font-size: 16px;
}
</style>