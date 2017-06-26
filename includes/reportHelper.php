<?php 
    include('../../includes/helpers.php');
    require_once 'PHPExcel/Classes/PHPExcel.php';
    require_once 'PHPExcel/Classes/PHPExcel/IOFactory.php';

    define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

    function CreateReport($header,$data,$reportType,$filename,$type="Product"){
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator($_SESSION["CompanyName"]);

        $objPHPExcel->getActiveSheet()->setTitle("Report");
        $objPHPExcel->getActiveSheet()->mergeCells('A1:B1');
        $objPHPExcel->getActiveSheet()->mergeCells('C1:L1');
        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(60);
        $objPHPExcel->getActiveSheet()->SetCellValue('C1', $_SESSION["CompanyName"]);
        $objPHPExcel->getActiveSheet()->getStyle("C1")->getFont()->setBold(true)->setSize(20); 
        $objPHPExcel->getActiveSheet()->getStyle("A1:C1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);

        $objDrawing = new PHPExcel_Worksheet_Drawing();
        $objDrawing->setName($_SESSION["CompanyName"]);
        $objDrawing->setDescription($_SESSION["CompanyName"]);
        $logo = '../../images/'.$_SESSION["Logo"]; 
        $objDrawing->setPath($logo); 
        $objDrawing->setCoordinates('A1');
        $objDrawing->setHeight(75); 
        $objDrawing->setWorksheet($objPHPExcel->getActiveSheet()); 

        $columnarray = createColumnsArray('BZ');
        $cols=0;
        $rowCount =2;
        foreach($header as $head){
            $objPHPExcel->getActiveSheet()->SetCellValue($columnarray[$cols].$rowCount, $head); 
            $cols++;
        }

        $rowCount++;

        if($type=="Product" || $type=="ProductHistory" || $type=="date"){
            foreach($data as $datum){
                $cols=0;            
                foreach($header as $key){
                    $objPHPExcel->getActiveSheet()->SetCellValue($columnarray[$cols].$rowCount, $datum[$key]); 
                    $cols++;
                }
                $rowCount++;
            }
        }
        else if($type=="Overview"){
            foreach($data as $datum => $value){
                $cols=0;            
                $objPHPExcel->getActiveSheet()->SetCellValue($columnarray[$cols].$rowCount, $datum); 
                $cols++;
                $objPHPExcel->getActiveSheet()->SetCellValue($columnarray[$cols].$rowCount, $value); 
                $cols++;              
                $rowCount++;
            }            
        }      

        foreach (range(0, count($header)) as $col) {
            $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn($col)->setAutoSize(true);                
        }

        $range = "A1:".$columnarray[$cols]."1";
        $objPHPExcel->getActiveSheet()
            ->getStyle($range)
            ->getNumberFormat()
            ->setFormatCode( PHPExcel_Style_NumberFormat::FORMAT_TEXT );

        $objPHPExcel->getDefaultStyle()
            ->getAlignment()
            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

        $range = "A2:".$columnarray[$cols]."2";
        $objPHPExcel->getActiveSheet()
            ->getStyle($range)
            ->getFont()->setBold(true)
            ->setSize(12);             

        if($reportType=="excel"){
            $filename = $filename.".xlsx";
            $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: attachment;filename={$filename}");
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output');
            exit();
        }
        if($reportType=="csv"){
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
            header('Content-Type: application/csv');
            header('Content-Disposition: attachment;filename="'.$filename.'.csv"');
            header('Cache-Control: max-age=0');

            $objWriter->save('php://output');
            exit();
        }
        if($reportType=="pdf"){
            $rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
            $rendererLibraryPath = '../../includes/PHPExcel/MPDF57/';
            if (!PHPExcel_Settings::setPdfRenderer(
                    $rendererName,
                    $rendererLibraryPath
                )) {
                die(
                    'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
                    '<br />' .
                    'at the top of this script as appropriate for your directory structure'
                );
            }

            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment;filename="'.$filename.'.pdf"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
            $objWriter->save('php://output');
            exit();
        }
    }
?>