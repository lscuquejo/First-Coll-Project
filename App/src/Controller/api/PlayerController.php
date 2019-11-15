<?php

namespace App\Controller\Api;

use App\Entity\Player;
use App\Form\PlayerFormType;
use App\Repository\PlayerRepository;
use App\Service\ResponseHandlerService;
use CCT\Bundle\ResourceBundle\ExceptionHandler\Exception\HttpException;
use CCT\Bundle\ResourceBundle\ExceptionHandler\Model\HttpError;
use Doctrine\Common\Collections\Criteria;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LanguageController extends AbstractController
{
    /**
     * @var PlayerRepository
     */
    private $playerRepository;

    /**
     * @var ValidatorInterface
     */
    private $validatorService;

    /**
     * @var ResponseHandlerService
     */
    private $responseHandlerService;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * LanguageController constructor.
     */
    public function __construct(
        LanguageRepository $languageRepository,
        ValidatorInterface $validatorService,
        ResponseHandlerService $responseHandlerService,
        Serializer $serializer
    ) {
        $this->languageRepository = $languageRepository;
        $this->validatorService = $validatorService;
        $this->serializer = $serializer;
        $this->responseHandlerService = $responseHandlerService;
    }

    /**
     * @return Response
     */
    public function list()
    {
        $languages = $this->languageRepository->findBy([], ['name' => Criteria::ASC]);

        if (!$languages) {
            throw new HttpException(new HttpError(Response::HTTP_OK, 'There are no Languages registered to the database yet'));
        }

        return $this->json($languages, Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function get(string $uuid)
    {
        if (!Uuid::isValid($uuid)) {
            throw new HttpException(new HttpError(Response::HTTP_BAD_REQUEST, 'Uuid Not Valid'));
        }

        $language = $this->languageRepository
            ->find($uuid);

        if (!$language) {
            throw new HttpException(new HttpError(Response::HTTP_NOT_FOUND, "No Language found with this uuid = $uuid"));
        }

        return $this->json($language, Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function create(Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $language = new Language();

        $form = $this->createForm(LanguageFormType::class, $language);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($language);
            $entityManager->flush();

            return $this->responseHandlerService->successfullyCreated('Project', $language->getId());
        }

        return $this->json($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @return Response
     */
    public function delete(string $uuid)
    {
        if (!Uuid::isValid($uuid)) {
            throw new HttpException(new HttpError(Response::HTTP_BAD_REQUEST, 'Uuid Not Valid'));
        }

        $entityManager = $this->getDoctrine()->getManager();

        $language = $this->languageRepository
            ->find($uuid);

        if (!$language) {
            throw new HttpException(new HttpError(Response::HTTP_NOT_FOUND, "No Language found with this uuid = $uuid"));
        }

        $entityManager->remove($language);
        $entityManager->flush();

        return $this->responseHandlerService->successfullyDeleted('Language', $uuid);
    }

    /**
     * @return Response
     */
    public function update(string $uuid, Request $request)
    {
        $content = $request->getContent();

        $content = $this->serializer->decode($content, 'json');

        $entityManager = $this->getDoctrine()->getManager();

        $language = $this->languageRepository
            ->find($uuid);

        $form = $this->createForm(LanguageFormType::class, $language);

        $form->submit($content);

        if ($form->isSubmitted() && $form->isValid() && $language) {
            $entityManager->persist($language);
            $entityManager->flush();

            return $this->responseHandlerService->successfullyUpdated('Project', $language->getId());
        }

        return $this->json($form->getErrors(true), Response::HTTP_BAD_REQUEST);
    }
}
