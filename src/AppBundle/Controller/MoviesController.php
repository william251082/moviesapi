<?php
/**
 * Created by PhpStorm.
 * User: williamdelrosario
 * Date: 3/6/18
 * Time: 12:36 PM
 */

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\ControllerTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;

class MoviesController extends AbstractController
{
    use ControllerTrait;

    /**
     * @Rest\View()
     */
    public function getMoviesAction()
    {
        $movies = $this
            ->getDoctrine()
            ->getRepository('AppBundle:Movie')
            ->findAll();

            return $movies;
    }

}