<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Integration\TestApp\Controller;

use \Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;

class ProfileAnnotationController extends Controller
{
    /**
     * @ValidatorProfile(profileName="testProfileName")
     *
     * @return Response
     */
    public function postAction(): Response
    {
        return new JsonResponse();
    }
}
