<?php
    include('../../../includes/connection.php');
    include('../../../includes/helpers.php');
    include('../../../includes/templates.php');

    $filename="";
    $CategoryID=$_REQUEST["Category"];
    $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
    $filename = slugify($CategoryName)."_".date("Y-m-d H:i:s")."_".$_REQUEST["mode"];  

    header("Content-Type: application/csv");
    header("Content-Disposition: attachment;Filename=".$filename.".csv");


  if($_REQUEST["mode"]=="Product"){
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
            $objFields=mysql_fetch_object($res);
            $count=0;
            $data = json_decode($objFields->Fields, TRUE);
            
            foreach(array_keys($data) as $key) {
                echo $key.",";
                $count++;
            }
            
            echo "\n";

            $cnt=0;
            while($obj=mysql_fetch_object($res))
            { 
                $cnt++;
                $showdata =true;
                if(count($filter)>0){
                    $data = json_decode($obj->Fields, TRUE);
                
                    for($k=0;$k<count($filter);$k++) {
                    $filterkey = $_REQUEST[$filter[$k]["Key"]];        
                    $filterdata = $filter[$k]["Name"];

                        if($filterkey !=""){
                            if($filterkey!=$data[$filterdata]){
                                $showdata= false;
                            }
                        }
                    }
                }

                if($cnt%2==0) $class=""; else $class="class=alt";
                $data = json_decode($obj->Fields, TRUE);
                if($showdata && count($data)>=6) {                                                    
                    $count=0;
                    
                    if(count($data)>=6){
                        foreach(array_values($data) as $value) {
                            echo str_replace(",","`",$value).",";
                        }
                    }
                }
                echo "\n";            
            }
        }  
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

        echo str_replace(",","`",$groupby).",";
        echo str_replace(",","`",$_REQUEST["Sum"]).",";
        echo "\n"; 

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
                echo str_replace(",","`",$label).",";
                echo str_replace(",","`",$count).",";
                echo "\n";  
            }        
        }  
    }
    else if($_REQUEST["mode"]=="ProductHistory"){
        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
        $res = mysql_query($sql);
        echo "S.No,Product Name,Invoice No,Owner,Purchase Date,Purchase Value,Due Date,Job Ref,LPO Ref,Quota Ref,Charge Details,Status";   
        echo "\n";
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
                    echo $cnt.",";
                    echo str_replace(",","`",$datum[$obj->ProductPrimaryName]).",";
                    echo str_replace(",","`",$obj->Owner).",";
                    echo str_replace(",","`",$obj->InvoiceNo).",";
                    echo str_replace(",","`",ConvertToCustomDate($obj->PurchaseDate)).",";
                    echo str_replace(",","`",$obj->PurchaseValue).",";
                    echo str_replace(",","`",ConvertToCustomDate($obj->DueDate)).",";
                    echo str_replace(",","`",$obj->JobRef).",";
                    echo str_replace(",","`",$obj->LPORef).",";
                    echo str_replace(",","`",$obj->QuotaRef).",";
                    echo str_replace(",","`",$obj->ChargeDetails).",";
                    echo $obj->Status==1 ? "Returned" : "Rented";
                    echo "\n";
                }
            }
        }                                         
    }
    else if($_REQUEST["mode"]=="date"){
        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
        $res = mysql_query($sql);
        
        echo "S.No,Product Name,Invoice No,Owner,Purchase Date,Purchase Value,Due Date,Job Ref,LPO Ref,Quota Ref,Charge Details,Status";   
        echo "\n";

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
                    echo $cnt.",";
                    echo str_replace(",","`",$datum[$obj->ProductPrimaryName]).",";
                    echo str_replace(",","`",$obj->Owner).",";
                    echo str_replace(",","`",$obj->InvoiceNo).",";
                    echo str_replace(",","`",ConvertToCustomDate($obj->PurchaseDate)).",";
                    echo str_replace(",","`",$obj->PurchaseValue).",";
                    echo str_replace(",","`",ConvertToCustomDate($obj->DueDate)).",";
                    echo str_replace(",","`",$obj->JobRef).",";
                    echo str_replace(",","`",$obj->LPORef).",";
                    echo str_replace(",","`",$obj->QuotaRef).",";
                    echo str_replace(",","`",$obj->ChargeDetails).",";
                    echo $obj->Status==1 ? "Returned" : "Rented";
                    echo "\n";
                }
            }
        }                                          
    }                                  
?>