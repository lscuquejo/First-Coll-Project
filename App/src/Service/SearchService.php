<?php

namespace App\Service;

use CCT\Bundle\ResourceBundle\ExceptionHandler\Exception\HttpException;
use CCT\Bundle\ResourceBundle\ExceptionHandler\Model\HttpError;
use Symfony\Component\HttpFoundation\Response;

class SearchService
{
    public function searchByCriteria($content, $criteria, $entityRepository)
    {
        $criterion = [];

        foreach ($criteria as $field) {
            if (!empty($content[$field])) {
                $criterion[$field] = $content[$field];
            }
        }

        $searchedResult = $entityRepository->findBy($criterion);

        if (empty($searchedResult)) {
            throw new HttpException(new HttpError(Response::HTTP_NOT_FOUND, 'Nothing was found on this search'));
        }

        return $searchedResult;
    }
}
