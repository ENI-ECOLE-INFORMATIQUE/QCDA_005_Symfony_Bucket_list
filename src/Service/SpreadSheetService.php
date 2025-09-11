<?php

namespace App\Service;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class SpreadSheetService
{
    public function createSampleSpreadSheet(string $title):string
    {
        //Création du spreadsheet
        $spreadsheet = new Spreadsheet();
        $activeWorksheet = $spreadsheet->getActiveSheet();
        $activeWorksheet->setTitle($title);

        //Ecrire des données
        //Ecrire les entetes
        $activeWorksheet->setCellValue('A1', 'Nom');
        $activeWorksheet->setCellValue('B1', 'Email');
        /* $activeWorksheet->setCellValue('A2', 'Jean Dupont');
           $activeWorksheet->setCellValue('B2', 'jean.dupont@exemple.com');

           $activeWorksheet->setCellValue('A3', 'Alice Martin');
           $activeWorksheet->setCellValue('B3', 'alice.martin@exemple.com');*/

        //Ecrire les datas
        $datas = [
            ['Jean Dupont','jean.dupont@exemple.com'],
            ['Alice Martin','alice.martin@exemple.com'],
            ['Bod Durant','bob.durant@exemple.com'],
        ];

        $row=2;
        foreach($datas as [$nom,$email]){
            $activeWorksheet->setCellValue("A$row", $nom);
            $activeWorksheet->setCellValue("B$row", $email);
            $row++;
        }

        //Styliser l'entete
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12,'color'=>['rgb' => 'FFFFFF']],
            'alignment' => ['horizontal'=>Alignment::HORIZONTAL_CENTER],
            'fill'=>[
                'fillType'=> Fill::FILL_SOLID,
                'startColor'=>['rgb' => '4F81BD'],
            ],
            'borders'=>[
                'allBorders'=>['borderStyle'=>Border::BORDER_THIN],
            ]
        ];
        $activeWorksheet->getStyle('A1:B1')->applyFromArray($headerStyle);

        //Bordure sur toutes les cellules contenant des données
        $dataRange = 'A1:B'.($row-1);
        $activeWorksheet->getStyle($dataRange)->applyFromArray(
            [
                'borders'=>[
                'allBorders'=>['borderStyle'=>Border::BORDER_THIN]
                ]
            ]
        );

        //Ajuster la largeur des colonnes
        foreach (range('A', 'B') as $col) {
            $activeWorksheet->getColumnDimension($col)->setAutoSize(true);
        }

        //Générer un fichier temporaire
        $tempfile =tempnam(sys_get_temp_dir(), 'excel_').'.xlsx';
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempfile);
        return $tempfile;
    }
}