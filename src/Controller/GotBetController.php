<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Personnage;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\User\UserInterface;
//use Symfony\Component\Routing\Annotation\JsonResponse;
use Symfony\Component\HttpFoundation\JsonResponse;

class GotBetController extends AbstractController
{
    /**
     * @Route("/", name="got_bet")
     */
    public function index()
    {
        return $this->render('got_bet/index.html.twig', [
            'controller_name' => 'GotBetController',
            'message' => false
        ]);
    }

    /**
     * @Route("/gotbet/questionnaire", name="questionnaire")
     */
    public function questionnaire()
    {
        $repo = $this->getDoctrine()->getRepository(Personnage::class);
        $personnages = $repo->findAll();
        $repoQ = $this->getDoctrine()->getRepository(Question::class);
        $questions = $repoQ->findAll();

        return $this->render('got_bet/questionnaire.html.twig', [
            'controller_name' => 'GotBetController',
            'personnages' => $personnages,
            'questions' => $questions
        ]);
    }

    /**
     * @Route("/gotbet/createReponse", name="createReponse", methods="POST")
     */
    public function createReponse(Request $request){
        $repo = $this->getDoctrine()->getRepository(Personnage::class);
        $personnages = $repo->findAll();
        //var_dump($request->request);
        //var_dump($request->request->get("statut_1"));

        foreach($personnages as $p){
            $entityManager = $this->getDoctrine()->getManager();
            $pid = $p->id;
            $statut = "statut_{$pid}";
            $pstatut = $request->request->get($statut);
            //var_dump($pid, $pstatut);
            $reponse = new Reponse();
            $reponse->setPersonnage($p);
            $reponse->setStatut($pstatut);
            $reponse->setUser($user = $this->getUser());
            $entityManager->persist($reponse);
            $entityManager->flush();
        }
        $entityManager = $this->getDoctrine()->getManager();
        $queryJouer = $entityManager->createQuery(
          'UPDATE App\Entity\User u SET u.jouer = 1
          WHERE u.id = :u')
          ->setParameter('u', $this->getUser());

        return $this->render('got_bet/index.html.twig', [
        $queryJouer->execute(),
        'message'=> true
    ]);
    }

    /**
     * @Route("/gotbet/scores", name="scores", methods="GET")
     */
    public function scores(){
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findBy([], ['score' => 'DESC']);

        $entityManager = $this->getDoctrine()->getManager();
        foreach ($users as $u) {
          $query3 = $entityManager->createQuery(
            'UPDATE App\Entity\User u SET u.score = (
              SELECT count(p)
              FROM App\Entity\Personnage p
              INNER JOIN App\Entity\Reponse r
              WHERE r.user = :u AND r.personnage = p.id
              AND r.statut = p.etat)
              WHERE u.id = :u')
              ->setParameter('u', $u);
            $query3->execute();
        }
        return $this->render('got_bet/scores.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route("/gotbet/about", name="about", methods="GET")
     */
    public function about(){
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findBy([], ['score' => 'DESC']);

        return $this->render('got_bet/about.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * @Route("/gotbet/compte", name="compte", methods="GET")
     */
    
    public function monCompte(){
        $entityManager = $this->getDoctrine()->getManager();
        $queryCompte = $entityManager->createQuery(
            'SELECT count(distinct(u)) as nombre
          
            From App\Entity\User u
            INNER JOIN App\Entity\Reponse r
            WHERE  r.user = u 
            ')
           ;
        $compte = $queryCompte->execute();
        $nb=$compte[0]["nombre"];

        $queryParticipants = $entityManager->createQuery(
            'SELECT count(u) as nombre
            From App\Entity\User u
            ')
           ;
        $participants = $queryParticipants->execute();
        $nbPart=$participants[0]["nombre"];
        
        $query = $entityManager->createQuery(
            'SELECT p.id,p.nom, p.prenom, r.statut, u.score,p.etat
            FROM App\Entity\Personnage p
            INNER JOIN App\Entity\Reponse r
            INNER JOIN App\Entity\User u
            WHERE p.id = r.personnage AND r.user = :u AND u.id = :u')
            ->setParameter('u', $this->getUser());
        $personnages = $query->execute();
        
        $query2 = $entityManager->createQuery(
            'SELECT COUNT(r) as total
            FROM App\Entity\Personnage p
            INNER JOIN App\Entity\Reponse r
            INNER JOIN App\Entity\User u
            WHERE r.user = :u AND u.id = :u AND r.personnage = p.id
            AND r.statut = p.etat')
            ->setParameter('u', $this->getUser());

        $query3 = $entityManager->createQuery(
          'UPDATE App\Entity\User u SET u.score = (
            SELECT count(p)
            FROM App\Entity\Personnage p
            INNER JOIN App\Entity\Reponse r
            WHERE r.user = :u AND r.personnage = p.id
            AND r.statut = p.etat)
            WHERE u.id = :u')
            ->setParameter('u', $this->getUser());
          $query3->execute();

       $i=0;
      
           foreach ($personnages  as $ligne)
            {
               $personnages[$i]["stats"]=array();
                
                $query4 = $entityManager->createQuery(
                'SELECT count(r) as nb, r.statut
                  FROM App\Entity\Reponse r
                  where r.personnage='.$ligne["id"].'
                  GROUP BY  r.statut
                  ORDER BY r.statut
                  DESC
                  ');
                $stats=array();
               foreach ($query4->execute()  as $ligneStat)
                { 
                    $stat["libelle"]=$ligneStat["statut"];
                    $stat["nombre"]=$ligneStat["nb"]*100/$nb;
                    $stats[]=$stat;
                }
                $personnages[$i]["stats"]=$stats;
                 $i++;
            }
            
        return $this->render('got_bet/compte.html.twig', [
            'persorep' => $personnages,
            'nb' => $nbPart,
            'res' => $query2->execute(),
        ]);
    }
}
