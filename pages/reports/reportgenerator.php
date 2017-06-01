<?php

    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if($_REQUEST["mode"]=="Product"){
        include_once('../../includes/excel/xlsxwriter.class.php');

        $CategoryID=$_REQUEST["Category"];
        $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
        $filename = slugify($CategoryName).".xlsx";
        header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
        header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
        $res = mysql_query($sql);
        $header = array();

        while($obj=mysql_fetch_object($res)){
            $header[$obj->ProductFieldName]='string';
        }

        $data = array();

        $sql = "select * from products p inner join categories c on p.CategoryID =c.CategoryID 
        where p.Deleted=0";
        if($_REQUEST["Category"]!=""){
            $sql= $sql." and p.CategoryID=".$_REQUEST["Category"];
        }
        $sql.= " order by p.ProductID";
        $res=mysql_query($sql);
        $numrows=mysql_num_rows($res);
        
        if($numrows>0)
        {
            $cnt=0;
            while($obj=mysql_fetch_object($res))
            { 
                $datum = json_decode($obj->Fields, TRUE);
                array_push($data,$datum);
            }
        }                                         
 
        $writer = new XLSXWriter();
        $writer->setAuthor($_SESSION["CompanyName"]);
        $writer->writeSheet($data,$CategoryName,$header);
        $writer->writeToStdOut(); 
    }
?>