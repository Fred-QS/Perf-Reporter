<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Smile\Perfreporter\Performers\PerformancesLogger;

#[Route('/perf-reporter')]
class DisplayPerfReportsController extends AbstractController
{
    #[Route('', name: 'perf-reporter:list')]
    public function display() :Response
    {
        return new Response(PerformancesLogger::getReportList('html'));
    }

    #[Route('/{id}', name: 'perf-reporter:one')]
    public function report(string $id) :Response
    {
        return new Response($id);
    }
}