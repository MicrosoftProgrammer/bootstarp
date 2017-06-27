<?php
    include('../../includes/connection.php');
    include('../../includes/reportHelper.php');
    include('../../includes/templates.php');

    $filename="";
    $CategoryID=$_REQUEST["Category"];
    $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
    $filename = slugify($CategoryName)."_".date("Y-m-d H:i:s")."_".$_REQUEST["mode"];    

    if($_REQUEST["mode"]=="Download"){
        $header = array();
        $data = array();

        if($_REQUEST["RequestedMode"]=="Product") {                        
            $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                $res = mysql_query($sql);    
                
                $cols =0;
                while($obj=mysql_fetch_object($res)){
                    array_push($header,$obj->ProductFieldName);
                }
            
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
                    while($obj=mysql_fetch_object($res))
                    {
                        $showdata =true;
                        $filter=$_REQUEST['filters'];
    
                        if(count($filter)>0){
                            $allFields = json_decode($obj->Fields, TRUE);
                        
                            for($k=0;$k<count($filter);$k++) {
                        
                                $filkeys = explode("|",$filter[$k]);  
                                $filterdata = $filkeys[0];
                                $filterkey =  $filkeys[1];
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
                            foreach ($header as $key) {
                                $dataVal[$key] = trim($datum[$key]); 
                            }
                            $data[] = $dataVal;
                        }
                    }
                }
            }
            else if($_REQUEST["RequestedMode"]=="Overview") {   
                $sql="select * from productfields pf inner join fieldmapping fm 
                        on pf.ProductFieldID = fm.ProductFieldID
                        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";

                $res = mysql_query($sql);
                $header = array($_REQUEST["groups"],$_REQUEST["Sum"]);
                $fieldArray = array();
                while($obj=mysql_fetch_object($res)){
                    $fieldArray[$obj->ProductFieldKey] = $obj->ProductFieldName;
                }

                $data = array();

                $groupby = $_REQUEST["groups"];
                $groupby = $fieldArray[$groupby];

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
                            if (!array_key_exists($label, $sums)) {
                                $sums[$label] = 0;
                            }
                            $sums[$label] += $count;
                        }
                    }

                    arsort($sums);

                    $data = $sums;
                }  
            }
            else if($_REQUEST["RequestedMode"]=="ProductHistory" || $_REQUEST["RequestedMode"]=="date") {   
                $sql="select * from productfields pf inner join fieldmapping fm 
                        on pf.ProductFieldID = fm.ProductFieldID
                        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                $res = mysql_query($sql);
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
                if($numrows>0){
                    $header = array("S.No","Product Name","Invoice No","Owner","Purchase Date","Purchase Value","Due Date","Job Ref","LPO Ref","Quota Ref","Charge Details","Status");   
                    $cnt=0;
            
                    while($obj=mysql_fetch_object($res))
                    {
                        $cnt++; 
                        $showdata =true;
                        $filter=$_REQUEST['filters'];
    
                        if(count($filter)>0){
                            $allFields = json_decode($obj->Fields, TRUE);
                        
                            for($k=0;$k<count($filter);$k++) {
                        
                                $filkeys = explode("|",$filter[$k]);  
                                $filterdata = $filkeys[0];
                                $filterkey =  $filkeys[1];
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
                            $dataVal["S.No"]=$cnt;
                            $dataVal["Product Name"]=$datum[$obj->ProductPrimaryName];
                            $dataVal["Owner"]=$obj->Owner;
                            $dataVal["Invoice No"]=$obj->InvoiceNo;
                            $dataVal["Purchase Date"]= $obj->PurchaseDate=="" ? "NA" :ConvertToCustomDate($obj->PurchaseDate);
                            $dataVal["Purchase Value"]=$obj->PurchaseValue;
                            $dataVal["Due Date"]= $obj->DueDate=="" ? "NA" : ConvertToCustomDate($obj->DueDate);
                            $dataVal["Job Ref"]=$obj->JobRef;
                            $dataVal["LPO Ref"]=$obj->LPORef;
                            $dataVal["Quota Ref"]=$obj->QuotaRef;
                            $dataVal["Charge Details"]=$obj->ChargeDetails;
                            $dataVal["Status"]=$obj->Status==1 ? "Returned" : "Rented";

                            array_push($data,$dataVal);
                        }
                    }                                                                                                                                                                                
                }                
            }   
           // print_r($data); 
           if($_REQUEST["type"]=="pdf"){                
                $HTMLoutput = "";
                ob_end_clean();

                $HTMLoutput = "<table>";
                if($_REQUEST["RequestedMode"]=="Product" || $_REQUEST["RequestedMode"]=="ProductHistory" || $_REQUEST["RequestedMode"]=="date"){
                    foreach($data as $datum){
                        $HTMLoutput = $HTMLoutput."<tr>";       
                        foreach($header as $key){
                            $HTMLoutput = $HTMLoutput."<td>".$datum[$key]."</td>"; 
                        }
                        $HTMLoutput = $HTMLoutput."</tr>"; 
                    }
                }
                else if($_REQUEST["RequestedMode"]=="Overview"){
                    foreach($data as $datum => $value){
                        $cols=0;            
                        $objPHPExcel->getActiveSheet()->SetCellValue($columnarray[$cols].$rowCount, $datum); 
                        $cols++;
                        $objPHPExcel->getActiveSheet()->SetCellValue($columnarray[$cols].$rowCount, $value); 
                        $cols++;              
                        $rowCount++;
                    }            
                } 
                $HTMLoutput = $HTMLoutput."</table>"; 


                //Convert HTML 2 PDF by using MPDF PHP library
                include '../../includes/PHPExcel/mpdf/mpdf.php';
                $mpdf=new mPDF(); 

                $mpdf->WriteHTML($HTMLoutput);
                $mpdf->Output('filename.pdf','D');             
           }
           else{
                CreateReport($header,$data,$_REQUEST["type"],$filename,$_REQUEST["RequestedMode"]);      
           }                                                                    
        }                       
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>
        <?php echo fnDataTableCSS(); ?>
        <style>
        footer{
            display:none;
        }
                    span.cell{
                display: block !important;
            }

            .cell{
                white-space: nowrap;
                width:120px !important;
                text-overflow: ellipsis;
                cursor: pointer;
                word-break: break-all;
                overflow:hidden;
                white-space: nowrap;
            }

            span.cell:hover{
                overflow: visible; 
                width:auto !important;  /* just added this line */
            }
        </style>
    </head>
    <body>
        <div id="page-wrapper" style="margin-left:0">
            <div class="container-fluid">
               <div class="row">
                <div class="col-lg-12" >
                    <div class="panel panel-primary">
                        <div class="panel-heading" id="head">
                            Preview Report
                            <span class="pull-right col-md-3 operation">
                                <a href="javascript:fnDownload()"><i class="fa fa-download fa-2x pull-right">&nbsp;&nbsp;</i></a>
                            </span>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body" style="width: 100%;overflow-x: scroll;">
                        <form name="adminForm" method="post"> 
                            <input type="hidden" name="Category" value="<?php echo $_REQUEST["Category"]; ?>" />
                            <input type="hidden" name="groups" value="<?php echo $_REQUEST["groups"]; ?>" />
                            <input type="hidden" name="Sum" value="<?php echo $_REQUEST["Sum"]; ?>" />
                            <input type="hidden" name="type" value="<?php echo $_REQUEST["type"]; ?>" />
                            <input type="hidden" name="Product" value="<?php echo $_REQUEST["Product"]; ?>" />
                            <input type="hidden" name="FromDate" value="<?php echo $_REQUEST["FromDate"]; ?>" />
                            <input type="hidden" name="ToDate" value="<?php echo $_REQUEST["ToDate"]; ?>" />
                            <input type="hidden" name="RequestedMode" value="<?php echo $_REQUEST["mode"]; ?>" />
                            <?php 
                                $filter=json_decode($_REQUEST['filters'],TRUE);
                                if(count($filter)>0){
                                    $allFields = json_decode($obj->Fields, TRUE);
                                
                                    for($k=0;$k<count($filter);$k++) {
                                        $filterkey = $_REQUEST[$filter[$k]["Key"]];                                
                                        if($filterkey !="") {
                                            echo ' <input type="hidden" name="filters[]" value="'.$filter[$k]["Name"].'|'.$filterkey.'" />';
                                        }
                                    }
                                }                            
                            ?>
                            <?php
                                $CategoryID=$_REQUEST["Category"];
                                $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
                                echo '<div id="divLoading">
                                        <p>
                                        Loading, please wait...
                                        <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
                                    </p></div>';
                                echo '<table id="export"  width="100%" class="table table-striped table-bordered table-hover">';
                                if($_REQUEST["mode"]=="Product"){
                                    $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                    where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                    $res = mysql_query($sql);                               
                                    echo "<thead>";
                                    echo "<tr>";
                                    $header = array();
                                    while($obj=mysql_fetch_object($res)){
                                    echo "<th class='cell'>".$obj->ProductFieldName."</th>";
                                    array_push($header,$obj->ProductFieldName);
                                    }
                                    echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";
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
                                            if($cnt>$numrows)
                                                continue;
                                            echo "<tr>"; 
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
                                                    echo "<td><span class='cell'>".$datum[$key]."</span></td>";
                                                    $count++;
                                                }
                                            }

                                            echo "</tr>";
                                            $cnt++;
                                        }
                                    }                                         
                            
                                    echo "</tbody>";                                 
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

                                        echo "<thead>";
                                        echo "<tr>";
                                        echo "<th>".$groupby."</th>";
                                        echo "<th>".$_REQUEST["Sum"]."</th>";
                                        echo "</tr>";
                                        echo "</thead>";

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
                                            echo "<tbody>";
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
                                                echo "<tr>";
                                                echo "<td>".$label."</td>";
                                                echo "<td>".$count."</td>";
                                                echo "</tr>";          
                                            }

                                            echo "</tbody>";     
                                        }  
                                    }
                                    else if($_REQUEST["mode"]=="ProductHistory" || $_REQUEST["mode"]=="date"){
                                        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                        $res = mysql_query($sql);

                                        echo "<thead>";
                                        echo "<tr>";
                                        $header = array("S.No","Product Name","Invoice No","Owner","Purchase Date","Purchase Value","Due Date","Job Ref","LPO Ref","Quota Ref","Charge Details","Status");   

                                        foreach($header as $key){
                                         echo "<th class='cell'>".$key."</th>";
                                        }

                                        echo "</tr>";
                                        echo "</thead>";

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
                                            echo "<tbody>";
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
                                                    echo "<tr>";
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

                                                    foreach($dataVal as $key){
                                                        echo "<td><span class='cell'>".$key."</span></td>"; 
                                                    }

                                                    echo "</tr>";
                                                }
                                            }
                                            echo "</tbody>";
                                        }                                         
                                    }  
                                echo "</table>"; 
                            ?>
                        
                            </form>       
                        </div>
                        <!-- /.panel-body -->
                    </div>                     
                    </div>
                <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
    </body>
    <?php echo fnScript(); ?>
    <?php echo fnDataTableScript(); ?>
    <script src="scripts/dataTables.buttons.min.js"></script>
    <script src="scripts/buttons.print.min.js"></script>
    <script>
        function fnPrint()
        {
            $("#head").hide();
            window.print();
        }

        function fnDownload()
        {
            document.adminForm.action="previewreport.php?mode=Download";
            document.adminForm.submit();               
        }

        $(document).ready(function() {
            $("#divLoading").hide();
        });
    </script>
</html>