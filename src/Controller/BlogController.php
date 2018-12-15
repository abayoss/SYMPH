<?php
namespace App\Controller;

use App\Entity\Articles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class BlogController extends AbstractController
{
    /**
     * @Route("/", name="article_list")
     */
    public function index()
    {
        // return new Response('<html><body>hello</body></html>');
        // return $this->json([
        //     'message' => 'Welcome to your new controller!',
        //     'path' => 'src/Controller/BlogController.php',
        // ]);
        $articles= $this->getDoctrine()->getRepository(Articles::class)->findAll();

        return $this->render('blogs/index.html.twig',['articles'=>$articles]);
    } 
    
    /**
     * @Route("/article/new" ,name="new_article")
     * Method({"GET","POST"})
     */
    public function new(Request $request){
        $article = new Articles();

        $form = $this->createFormBuilder($article)
        ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('body', TextareaType::class, array(
          'required' => false,
          'attr' => array('class' => 'form-control')
        ))
        ->add('save', SubmitType::class, array(
          'label' => 'Ajouter',
          'attr' => array('class' => 'btn btn-primary mt-3')
        ))
        ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $article = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }
        return $this->render('blogs/new.html.twig', array(
            'form' => $form->createView()
          ));
    }

    /**
    * @Route("/article/edit/{id}", name="edit_blog")
    */
    public function edit($id,Request $request){
        $article = new Articles();
        $article = $this->getDoctrine()->getRepository(Articles::class)->find($id);

        $form = $this->createFormBuilder($article)
        ->add('title', TextType::class, array('attr' => array('class' => 'form-control')))
        ->add('body', TextareaType::class, array(
          'required' => false,
          'attr' => array('class' => 'form-control')
        ))
        ->add('save', SubmitType::class, array(
          'label' => 'Modifier',
          'attr' => array('class' => 'btn btn-primary mt-3')
        ))
        ->getForm();
        
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('article_list');
        }
        return $this->render('blogs/edit.html.twig',
            ['form' => $form->createView()]
          );
    }
    /**
     * @Route("/article/delete/{id}" )
     * @Method({"DELETE"})
     */
    public function delete(Request $request,$id){
        $article = $this->getDoctrine()
        ->getRepository(Articles::class)->find($id);
        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($article);
            $entityManager->flush();

            $response = new Response();
            $response->send();
    }

    /**
    * @Route("/article/{id}", name="show_blog")
    */
    public function show($id){
        $article = $this->getDoctrine()->getRepository(Articles::class)->find($id);
        
        return $this->render('blogs/show.html.twig',['article'=>$article]);
    }
}
