<?php

namespace App\Service;

use App\Entity\User;
use App\Entity\Member;
use App\Repository\UserRepository;
use App\Repository\MemberRepository;
use App\Repository\TeamRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserGenerator
{
    private $userRepository;
    private $memberRepository;
    private $teamRepository;
    private $manager;

    public function __construct(
        UserRepository $userRepository, 
        MemberRepository $memberRepository, 
        TeamRepository $teamRepository, 
        EntityManagerInterface $manager
    ) {
        $this->userRepository = $userRepository;
        $this->memberRepository = $memberRepository;
        $this->teamRepository = $teamRepository;
        $this->manager = $manager;
    }

    /**
     * Create or Update users
     */ 
    public function createUser(Member $member, User $user = null)
    {    
        // Si l'utilisateur n'existe pas, en créer un nouveau
        if (!$user) {
            $user = new User();

            // et initialiser le compte
            $user->setLogin($member->getLogin());
            $user->setRoles($user->getRoles());
            $user->setPassword($member->getPassword());
        }

        // hydrater l'utilisateur avec les props du member|newMember 
        $user->setTeams($member->getTeams());
        $user->setFirstName($member->getFirstName());
        $user->setLastName($member->getLastName());
        $user->setEmail($member->getEmail());
        $user->setPhone($member->getPhone());
        $user->setStreet($member->getStreet());
        $user->setZip($member->getZip());
        $user->setTown($member->getTown());
        $user->setYearOfBirth($member->getYearOfBirth());
        $user->setHasPass($member->getHasPass());
        $user->setHasValidAdhesion($member->getHasValidAdhesion());
        
        $this->manager->persist($user);
        $this->manager->flush();
    }

    /**
     * Find all users
     */ 
    public function findAll()
    {
        $users = $this->userRepository->findAll();
        $members = $this->memberRepository->findAll();
        
        $newMembers = [];
        $users_logins = [];
        
        // Parcourir le tableau des membres 
        // pour rechercher les nouveaux arrivants
        foreach ($members as $member) {

            // si pas d'utilisateurs, les ajouter le tableau des nouveaux
            // sinon...
            if (count($this->userRepository->findAll()) === 0) {
                $newMembers[] = $member;
            } else {
                // Parcourir la liste des users
                // et stocker les login pour les comparer aux membres
                foreach ($users as $user) {
                    $users_logins[] = $user->getLogin();
                    $users_logins = array_unique($users_logins);

                    // si member est déjà utilisateur, mettre à jour user
                    if ($member->getLogin() == $user->getLogin()) {
                        $this->createUser($member, $user);
                    }
                }

                // si un nouveau membre est trouvé, l'ajouter au tableau
                if (!in_array($member->getLogin(), $users_logins)) {
                    $newMembers[] = $member;
                }
            }
        }

        // Pour cheque newMember, créer un nouvel utilisateur
        foreach ($newMembers as $newMember) {
            $this->createUser($newMember);
        }

        // @TODO: Remove deleted members from users

        // Une fois tous les utilisateurs créés, définir les rôles
        foreach ($users as $user) {
            // récupérer le tableau des rôles
            $userRoles = $user->getRoles();
            
            // parcourir les équipes
            foreach ($user->getTeams() as $userTeam) {
                foreach ($this->teamRepository->findAll() as $team) {
                    // si une équipe a la propriété isAdmin à 'true'
                    // ajouter le rôle admin à l'utilisateur
                    if ($userTeam == $team && $team->getIsAdmin()) {
                        $userRoles[] = 'ROLE_ADMIN';
                    }
                }
            }
            $user->setRoles(array_unique($userRoles));
            $this->manager->flush();
        }

        return $users;
    }
}
