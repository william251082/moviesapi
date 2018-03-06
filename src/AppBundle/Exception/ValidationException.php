<?php
/**
 * Created by PhpStorm.
 * User: williamdelrosario
 * Date: 3/6/18
 * Time: 2:32 PM
 */
namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ValidationException extends HttpException
{
    public function __construct(ConstraintViolationListInterface $constraintViolationList)
    {
        $message = [];

        /** @var  ConstraintViolationInterface $violation */
        foreach ($constraintViolationList as $violation)
        {
            $message[$violation->getPropertyPath()] = $violation->getMessage();
        }

        parent::__construct(400, json_encode($message));
    }
}