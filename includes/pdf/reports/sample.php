<?php

include("../../../connection.php");
include("../../../functions.php");

// Include the main TCPDF library (search for installation path).
require_once('tcpdf_include.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Nicola Asuni');
$pdf->SetTitle('TCPDF Example 001');
$pdf->SetSubject('TCPDF Tutorial');
$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING, array(0,64,255), array(0,64,128));
$pdf->setFooterData(array(0,64,0), array(0,64,128));

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// Add a page
// This method has several options, check the source code documentation for more information.
$pdf->AddPage();

for ($i=0;$i<count($_REQUEST['columns']);$i++)
{
    echo $_REQUEST['columns'][$i];
}
exit();



$sql = "SELECT * from studentregistration ";
$sql.= " order by Name LIMIT 0 , 30";
$res=mysql_query($sql);
$numrows=mysql_num_rows($res);

if($numrows>0)
{
    $html='<div align="center" style="color=#DB843D;font-size:20px;">Caste Report</div>';
    $html=$html.'<table cellpadding="3" cellspacing="0" border="0" width="100%" 
    style="width:100%;border:solid 1px #7accbb;font-size:12px;font-family:Arial, Helvetica, sans-serif;color:#333333;
    text-align:left;border-collapse:collapse;">
    <tr>
        <th width="50" style="background-color:#5BA11D;color:#FFF;padding:7px;font-weight: bold;">S.No</th>
        <th style="background-color:#5BA11D;color:#FFF;padding:7px;font-weight: bold;">Name</th>
        <th style="background-color:#5BA11D;color:#FFF;padding:7px;font-weight: bold;">Gender</th>
    </tr>';
    $cnt=0;
    while($obj=mysql_fetch_object($res))
    {
        $cnt++;
        
        if($cnt%2==0)
            $html =$html.'<tr style="background-color:#cfeae5;">
                            <td style="padding:7px;">'.$cnt.'</td>
                            <td style="padding:7px;">'.$obj->Name.'</td>
                            <td style="padding:7px;">'.$obj->Gender.'</td>
                          </tr>';
        else
            $html =$html.'<tr style="background-color:#FFFFFF;">
                            <td style="padding:7px;">'.$cnt.'</td>
                            <td style="padding:7px;">'.$obj->Name.'</td>
                            <td style="padding:7px;">'.$obj->Gender.'</td>
                          </tr>';
    }

    $html=$html .'</table>';
}



// Print text using writeHTMLCell()
$pdf->writeHTML($html, true, false, true, false, '');

// ---------------------------------------------------------

// Close and output PDF document
// This method has several options, check the source code documentation for more information.
$pdf->Output('example_001.pdf', 'I');

//============================================================+
// END OF FILE
//============================================================+
