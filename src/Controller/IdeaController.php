<?php

Namespace App\Controller;

use App\Entity\Idea;
use App\Entity\Vote;
use App\Form\IdeaType;
use Doctrine\ORM\EntityManagerInterface;
use \Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use \Symfony\Component\HttpFoundation\Request;

Class IdeaController extends AbstractController
{

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $userId = 0;
        if ($this->getUser()) {
            $userId = $this->getUser()->getId();
        }

        //Get the ideas from database

        $ideaRepo = $this->getDoctrine()->getRepository(Idea::class);
        $ideas = $ideaRepo->findIdeasOrderPerNote($userId);

        //dump($ideas);

        return $this->render("idea/home.html.twig", [
            "ideas" => $ideas
        ]);
    }

    /**
     * @Route("/idea/{id}", name="idea_detail",
     *     requirements={"id": "\d*"})
     */
    public function detail($id)
    {
        // Acces denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Get the idea from database
        $ideaRepo = $this->getDoctrine()->getRepository(Idea::class);
        $idea = $ideaRepo->find($id);

        // error if not a valide id
        if (empty($idea)) {
            throw $this->createNotFoundException("Cette idée n'éxiste pas");
        }

        return $this->render("idea/details.html.twig", [
            "idea" => $idea
        ]);
    }

    /**
     * @Route("/idea/add", name="idea_add")
     */
    public function add(EntityManagerInterface $em, Request $request)
    {

        // Acces denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        // Form to register an idea
        $idea = new Idea();
        $idea->setDatecreated(new \DateTime());
        $idea->setAuthor($this->getUser()->getUsername());

        $ideaForm = $this->createForm(IdeaType::class, $idea);

        // Get the data from the form
        $ideaForm->handleRequest($request);
        if ($ideaForm->isSubmitted() && $ideaForm->isValid()) { //validation checked and csrf_protection: activated

            // Save the data
            $em->persist($idea);
            $em->flush();

            $this->addFlash('success', 'L\'idée a été sauvegardée');
            return $this->redirectToRoute('idea_detail', ['id' => $idea->getId()]);
        }

        return $this->render("idea/add.html.twig", [
            "ideaForm" => $ideaForm->createView(),
            "idea" => $idea
        ]);
    }

    /**
     * @Route("/idea/delete/{id}", name="idea_delete", requirements={"id":"\d+"})
     */
    public function delete($id, EntityManagerInterface $em)
    {
        // Acces denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        $ideaRepo = $this->getDoctrine()->getRepository(Idea::class);
        $idea = $ideaRepo->find($id);

        $em->remove($idea);
        $em->flush();

        $this->addFlash('success', "L'idée a été supprimée");
        return $this->redirectToRoute('home');


    }

    /**
     * @Route("/idea/update/{id}", name="idea_update", requirements={"id":"\d+"})
     */
    public function update($id, EntityManagerInterface $em, Request $request)
    {
        // Acces denied if user not connected
        $this->denyAccessUnlessGranted("ROLE_USER");

        $ideaRepo = $this->getDoctrine()->getRepository(Idea::class);
        $idea = $ideaRepo->find($id);

        $ideaForm = $this->createForm(IdeaType::class, $idea);

        $ideaForm->handleRequest($request);
        if ($ideaForm->isSubmitted() && $ideaForm->isValid()){
            $em->persist($idea);
            $em->flush();

            $this->addFlash('success', 'L\'idée a été modifiée');
            return $this->redirectToRoute('idea_detail', ['id' => $idea->getId()]);
        }

        return $this->render('idea/add.html.twig', [
            "ideaForm" => $ideaForm->createView(),
            "idea" => $idea
        ]);

    }

    /**
     * @Route("/idea/thumbUp/{id}", name="idea_thumbUp", requirements={"id":"\d+"})
     */
    public function thumbUp($id, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $ideaRepo = $this->getDoctrine()->getRepository(Idea::class);
        $idea = $ideaRepo->find($id);

        $idea->setThumbUp($idea->getThumbUp()+1);
        $idea->setNote($idea->getThumbUp() - $idea->getThumbDown());

        $em->persist($idea);
        $em->flush();

        $this->addVote($em, $idea);

        $this->addFlash('success', 'Vous avez voté');
        return $this->redirectToRoute('home');
    }

    /**
     * @Route("/idea/thumbDown/{id}", name="idea_thumbDown", requirements={"id":"\d+"})
     */
    public function thumbDown($id, EntityManagerInterface $em, Request $request)
    {
        $this->denyAccessUnlessGranted("ROLE_USER");

        $ideaRepo = $this->getDoctrine()->getRepository(Idea::class);
        $idea = $ideaRepo->find($id);

        $idea->setThumbDown($idea->getThumbDown()+1);
        $idea->setNote($idea->getThumbUp() - $idea->getThumbDown());

        $em->persist($idea);
        $em->flush();

        $this->addVote($em, $idea);

        $this->addFlash('success', 'Vous avez voté');
        return $this->redirectToRoute('home');
    }

    public function addVote(EntityManagerInterface $em, $idea){

        $vote = new Vote();

        //Set the data
        $vote->setUser($this->getUser());
        $vote->setIdea($idea);

        // Save the data
        $em->persist($vote);
        $em->flush();
    }
}
