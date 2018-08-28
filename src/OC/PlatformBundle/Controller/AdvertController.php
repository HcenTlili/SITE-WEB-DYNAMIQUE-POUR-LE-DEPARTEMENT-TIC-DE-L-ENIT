<?php

// src/OC/PlatformBundle/Controller/AdvertController.php

namespace OC\PlatformBundle\Controller;

// N'oubliez pas ce use :
use OC\PlatformBundle\Form\ApplicationType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Entity\Image;
use OC\PlatformBundle\Entity\Application;
use OC\PlatformBundle\Form\AdvertType;
use OC\PlatformBundle\Form\UserType;
use OC\PlatformBundle\Entity\AdvertRepository;
use OC\PlatformBundle\Entity\User;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // Pour récupérer la liste de toutes les annonces : on utilise findAll()
        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository('OCPlatformBundle:Advert')
            ->findAll()
        ;

        // L'appel de la vue ne change pas
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }
/* ancieeennnnn
    public function viewAction($id)
    {
        $advert = array(
            'title'   => 'Recherche développpeur Symfony2',
            'id'      => $id,
            'author'  => 'Alexandre',
            'content' => 'Nous recherchons un développeur Symfony2 débutant sur Lyon. Blabla…',
            'date'    => new \Datetime()
        );

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert' => $advert
        ));
    }

    */

    /**
     * @param Request $request
     * @return Response
     */
    public function viewAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'annonce $id
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);


        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id  n'existe pas, d'où ce if :
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }


        $application = new Application();
        $form = $this->createForm(new ApplicationType(), $application);
        $listApplications = $em
            ->getRepository('OCPlatformBundle:Application')
            ->findBy(array('advert' => $advert))
        ;

        if ($form->handleRequest($request)->isValid()) {
            $application->setAdvert($advert);
            $em = $this->getDoctrine()->getManager();
            $em->persist($application);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Commentaire bien enregistrée.');
            // On récupère la liste des candidatures de cette annonce


            return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert'           => $advert,
            'form' => $form->createView(),
            'listApplications' => $listApplications
        ));

        }



        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert'           => $advert,
            'form' => $form->createView(),
            'listApplications' => $listApplications
        ));
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $advert = new Advert();
        $form = $this->createForm(new AdvertType(), $advert);

        if ($form->handleRequest($request)->isValid()) {
            $advert->getImage()->upload();
            $em = $this->getDoctrine()->getManager();
            $em->persist($advert);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien enregistrée.');

            return $this->redirect($this->generateUrl('oc_platform_view', array('id' => $advert->getId())));
        }

        // À ce stade :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));

    }

    /*public function viewAction($id)
    {
        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'id' => $id
        ));
    }
*/
    /*
       public function viewAction($id)
       {
           return new JsonResponse(array('id' => $id));
       }
    */
    /*
        public function viewAction($id, Request $request)
        {
            // Récupération de la session

            $session = $request->getSession();



            // On récupère le contenu de la variable user_id

            $userId = $session->get('user_id');


            // On définit une nouvelle valeur pour cette variable user_id

            $session->set('user_id', 91);


            // On n'oublie pas de renvoyer une réponse

            return new Response("<body>Je suis une page de test, je n'ai rien à dire  </body>".$userId);
            /*
            // On récupère notre paramètre tag
            $tag = $request->query->get('tag');

            // On utilise le raccourci : il crée un objet Response
            // Et lui donne comme contenu le contenu du template
            return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
                'id'  => $id,
                'tag' => $tag,
            ));*/
    /*  }
  */




    public function viewSlugAction($slug, $year, $format)
    {
        return new Response(
            "On pourrait afficher l'annonce correspondant au
            slug '".$slug."', créée en ".$year." et au format ".$format."."
        );
    }

    public function editAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // Ici encore, il faudra mettre la gestion du formulaire

        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirectToRoute('oc_platform_view', array('id' => $advert->getId()));
        }

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
            'advert' => $advert
        ));
    }
    /* ancien methode
    public function editAction($id, Request $request)
    {
        // Ici, on récupérera l'annonce correspondante à $id

        // Même mécanisme que pour l'ajout
        if ($request->isMethod('POST')) {
            $request->getSession()->getFlashBag()->add('notice', 'Annonce bien modifiée.');

            return $this->redirectToRoute('oc_platform_view', array('id' => 5));
        }

        return $this->render('OCPlatformBundle:Advert:edit.html.twig');
    }
*/

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        $em->remove($advert);
        $em->flush();


        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository('OCPlatformBundle:Advert')
            ->findAll()
        ;

        // L'appel de la vue ne change pas
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
    }

    public function menuAction($limit)
    {
        $em = $this->getDoctrine()->getManager(); 

        $listAdverts = $em->getRepository('OCPlatformBundle:Advert')->findBy(
            array(),                 // Pas de critère
            array('date' => 'desc'), // On trie par date décroissante
            $limit,                  // On sélectionne $limit annonces
            0                        // À partir du premier
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            'listAdverts' => $listAdverts
        ));
    }
    
     public function ticAction()
    {
        return $this->render('OCPlatformBundle:Advert:tic.html.twig');
    }
    public function emploisAction()
    {
        return $this->render('OCPlatformBundle:Advert:emplois.html.twig');
    }
     public function acceuilAction()
    {
        return $this->render('OCPlatformBundle:Advert:acceuil.html.twig');
    }
    public function formauthAction(Request $request)
    {
         $user = new User();
        $form = $this->createForm(new UserType(), $user);
$data = $form->getData();
        
        
        $form->handleRequest($request);
        if (($form->getData()->getName()=='admin')&&($form->getData()->getId()=='0')) {
            
            return $this->redirect($this->generateUrl('oc_platform_acceuil', array('id' => $user->getId(),'name' => $user->getName())));
        }

        // À ce stade :
        // - Soit la requête est de type GET, donc le visiteur vient d'arriver sur la page et veut voir le formulaire
        // - Soit la requête est de type POST, mais le formulaire n'est pas valide, donc on l'affiche de nouveau
        return $this->render('OCPlatformBundle:Advert:formauth.html.twig', array(
            'form' => $form->createView(),
        ));
    }
     public function actualiteAction()
    {
        return $this->render('OCPlatformBundle:Advert:actualite.html.twig');
    }
    
    
    public function affichageAction()
    {
        

        // Pour récupérer la liste de toutes les annonces : on utilise findAll()
        $listAdverts = $this->getDoctrine()
            ->getManager()
            ->getRepository('OCPlatformBundle:Advert')
            ->findAll()
        ;

        // L'appel de la vue ne change pas
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => $listAdverts,
        ));
       
    }
   
}