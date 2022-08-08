<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\Customer;
use App\Model\ImageUploadApiModel;
use App\Repository\ContactRepository;
use Aws\S3\S3Client;
use Gedmo\Sluggable\Util\Urlizer;
use JMS\Serializer\ContextFactory\SerializationContextFactoryInterface;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Operation;
use OpenApi\Annotations as AO;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/contact', name: 'client_')]
class ContactController extends AbstractApiController
{
    public function __construct(
        private readonly ContactRepository $contactRepository,
        private readonly ValidatorInterface $validator,
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
                    required: ['file', 'contact_id'],
                    properties: [
                        new OA\Property(property: 'file', type: "string", format: "binary"),
                        new OA\Property(property: 'contact_id', type: "integer")
                    ],
                    type: "object"
                )
            ),
        ],
    )]
    #[Route('/upload', name: 'upload', methods: [Request::METHOD_POST])]
    public function upload(Request $request, S3Client $s3Client): JsonResponse
    {
        /** @var UploadedFile $uploadedFile */
        $uploadedFile = $request->files->get('file');
        /** @var Contact $contact */
        $contact = $this->contactRepository->find($request->get('contact_id'));
//        $uploadModel = new ImageUploadApiModel();
//        $uploadModel->setFile($uploadedFile);
//        $uploadModel->setContact($contact);
        $destination = $this->getParameter('kernel.project_dir') . '/public/uploads';
        $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $newFilename = Urlizer::urlize($originalFilename) . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
        $uploadedFile->move(
            $destination,
            $newFilename
        );

        $contact->setImgUrl($newFilename);
        $this->contactRepository->save();

//        $violations = $this->validator->validate($uploadApiModel);
//        if ($violations->count() > 0) {
//            return $this->json($violations, 400);
//        }
        // dd($uploadApiModel);
        if ($request->headers->get('Content-Type') === 'application/json') {
            $uploadedFile = $request->getContent();
//            $uploadApiModel = $serializer->deserialize(
//                $request->getContent(),
//                ImageUploadApiModel::class,
//                'json'
//            );
        }

        return $this->json($uploadModel);
    }
}
