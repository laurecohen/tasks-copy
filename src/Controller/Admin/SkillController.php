<?php

namespace App\Controller\Admin;

use App\Entity\Skill;
use App\Form\SkillType;
use App\Repository\SkillRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/skill")
 */
class SkillController extends AbstractController
{
    /**
     * @Route("/", name="skill_index", methods={"GET","POST"})
     */
    public function index(SkillRepository $skillRepository, Request $request, EntityManagerInterface $manager): Response
    {
        // Créer un nouvel objet Skill et le formulaire associé
        $skill = new Skill();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            // Préparer les variables pour les tests
            $err = false;
            
            // boucler sur les compétences existantes
            foreach ($skillRepository->findAll() as $sk) {
                // si un doublon est trouvé, renvoyer une erreur
                if ($form->get('label')->getData() ===  $sk->getLabel()) {
                    $form->get('label')->addError(new FormError("Cette valeur est déjà utilisée."));
                    $err = true;
                }
            }

            // Si tous les tests sont passés...
            if (!$err) {
                // Préparer l'enregistrement et exécuter les requêtes
                $manager->persist($skill);
                $manager->flush();

                // et vider le flashBag avant la redirection (accumulation)
                $request->getSession()->getFlashBag()->clear();
                $this->addFlash('success', "La compétence $skill a été ajoutée avec succès.");
                return $this->redirectToRoute('skill_index');
            }
        }

        return $this->render('dashboard/skill/index.html.twig', [
            'skills' => $skillRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }
}