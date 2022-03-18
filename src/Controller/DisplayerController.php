<?php

namespace Smile\PerfreporterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DisplayerController extends AbstractController
{
    #[Route('/perf-reporter', name: 'perf-reporter:display')]
    public function index() :Response
    {
        echo 'test';
    }
}