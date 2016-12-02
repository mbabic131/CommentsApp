<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Author;
use AppBundle\Entity\Comment;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="authors_list")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $authors_list = $em->getRepository('AppBundle:Author')->findBy(array(), array('name' => 'ASC'));

        return $this->render('authors_list.html.twig', array('authors_list' => $authors_list));
    }

    /**
     * Show page for creating new comment.
     *
     * @Route("add-comment", name="add_comment")
     * @Method("GET")
     */
    public function showAction()
    {
        return $this->render('add_comment.html.twig');
    }

    /**
     * Store new commnent.
     *
     * @Route("store-comment", name="store_comment")
     * @Method("POST")
     */
    public function storeAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $author_name = trim($request->request->get('author_name'));
        $comment_text = $request->request->get('comment_text');

        if(empty($author_name) || empty($comment_text)) {
            $request->getSession()
                ->getFlashBag()
                ->add('valMessg', 'Ime autora i komentar su obavezni.');

            return $this->redirectToRoute('add_comment');
        }

        $author = $em->getRepository('AppBundle:Author')->findOneBy(['name' => $author_name]);

        if (empty($author)) {
            $author = new Author();
            $author->setName($author_name);
            $author->setSlug($em->getRepository('AppBundle:Author')->setSlug($author_name));
            $em->persist($author);
        }

        $new_comment = new Comment();
        $new_comment->setText($comment_text);
        $new_comment->setAuthor($author);

        $em->persist($new_comment);
        $em->flush();

        return $this->redirectToRoute('add_comment');
    }

    /**
     * Show authors comments
     * @Route("/{slug}")
     * @Method("GET")
     */
    public function authorCommentsAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $author = $em->getRepository('AppBundle:Author')->findOneBy(['slug' => $slug]);

        if (empty($author)) {
            throw $this->createNotFoundException('Stranica nije pronađena.');
        }
        else {
            $comments = $author->getComments();
            return $this->render('author_comments.html.twig', array('author' => $author, 'comments' => $comments));
        }
    }

}
