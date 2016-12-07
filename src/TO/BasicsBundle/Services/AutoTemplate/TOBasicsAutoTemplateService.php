<?php

namespace TO\BasicsBundle\Services\AutoTemplate;

use Symfony\Component\HttpFoundation\RequestStack;

class TOBasicsAutoTemplateService
{
    private $request;
    private $defaultExtension;

    public function __construct(RequestStack $requestStack, $defaultExtension)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->defaultExtension = $defaultExtension;
    }

    public function getTemplateFileName()
    {
        preg_match(
            '/^(.*)\\\(.*)\\\Controller\\\(.*)Controller::(.*)Action$/',
            $this->request->attributes->get('_controller'),
            $matches
        );

        $bundleName = $matches[1] . $matches[2];
        $controllerName = $matches[3];
        $actionName = $matches[4];

        $templateFileName = sprintf(
            '%s:%s:%s.%s',
            $bundleName,
            $controllerName,
            $actionName,
            $this->defaultExtension
        );

        return $templateFileName;
    }
}