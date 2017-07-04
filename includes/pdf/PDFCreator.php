<?php


    // Include the main TCPDF library (search for installation path).
    include('reports/tcpdf_include.php');
    include('tcpdf.php');
set_time_limit(600);
    function PdfReportGeneration($header,$data,$type,$filename){

    // create new PDF document
    if(count($header)<=7)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
    }
    else if(count($header)<=11)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A3', true, 'UTF-8', false);
    }
    else if(count($header)<=20)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A2', true, 'UTF-8', false);
    }
    else if(count($header)<=38)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A1', true, 'UTF-8', false);
    }

    // set document information
    $pdf->SetCreator(PDF_CREATOR);

    // set default header data
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "", "", array(0,64,255), array(0,64,128));
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    // set default monospaced font
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    // set margins
    $pdf->SetMargins(PDF_MARGIN_LEFT, '50', PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    // set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    // set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set some language-dependent strings (optional)
    if (@file_exists('includes/reports/pdf/reports/lang/eng.php')) {
        require_once('includes/reports/pdf/reports/lang/eng.php');
        $pdf->setLanguageArray($l);
    }

    // ---------------------------------------------------------

    // Add a page
    // This method has several options, check the source code documentation for more information.
    $pdf->AddPage();


        $html=$html.'<table cellpadding="3" cellspacing="0" border="0" width="100%"
                    style="width:100%;font-size:9px;font-family:Arial, Helvetica, sans-serif;color:#333333;
                    text-align:left;border-collapse:collapse;">
                    <tr><td align="center" colspan="'.count($header).'" style="font-size:15px;font-weight:bold">'.$type.' Report</td></tr>
                    <tr>';
        $html=$html.'<th width="40" style="background-color:#5BA11D;color:#FFF;padding:7px;font-weight: bold;">S.No</th>';

        for ($i=0;$i<count($header);$i++)
        {
            $html =$html.'<th style="background-color:#5BA11D;color:#FFF;padding:2px;font-weight: bold;">'.$header[$i].'</th>';

        }

        $html =$html.'</tr>';
        $cnt=0;
        if($type=="Product" || $type=="ProductHistory" || $type=="date"){
            foreach($data as $datum){
                $cnt++;
                if($cnt%2==0)
                    $class='style="background-color:#cfeae5;"';
                else
                    $class='style="background-color:#FFFFFF;"';

                $html =$html.'<tr '.$class.'>';
                foreach($header as $key){
                    $html =$html.'<td style="padding:2px;">'.$datum[$key].'</td>';
                }

                $html =$html.'</tr>';
            }
        }
        else if($type=="Overview"){
            foreach($data as $datum => $value){
                $cnt++;
                if($cnt%2==0)
                    $class='style="background-color:#cfeae5;"';
                else
                    $class='style="background-color:#FFFFFF;"';

                $html =$html.'<tr '.$class.'>';

                $html =$html.'<td style="padding:2px;">'.$datum.'</td>';
                $html =$html.'<td style="padding:2px;">'.$value.'</td>';

                $html =$html.'</tr>';
            }            
        }  

        $html=$html .'</table>';
    

    // echo $html;
    // exit();
ob_clean();
    // Print text using writeHTMLCell()
    $pdf->writeHTML($html, true, false, true, false, '');


    $pdf->Output($filename, 'I');

    $pdf->Close();

    }

?>
