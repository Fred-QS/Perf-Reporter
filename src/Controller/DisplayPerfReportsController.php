<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Smile\Perfreporter\Performers\PerformancesLogger;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $logs = PerformancesLogger::getReportList();
        foreach ($logs as $log) {
            if ($log['id'] === $id) {
                return new Response(PerformancesLogger::getReport($log['path']));
            }
        }
        throw new NotFoundHttpException('This report does not exist');
    }
}