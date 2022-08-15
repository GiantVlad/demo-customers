<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Image;
use App\Model\ImageUploadApiModel;
use App\Repository\ContactRepository;
use App\Repository\ImageRepository;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/contact', name: 'client_')]
class ContactController extends AbstractApiController
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly ValidatorInterface $validator,
        private readonly ImageRepository $imageRepository,
        private readonly SerializationContextFactoryInterface $serializationContextFactory,
    ) {
    }

    #[Route('/{contact}', name: 'get_one', requirements: ['customer' => '\d+'], methods: [Request::METHOD_GET])]
    #[OA\Response(
        response: 200,
        description: 'Returns the Contact by id',
        content: new Model(
            type: Contact::class, groups: ['contact']
        ),
    )]
    #[ParamConverter('contact', converter: 'doctrine.orm',)]
    public function getOne(Contact $contact): JsonResponse
    {
        $serializerContext = $this->serializationContextFactory
            ->createSerializationContext()
            ->setGroups(['Default', 'getters']);

        return $this->okResponse($contact, $serializerContext);
    }

    #[Route('/', name: 'get_list', methods: [Request::METHOD_GET])]
    public function getList(): JsonResponse
    {
        $contacts = $this->contactRepository->findAll();

        $serializerContext = $this->serializationContextFactory
            ->createSerializationContext()
            ->setGroups(['Default', 'getters']);

        return $this->okResponse($contacts, $serializerContext);
    }

    #[Route('/', name: 'create', methods: [Request::METHOD_POST])]
    #[OA\Response(
        response: 200,
        description: 'Returns created a new Contact',
        content: new Model(
            type: Contact::class,
        ),
    )]
    #[OA\RequestBody(
        request: "contact",
        required: true,
        content: new OA\JsonContent(
            ref: new Model(
                type: Contact::class, groups: ['createContact']
            )
        )
    )]
    #[ParamConverter('contact', converter: 'serializer_converter')]
    public function create(Contact $contact)
    {
        $errors = $this->validator->validate($contact);
        if ($errors->count() > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }
        $contact = $this->contactRepository->create($contact);

        $serializerContext = $this->serializationContextFactory
            ->createSerializationContext()
            ->setGroups(['Default', 'createContact', 'getters']);

        return $this->okResponse($contact, $serializerContext);
    }

    #[OA\RequestBody(
        content: [
            new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [
                        new OA\Property(property: 'file', type: "string", format: "binary"),
                    ],
                    type: "object",
                )
            ),
        ],
    )]
    #[Route('/{contact}/image', name: 'upload-img', methods: [Request::METHOD_POST])]
    #[ParamConverter('image', converter: 'serializer_converter')]
    #[ParamConverter('contact', converter: 'doctrine.orm')]
    public function upload(Image $image, Contact $contact): JsonResponse
    {
        $this->imageRepository->create($image);
        $contact->setImage($image);
        $this->contactRepository->save();
        return $this->okResponse($image);
    }
}
