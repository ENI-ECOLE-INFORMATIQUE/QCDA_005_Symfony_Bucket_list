<?php

namespace App\Controller;

use App\Service\SpreadSheetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

final class SpreadSheetController extends AbstractController
{
    #[Route('/export/excel', name: 'app_export_excel')]
    public function exportExcel(SpreadSheetService $spreadSheetService): BinaryFileResponse
    {
        //Créer le fichier via le service.
        $filePath = $spreadSheetService->createSampleSpreadSheet("Exemple Nom");

        //Retourner en téléchargement
        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT,'export.xlsx');
        return $response;
    }
}
