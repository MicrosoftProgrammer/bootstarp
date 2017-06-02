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
                    foreach (array_keys($header) as $key) {
                        $dataVal[$count]= $datum[$key];
                        $count++;
                    }
                    array_push($data,$dataVal);
                }
            }
        }                                         
 
        $writer = new XLSXWriter();
        $writer->setAuthor($_SESSION["CompanyName"]);
        $writer->writeSheet($data,$CategoryName,$header);
        $writer->writeToStdOut(); 
    }
    else if($_REQUEST["mode"]=="Overview"){
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
        $fieldArray = array();
        while($obj=mysql_fetch_object($res)){
            $fieldArray[$obj->ProductFieldKey] = $obj->ProductFieldName;
        }

        $data = array();

        $groupby = $_REQUEST["groups"];
        $groupby = $fieldArray[$groupby];
        $header = array($groupby=>'string',"Stock Count"=>'number') ;    

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
                array_push($data,$dataVal);
            }        
        }  
                                         
        $writer = new XLSXWriter();
        $writer->setAuthor($_SESSION["CompanyName"]);
        $writer->writeSheet($data,$CategoryName,$header);
        $writer->writeToStdOut(); 
    }
?>