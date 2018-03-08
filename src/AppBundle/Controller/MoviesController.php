<?php
/**
 * Created by PhpStorm.
 * User: williamdelrosario
 * Date: 3/6/18
 * Time: 12:36 PM
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Movie;
use AppBundle\Entity\Role;
use AppBundle\Exception\ValidationException;
use FOS\RestBundle\Controller\ControllerTrait;
use FOS\RestBundle\Validator\Constraints\Regex;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\ConstraintViolationListInterface;

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

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("movie", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     */
    public function postMoviesAction(Movie $movie, ConstraintViolationListInterface $validationErrors)
    {
        if (count($validationErrors) > 0)
        {
            throw new ValidationException($validationErrors);
        }
        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->persist($movie);
        $em->flush();

        return $movie;
    }

    /**
     * @Rest\View()
     */
    public function deleteMovieAction(?Movie $movie)
    {
        if (null === $movie)
        {
            return $this->view(null, 404);
        }

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->remove($movie);
        $em->flush();
    }

    /**
     * @Rest\View()
     */
    public function getMovieAction(?Movie $movie)
    {
        if (null === $movie)
        {
            return $this->view(null,404);
        }

        return $movie;
    }

    /**
     * @Rest\View()
     */
    public function getMovieRolesAction(Movie $movie)
    {
        return $movie->getRoles();
    }

    /**
     * @Rest\View(statusCode=201)
     * @ParamConverter("role", converter="fos_rest.request_body")
     * @Rest\NoRoute()
     */
    public function postMovieRolesAction(Movie $movie, Role $role)
    {
        $role->setMovie($movie);

        $em = $this
            ->getDoctrine()
            ->getManager();

        $em->persist($role);
        $movie
            ->getRoles()
            ->add($role);

        $em->persist($movie);
        $em->flush();

        return $role;

    }

}