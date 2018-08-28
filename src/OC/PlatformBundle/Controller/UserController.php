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
use OC\PlatformBundle\Entity\AdvertRepository;


class userController extends Controller
{
   

    
  


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
     public function actualiteAction()
    {
        return $this->render('OCPlatformBundle:Advert:actualite.html.twig');
    }
}