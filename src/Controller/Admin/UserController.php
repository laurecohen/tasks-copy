<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Entity\UserTeam;
use App\Entity\UserSkill;
use App\Form\UserTeamType;
use App\Service\UserGenerator;
use App\Entity\RegistrationTask;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/user")
 */
class UserController extends AbstractController
{
    private $userGenerator;
    private $userRepository;
    private $manager;

    public function __construct(UserGenerator $userGenerator, UserRepository $userRepository, EntityManagerInterface $manager)
    {
        $this->userGenerator = $userGenerator;
        $this->userGenerator->findAll();
        $this->userRepository = $userRepository;
        $this->manager = $manager;
    }

    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('dashboard/user/index.html.twig', [
            'users' => $this->userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, User $user): Response
    {
        // Créer un formulaire pour les propriétés dont la modification est permise ('userSkills')
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // Préparer les variables pour les tests
            $err = false;
            $array = [];
            
            foreach ($form->get('userSkills') as $form_userskill) { 
                // Récupérer le champ 'skill' pour chaque rang/élément de collection
                $formskill = $form_userskill->get('skill');

                // Tester si la compétence est présente dans la collection :
                // si elle n'est pas encore dans le tableau, l'ajouter
                // sinon renvoyer une erreur sur le champ
                if (!in_array($formskill->getData(), $array) ) {
                    $array[] = $formskill->getData();
                }  else {
                    $formskill->addError(new FormError("Cette valeur existe déjà."));
                    $err = true;   
                }
            }

            // ...
            
            // Si tous les tests sont passés
            // enregistrer les requêtes, et redirection
            if(!$err) {
                $this->manager->persist($user);

                dump($user);
                // die();
                $this->manager->flush();
                return $this->redirectToRoute('user_index');
            }
        }

        return $this->render('dashboard/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'editMode' => $user->getId() !== null,
        ]);
    }

    /**
     * @Route("/delete_userskill/{id}", name="user_skill_delete")
     */
    public function removeUserSkill(UserSkill $userSkill)
    {
        // Récupérer l'utilisateur avant de supprimer la compétence associée
        $user = $userSkill->getUser();
        $this->manager->remove($userSkill);
        $this->manager->persist($user);
        $this->manager->flush();

        return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
    }

    /**
     * @Route("/{user}/userteam/new", name="user_team_new", methods={"GET","POST"})
     */
    public function addUserTeam(Request $request, User $user): Response
    {
        $form = $this->createForm(UserTeamType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $arr_teams = [];
            $arr_userteams = [];

            foreach ($user->getUserTeams() as $ust) {
                $arr_teams[] = $ust->getTeam();
            }
            
            foreach ($form->get("teams")->getData() as $team) {

                if (!in_array($team, $arr_teams)){
                    $userTeam = new UserTeam();
                    $userTeam->setTeam($team);
                    $userTeam->setUser($user);
                    $user->addUserTeam($userTeam);
                    $arr_userteams[] = $team;
                }
            }

            if (count($arr_userteams) > 0 && count($arr_userteams) < 2) {
                $this->addFlash("success", "Le groupe « $team » a été ajouté avec succès.");
            } else if (count($arr_userteams) >= 2) {
                $arr_str = implode(' », « ', $arr_userteams);
                $this->addFlash("success", "Les groupes « $arr_str » ont été ajoutés avec succès.");
            }
            
            $this->manager->persist($user);
            $this->manager->flush();

            return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
        }

        return $this->render('user_team/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete_userteam/{id}", name="user_team_delete")
     */
    public function removeUserTeam(UserTeam $userTeam)
    {
        // Si la team implique un rôle admin, demander la confirmation.
        $user = $userTeam->getUser();
        $this->manager->remove($userTeam);
        $this->manager->persist($user);
        $this->manager->flush();
      
        return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
    }

    /**
     * @Route("/delete_registration/{id}", name="user_registration_delete")
     */
    public function removeRegistrationTask(RegistrationTask $registrationTask) {
        $user = $registrationTask->getUser();
        $this->manager->remove($registrationTask);
        $this->manager->flush();

        return $this->redirectToRoute('user_edit', ['id' => $user->getId()]);
    }
}
