<?php

    include('../../../includes/connection.php');
    include('../../../includes/helpers.php');
    include('../../../includes/templates.php');
    include_once('../../../includes/excel/xlsxwriter.class.php');

    $filename="";
    $CategoryID=$_REQUEST["Category"];
    $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
    $filename = slugify($CategoryName)."_".date("Y-m-d H:i:s")."_".$_REQUEST["mode"].".xlsx";      

    header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');

    if($_REQUEST["mode"]=="Product"){
        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
        $res = mysql_query($sql);
        $header = array();   
        $styles = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');       
        $headerstyles = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eeffee', 'halign'=>'center', 'border'=>'left,right,top,bottom');              
        
        while($obj=mysql_fetch_object($res)){
            array_push($header,$obj->ProductFieldName);
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

        $writer = new XLSXWriter();
        $writer->setAuthor($_SESSION["CompanyName"]); 
        $writer->writeSheetRow($CategoryName,$header,$headerstyles);           

        if($numrows>0)
        {
            $cnt=0;
            while($obj=mysql_fetch_object($res))
            { 
                $showdata =true;
                $filter=json_decode($_REQUEST['filters'],TRUE);
                if(count($filter)>0){
                    $allFields = json_decode($obj->Fields, TRUE);
                
                    for($k=0;$k<count($filter);$k++) {
                        $filterkey = $_REQUEST[$filter[$k]["Key"]];
                
                        $filterdata = $filter[$k]["Name"];
                        if($filterkey !="") {
                            if($filterkey!=$allFields[$filterdata]){
                                $showdata= false;
                            }
                        }
                    }
                }

                if($showdata){
                    $datum = json_decode($obj->Fields, TRUE);
                    $dataVal = array();
                    $count =0;
                    foreach ($header as $key) {
                        $dataVal[$count]= $datum[$key];
                        $count++;
                    }
                    $writer->writeSheetRow($CategoryName,$dataVal,$styles); 
                }
            }
        }                                         
 
        $writer->writeToStdOut(); 
    }
    else if($_REQUEST["mode"]=="Overview"){

        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
        $res = mysql_query($sql);
        $header = array();
        $fieldArray = array();
        while($obj=mysql_fetch_object($res)){
            $fieldArray[$obj->ProductFieldKey] = $obj->ProductFieldName;
        }

        $data = array();

        $groupby = $_REQUEST["groups"];
        $groupby = $fieldArray[$groupby];
        $header = array($groupby, $_REQUEST["Sum"]) ;    

        $sql = "select * from products p inner join categories c on p.CategoryID =c.CategoryID 
        where p.Deleted=0";
        if($_REQUEST["Category"]!=""){
            $sql= $sql." and p.CategoryID=".$_REQUEST["Category"];
        }
        $sql.= " order by p.ProductID";
        $res=mysql_query($sql);
        $numrows=mysql_num_rows($res);

        $styles = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');       
        $headerstyles = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eeffee', 'halign'=>'center', 'border'=>'left,right,top,bottom');                      
        $writer = new XLSXWriter();
        $writer->setAuthor($_SESSION["CompanyName"]); 
        $writer->writeSheetRow($CategoryName,$header,$headerstyles);         

        if($numrows>0)
        {
            $cnt=0;

            while($obj=mysql_fetch_object($res))
            { 
                $datum = json_decode($obj->Fields, TRUE);
                array_push($data,array($datum[$groupby]=>$datum[$_REQUEST["Sum"]]));             
            }

            foreach ($data as $key => $values) {
                foreach ($values as $label => $count) {
                    // Create a node in the array to store the value
                    if (!array_key_exists($label, $sums)) {
                        $sums[$label] = 0;
                    }
                    // Add the value to the corresponding node
                    $sums[$label] += $count;
                }
            }

            // Sort the array in descending order of values
            arsort($sums);

            $data = array();
            foreach ($sums as $label => $count) {
                $dataVal = array();
                $dataVal[$groupby]= $label;
                $dataVal["Stock Count"] = $count;
                $writer->writeSheetRow($CategoryName,$dataVal,$styles);                
            }        
        }  


  
        $writer->writeToStdOut(); 
    }
    else if($_REQUEST["mode"]=="ProductHistory"){
        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
        $res = mysql_query($sql);
        $header = array("S.No","Product Name","Invoice No","Owner","Purchase Date","Purchase Value","Due Date","Job Ref","LPO Ref","Quota Ref","Charge Details","Status");   
        $styles = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');       
        $headerstyles = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eeffee', 'halign'=>'center', 'border'=>'left,right,top,bottom');              

        $data = array();

        $sql = "select * from producttransactions pt inner join products p on p.ProductID=pt.ProductID 
        inner join categories c on p.CategoryID =c.CategoryID 
        where p.Deleted=0";
        if($_REQUEST["Category"]!=""){
            $sql= $sql." and p.CategoryID=".$_REQUEST["Category"];
        }
        if($_REQUEST["Product"]!=""){
            $sql= $sql." and p.ProductID=".$_REQUEST["Product"];
        }        
        $sql.= " order by p.ProductID";

        $res=mysql_query($sql);
        $numrows=mysql_num_rows($res);

        $writer = new XLSXWriter();
        $writer->setAuthor($_SESSION["CompanyName"]); 
        $writer->writeSheetRow($CategoryName,$header,$headerstyles);           

        if($numrows>0)
        {
            $cnt=0;
            while($obj=mysql_fetch_object($res))
            {
                $cnt++; 
                $showdata =true;
                $filter=json_decode($_REQUEST['filters'],TRUE);
                if(count($filter)>0){
                    $allFields = json_decode($obj->Fields, TRUE);
                
                    for($k=0;$k<count($filter);$k++) {
                        $filterkey = $_REQUEST[$filter[$k]["Key"]];
                
                        $filterdata = $filter[$k]["Name"];
                        if($filterkey !="") {
                            if($filterkey!=$allFields[$filterdata]){
                                $showdata= false;
                            }
                        }
                    }
                }

                if($showdata){
                    $datum = json_decode($obj->Fields, TRUE);
                    $dataVal = array();
                    $dataVal[0]=$cnt;
                    $dataVal[1]=$datum[$obj->ProductPrimaryName];
                    $dataVal[2]=$obj->Owner;
                    $dataVal[3]=$obj->InvoiceNo;
                    $dataVal[4]=ConvertToCustomDate($obj->PurchaseDate);
                    $dataVal[5]=$obj->PurchaseValue;
                    $dataVal[6]=ConvertToCustomDate($obj->DueDate);
                    $dataVal[7]=$obj->JobRef;
                    $dataVal[8]=$obj->LPORef;
                    $dataVal[9]=$obj->QuotaRef;
                    $dataVal[10]=$obj->ChargeDetails;
                    $dataVal[11]=$obj->Status==1 ? "Returned" : "Rented";

                    $writer->writeSheetRow($CategoryName,$dataVal,$styles); 
                }
            }
        }                                         
 
        $writer->writeToStdOut(); 
    }
    else if($_REQUEST["mode"]=="date"){
        $CategoryID=$_REQUEST["Category"];
        $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
        $filename = slugify($CategoryName)."-History.xlsx";

        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
        $res = mysql_query($sql);
        $header = array("S.No","Product Name","Invoice No","Owner","Purchase Date","Purchase Value","Due Date","Job Ref","LPO Ref","Quota Ref","Charge Details","Status");   
        $styles = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eee', 'halign'=>'center', 'border'=>'left,right,top,bottom');       
        $headerstyles = array( 'font'=>'Arial','font-size'=>10,'font-style'=>'bold', 'fill'=>'#eeffee', 'halign'=>'center', 'border'=>'left,right,top,bottom');              

        $data = array();

        $sql = "select * from producttransactions pt inner join products p on p.ProductID=pt.ProductID 
        inner join categories c on p.CategoryID =c.CategoryID 
        where p.Deleted=0";
        if($_REQUEST["Category"]!=""){
            $sql= $sql." and p.CategoryID=".$_REQUEST["Category"];
        }
        if($_REQUEST["FromDate"]!="" && $_REQUEST["ToDate"]!=""){
            $FromDate = str_replace('/','-',$_REQUEST["FromDate"]);
            $FromDate = ConvertToStdDate($FromDate);
            $ToDate = str_replace('/','-',$_REQUEST["ToDate"]);
            $ToDate = ConvertToStdDate($ToDate);

            $sql= $sql." and pt.PurchaseDate between '".$FromDate."' and '".$ToDate."'";
        }
        if($_REQUEST["Product"]!=""){
            $sql= $sql." and p.ProductID=".$_REQUEST["Product"];
        }        
        $sql.= " order by p.ProductID";

        $res=mysql_query($sql);
        $numrows=mysql_num_rows($res);

        $writer = new XLSXWriter();
        $writer->setAuthor($_SESSION["CompanyName"]); 
        $writer->writeSheetRow($CategoryName,$header,$headerstyles);           

        if($numrows>0)
        {
            $cnt=0;
            while($obj=mysql_fetch_object($res))
            {
                $cnt++; 
                $showdata =true;
                $filter=json_decode($_REQUEST['filters'],TRUE);
                if(count($filter)>0){
                    $allFields = json_decode($obj->Fields, TRUE);
                
                    for($k=0;$k<count($filter);$k++) {
                        $filterkey = $_REQUEST[$filter[$k]["Key"]];
                
                        $filterdata = $filter[$k]["Name"];
                        if($filterkey !="") {
                            if($filterkey!=$allFields[$filterdata]){
                                $showdata= false;
                            }
                        }
                    }
                }

                if($showdata){
                    $datum = json_decode($obj->Fields, TRUE);
                    $dataVal = array();
                    $dataVal[0]=$cnt;
                    $dataVal[1]=$datum[$obj->ProductPrimaryName];
                    $dataVal[2]=$obj->Owner;
                    $dataVal[3]=$obj->InvoiceNo;
                    $dataVal[4]=ConvertToCustomDate($obj->PurchaseDate);
                    $dataVal[5]=$obj->PurchaseValue;
                    $dataVal[6]=ConvertToCustomDate($obj->DueDate);
                    $dataVal[7]=$obj->JobRef;
                    $dataVal[8]=$obj->LPORef;
                    $dataVal[9]=$obj->QuotaRef;
                    $dataVal[10]=$obj->ChargeDetails;
                    $dataVal[11]=$obj->Status==1 ? "Returned" : "Rented";

                    $writer->writeSheetRow($CategoryName,$dataVal,$styles); 
                }
            }
        }                                         
 
        $writer->writeToStdOut(); 
    }    
?>
