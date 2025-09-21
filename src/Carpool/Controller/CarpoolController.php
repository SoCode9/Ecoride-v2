<?php

namespace App\Carpool\Controller;

use App\Database\DbConnection;
use PDO;
use App\Controller\BaseController;


class CarpoolController extends BaseController
{
    public function list()
    {

        //requête en BDD pour récupérer la liste des covoiturages (créer la table en dbb)
        $query = DbConnection::getPdo()->prepare('SELECT * FROM carpool');
        $query->execute();

        $carpools = $query->fetchAll(PDO::FETCH_OBJ);
        //renvoyer le résultat et donner un template à utiliser
        //on voudra retourner un tableau

        return $this->render('pages/carpools/list.php', [
            'carpools' => $carpools
        ]);
    }
}
