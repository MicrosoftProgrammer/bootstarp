<?php

    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

include_once('../../includes/excel/xlsxwriter.class.php');
$CategoryID=$_REQUEST["CategoryID"];
$CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
$filename = slugify($CategoryName).".xlsx";
header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

$sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
where fm.CategoryID=".$CategoryID." order by pf.ProductFieldID";
$res = mysql_query($sql);
$header = array();

while($obj=mysql_fetch_object($res)){
    $header[$obj->ProductFieldName]='string';
}

$data = array();

 
$writer = new XLSXWriter();
$writer->setAuthor($_SESSION["CompanyName"]);
$writer->writeSheet($data,$CategoryName,$header);
$writer->writeToStdOut(); 
?>