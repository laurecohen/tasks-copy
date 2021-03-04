<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Entity\TaskTemplate;
use App\Form\TaskTemplateType;
use App\Service\TaskGenerator;
use App\Entity\TaskTemplateSkill;
use App\Repository\TaskRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\TaskTemplateRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/task")
 */
class TaskTemplateController extends AbstractController
{
    private $taskGenerator;
    private $entityManager;

    public function __construct(TaskGenerator $taskGenerator, EntityManagerInterface $entityManager)
    {
        $this->taskGenerator = $taskGenerator; 
        $this->entityManager = $entityManager; 
    }

    /**
     * @Route("/template", name="task_template_index", methods={"GET"})
     */
    public function index(TaskTemplateRepository $taskTemplateRepository): Response
    {
        return $this->render('dashboard/task_template/index.html.twig', [
            'task_templates' => $taskTemplateRepository->findAll(),
        ]);
    }

    /**
     * @Route("/template/new", name="task_template_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        // Créer un nouvel objet taskTemplate, et le formulaire associé
        $taskTemplate = new TaskTemplate();
        $form = $this->createForm(TaskTemplateType::class); 
        $form->handleRequest($request);

        
        if ($form->isSubmitted() && $form->isValid()) {            
            
            // Préparer les variables pour les tests
            $err = false;   
            $skilllabels = [];
            
            foreach ($form->get('templateSkills') as $form_templateskill) {  
                $array = [];
                
                // Stocker la valeur des champs 'skill' et 'exp' dans un nouvel objet
                // pour s'assurer que le formulaire ne contient qu'une seule fois le couple skill/exp
                $formskill = $form_templateskill->get('skill');
                $formexp = $form_templateskill->get('exp');
                $obj = (object) ['skill' => "{$formskill->getData()}", 'exp' => "{$formexp->getData()}"];

                // vérifier si la paire skill/exp est trouvée dans le tabelau
                // si non, l'ajouter, sinon retourner une erreur sur les deux champs
                if (!in_array($obj, $array) ) {
                    $array[] = $obj;
                }  else {
                    $formskill->addError(new FormError("Cette valeur existe déjà."));
                    $formexp->addError(new FormError("Cette valeur existe déjà."));
                    $err = true;   
                } 
            
                // Tester si la valeur entrée pour chaque 'qty' est inférieure ou égale au nombre de participants max
                // si non, retourner une erreur sur le champ.
                if ($form_templateskill->get('qty')->getData() > $form->get('membersMax')->getData()) {
                    $form_templateskill->get('qty')->addError(new FormError("Cette valeur ne peut être supérieure au total des participants."));
                    $err = true;
                }

                // Stocker les éléments de collection et les labels dans deux tableaux
                // pour tester cette fois le cumul des champs 'qty'
                $rows[] = $form_templateskill;
                $skilllabels[] = $formskill->getData()->getLabel();
            }

            // Calculer la somme de tous les champs 'qty' pour une compétence :
            // utiliser le tableau associatif retourné par la fonction PHP array_count_values
            // pour filtrer le tableau et récupérer leur fréquence dans le formulaire
            foreach (array_count_values($skilllabels) as $key => $value) {
                
                // Préparer la variable
                $sum = 0; 

                // boucler une première fois pour calculer la somme des entrées 'qty' :
                // si une compétence est trouvée plusieurs fois, additionner les 'qty'
                foreach ($rows as $row) {
                    if ($value > 1 && $row->get('skill')->getData()->getLabel() === $key) {
                        $sum += $row->get('qty')->getData();
                    }
                }

                 // Boucler encore, pour récupérer toutes les 'qty' associées à une même compétence
                foreach ($rows as $row) {
                    if ($row->get('skill')->getData()->getLabel() === $key) {
                        // vérifier si la somme des 'qty' est inférieure ou égale au nombre de participants maximum,
                        // si non, retourner une erreur
                        if ($sum > $form->get('membersMax')->getData()) {
                            $err = true;
                            $row->get('qty')->addError(new FormError("La valeur cumulée ne peut être supérieure au total des participants."));
                        }
                    }
                }
            }       

            foreach ($taskTemplate->getTemplateSkills() as $templateSkill) {  
                // Supprimer l'élément si 'qty' vaut zéro
                if ($templateSkill->getQty() === 0) $taskTemplate->removeTemplateSkill($templateSkill);
            }

            // Si tous les tests sont passés...
            if (!$err) {
                // @TODO: vérifier si un template similaire existe,
                // Si oui, demander confirmation avant d'enregistrer

                // Préparer l'enregistrement et exécuter les requêtes
                $this->entityManager->persist($taskTemplate);
                $this->entityManager->flush();

                // Générer les tâches à partir du modèle nouvellement créé
                // si la récurrence est activée...
                $count = $this->taskGenerator->createTasks($taskTemplate);
                // $count = $this->taskGenerator->updateTasks($taskTemplate);
                $msg = $count > 0 ? "Le modèle et $count tâches été créés avec succès." : "Le modèle a été créé avec succès.";
                
                // Vider le flashBag avant la redirection (accumulation)
                $request->getSession()->getFlashBag()->clear();
                $this->addFlash('success', $msg);
                return $this->redirectToRoute('task_template_index');
            }
        }

        return $this->render('dashboard/task_template/new.html.twig', [
            'task_template' => $taskTemplate,
            'form' => $form->createView(),
            'editMode' => false
        ]);
    }

    /**
     * @Route("/template/{id}/edit", name="task_template_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TaskTemplate $taskTemplate): Response
    {     
        // Créer un nouvel objet taskTemplate, et le formulaire associé
        $form = $this->createForm(TaskTemplateType::class, $taskTemplate); 
        
        // Si le modèle a déjà servi à générer des tâches,
        // empêcher l'utilisateur de modifier la définition du modèle, en supprimant les éléments de formulaire
        if (count($taskTemplate->getTasks()) > 0) {
            $form->remove('category');
            $form->remove('dayOfWeek');
            $form->remove('startAt');
            $form->remove('endAt');
            
            // et renvoyer un message d'information
            $this->addFlash("warning", "L'édition du template est restreinte lorsqu'il a déjà servi à générer des tâches.");
        }
        
        $form->handleRequest($request);   
        
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Préparer les variables pour les tests
            $err = false;   
            $skilllabels = [];
            
            foreach ($form->get('templateSkills') as $form_templateskill) {  
                $array = [];
                
                // Stocker la valeur des champs 'skill' et 'exp' dans un nouvel objet
                // pour s'assurer que le formulaire ne contient qu'une seule fois le couple skill/exp
                $formskill = $form_templateskill->get('skill');
                $formexp = $form_templateskill->get('exp');
                $obj = (object) ['skill' => "{$formskill->getData()}", 'exp' => "{$formexp->getData()}"];

                // vérifier si la paire skill/exp est trouvée dans le tabelau
                // si non, l'ajouter, sinon retourner une erreur sur les deux champs
                if (!in_array($obj, $array) ) {
                    $array[] = $obj;
                }  else {
                    $formskill->addError(new FormError("Cette valeur existe déjà."));
                    $formexp->addError(new FormError("Cette valeur existe déjà."));
                    $err = true;   
                } 
            
                // Tester si la valeur entrée pour chaque 'qty' est inférieure ou égale au nombre de participants max
                // si non, retourner une erreur sur le champ.
                if ($form_templateskill->get('qty')->getData() > $form->get('membersMax')->getData()) {
                    $err = true;
                    $form_templateskill->get('qty')->addError(new FormError("Cette valeur ne peut être supérieure au total des participants."));
                }

                // Stocker les éléments de collection et les labels dans deux tableaux
                // pour tester cette fois le cumul des champs 'qty'
                $rows[] = $form_templateskill;
                $skilllabels[] = $formskill->getData()->getLabel();
            }

            // Calculer la somme de tous les champs 'qty' pour une compétence :
            // utiliser le tableau associatif retourné par la fonction PHP array_count_values
            // pour filtrer le tableau et récupérer leur fréquence dans le formulaire
            foreach (array_count_values($skilllabels) as $key => $value) {
                
                // Préparer la variable
                $sum = 0; 
                
                // boucler une première fois pour calculer la somme des entrées 'qty' :
                // si une compétence est trouvée plusieurs fois, additionner les 'qty'
                foreach ($rows as $row) {
                    if ($value > 1 && $row->get('skill')->getData()->getLabel() === $key) {
                        $sum += $row->get('qty')->getData();
                    }
                }

                 // Boucler encore, pour récupérer toutes les 'qty' associées à une même compétence
                 foreach ($rows as $row) {
                    if ($row->get('skill')->getData()->getLabel() === $key) {
                        // vérifier si la somme des 'qty' est inférieure ou égale au nombre de participants maximum,
                        // si non, retourner une erreur
                        if ($sum > $form->get('membersMax')->getData()) {
                            $row->get('qty')->addError(new FormError("La valeur cumulée ne peut être supérieure au total des participants."));
                            $err = true;
                        }
                    }
                }
            }       

            foreach ($taskTemplate->getTemplateSkills() as $templateSkill) {  
                // Supprimer l'élément si 'qty' vaut zéro
                if ($templateSkill->getQty() === 0) $taskTemplate->removeTemplateSkill($templateSkill);
            }

            // Si tous les tests sont passés...
            if (!$err) {
                // @TODO: vérifier si un template similaire existe,
                // Si oui, demander confirmation avant d'enregistrer

                // Exécuter les requêtes
                $this->entityManager->flush();
                $msg = "Le modèle a été modifié avec succès.";
                
                // et éditer les tâches déjà générées, s'il y en a
                if (count($taskTemplate->getTasks()) > 0) {
                    $count = $this->taskGenerator->updateTasks($taskTemplate);
                    $msg = "Le modèle et $count tâches ont été mis à jour.";
                }
                
                // Vider le flashBag avant la redirection (accumulation)
                $request->getSession()->getFlashBag()->clear();
                $this->addFlash('success', $msg);
                return $this->redirectToRoute('task_template_index');
            } 
        }
                 
        return $this->render('dashboard/task_template/edit.html.twig', [
            'task_template' => $taskTemplate,
            'form' => $form->createView(),
            'editMode' => true,
        ]);
    }

    /**
     * @Route("/delete_templateskill/{id}", name="template_skill_delete")
     */
    public function removeTemplateSkill(TaskTemplateSkill $templateSkill)
    {
        // Récupérer le template avant de supprimer la compétence associée
        $template = $templateSkill->getTaskTemplate();
        $this->entityManager->remove($templateSkill);
        $this->entityManager->flush();

        return $this->redirectToRoute('task_template_edit', ['id' => $template->getId()]);
    }

    /**
     * @Route("/", name="task_index", methods={"GET","POST"})
     */
    public function taskIndex(TaskTemplateRepository $taskTemplateRepository, TaskRepository $taskRepository): Response
    {
        // Rafraîchir la liste des tâches, avant d'interroger la bdd
        foreach ($taskTemplateRepository->findAll() as $template) {
            $this->taskGenerator->createTasks($template);
        }

        return $this->render('dashboard/task/index.html.twig', [
            'tasks' => $taskRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_more")
     */
    public function taskMore(Task $task): Response
    {
        // Afficher le détail d'une tâche depuis le tableau de bord
        return $this->render('dashboard/task/more.html.twig', [
            'task' => $task
        ]);
    }
}