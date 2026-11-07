<?php

namespace Pelso\OpenAPIValidatorBundle\Tests\Interceptor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Pelso\OpenAPIValidatorBundle\Annotation\ValidatorProfile;

class ProfileAnnotationController extends Controller
{
    /**
     * @ValidatorProfile(profileName = "v_profile_name")
     * */
    public function profileAnnotationAction(): Response
    {
        return new Response();
    }
}
