<?php
    include('../../../includes/connection.php');
    include('../../../includes/helpers.php');
    include('../../../includes/templates.php');

    $CategoryID=$_REQUEST["Category"];
    $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
    $filename = slugify($CategoryName);

    header("Content-Type: application/csv");
    header("Content-Disposition: attachment;Filename=".$filename.".csv");

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
?>
