<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class McoloController extends AbstractController
{
    /**
     * @Route("/blog", name="main")
     */
    public function index()
    {   
        $repo = $this->getDoctrine()->getRepository(Article::class);

        $articles = $repo->findAll();

        return $this->render('mcolo/index.html.twig', [
            'controller_name' => 'McoloController',
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/blog/new", name="blog_create") 
     * @Route("/blog/{id}/edit", name="blog_edit")
     */
    public function form(Article $article =null, Request $request, ObjectManager $manager)
    {
        //Je verifie si l'article est deferend de NULL. j'en initialiseun nouveaux si non
        if (!$article){
            $article = new Article();
        }
        //JE CREE LES FORM LIEE A L'ENTITY ARTICLE
        $form=$this->createForm(ArticleType::class, $article);
        //JE SURVEILLER LE $request SI IL CONTIENT DES DONNEES
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //JE PERSISTE ET J'ENREGISTRE DANS LA BASE DE DONNEE
            $manager->persist($article);
            $manager->flush();
            //JE RETOURNE LA VUE DE LA METHODE SANS OUBLIER DE CREER UN VIEW POUR LE FORM
            //DANS LE TABLEAU DE PARAMETRE
            return $this->redirectToRoute('show', ['id'=>$article->getId()]);            
        }
        return $this->render('mcolo/form.html.twig', [
            'formArticle'=>$form->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }


    /**
     * @Route("/blog_show/{id}", name="show")
     */
    public function home($id, Request $request, ObjectManager $manager)
    {   
        $faker = \Faker\Factory::create('fr_FR');

        $comment = new Comment();
        
        $repo = $this->getDoctrine()->getRepository(Article::class);
        $article =$repo->find($id);

        
        $form=$this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $comment->setAuthor($faker->name)
                    ->setArticle($article);
            $manager->persist($comment);
            $manager->flush();            

            $comment = new Comment();
             
            return $this->redirectToRoute('show', ['id'=>$article->getId()]);   
        }       
        return $this->render('mcolo/home.html.twig',[
            'article' => $article,
            'formComment' =>$form->createView()
        ]);
    }    
}
