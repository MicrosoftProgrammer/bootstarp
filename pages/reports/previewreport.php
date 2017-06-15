<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    $filename="";
    $CategoryID=$_REQUEST["Category"];
    $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
    $filename = slugify($CategoryName)."_".date("Y-m-d H:i:s")."_".$_REQUEST["mode"];      
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
        </style>
    </head>
    <body>
        <div id="page-wrapper" style="margin-left:0">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
               
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <form name="adminForm" method="post"> 
                        <div id="head" class="col-md-12" style="background-color:#000;color:white;height:50px;margin-top:10px;">
                            <h3 class="col-md-9"><?php echo $_REQUEST["mode"]." Report"; ?></h3>
                            <span class="pull-right col-md-3 operation">
                                <a href="javascript:fnDownload()"><i class="fa fa-download fa-3x pull-right"></i></a>
                                <a href="javascript:fnPrint()"><i class="fa fa-print fa-3x pull-right"></i></a>
                            </span>
                        </div>
                        <input type="hidden" name="Category" value="<?php echo $_REQUEST["Category"]; ?>" />
                        <input type="hidden" name="groups" value="<?php echo $_REQUEST["groups"]; ?>" />
                        <input type="hidden" name="Sum" value="<?php echo $_REQUEST["Sum"]; ?>" />
                        <input type="hidden" name="Product" value="<?php echo $_REQUEST["Product"]; ?>" />
                        <input type="hidden" name="FromDate" value="<?php echo $_REQUEST["FromDate"]; ?>" />
                        <input type="hidden" name="ToDate" value="<?php echo $_REQUEST["ToDate"]; ?>" />
                        <textarea name="filters" style="display:none;"><?php echo $_REQUEST["filters"]; ?></textarea>
                        <?php
                            $CategoryID=$_REQUEST["Category"];
                            $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
                            echo '<div id="divLoading" style="display:none;margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(102, 102, 102); z-index: 30001; opacity: 0.8;">
<p style="position: absolute; color: White; top: 50%; left: 45%;">
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
                                while($obj=mysql_fetch_object($res)){
                                echo "<th style='white-space: nowrap'>".$obj->ProductFieldName."</th>";
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
                                            foreach ($datum as $key) {
                                                echo "<td>".$key."</td>";
                                                $count++;
                                            }
                                        }

                                        echo "</tr>";
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
                                else if($_REQUEST["mode"]=="ProductHistory"){
                                    $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                    where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                    $res = mysql_query($sql);

                                    echo "<thead>";
                                    echo "<tr>";
                                    $header = array("S.No","Product Name","Invoice No","Owner","Purchase Date","Purchase Value","Due Date","Job Ref","LPO Ref","Quota Ref","Charge Details","Status");   

                                    foreach($header as $key){
                                    echo "<th>".$key."</th>";  
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
                                                    echo "<td>".$key."</td>";  
                                                }

                                                echo "</tr>";
                                            }
                                        }
                                        echo "</tbody>";
                                    }                                         
                                }
                                else if($_REQUEST["mode"]=="date"){
                                    include_once('../../includes/excel/xlsxwriter.class.php');

                                    $CategoryID=$_REQUEST["Category"];
                                    $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
                                    $filename = slugify($CategoryName)."-History.xlsx";

                                    $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                    where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                    $res = mysql_query($sql);

                                    echo "<thead>";
                                    echo "<tr>";
                                    $header = array("S.No","Product Name","Invoice No","Owner","Purchase Date","Purchase Value","Due Date","Job Ref","LPO Ref","Quota Ref","Charge Details","Status");   

                                    foreach($header as $key){
                                    echo "<th>".$key."</th>";  
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
                                                    echo "<td>".$key."</td>";  
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
                <!-- /.col-lg-12 -->
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
    </body>
    <?php echo fnScript(); ?>
    <script type="text/javascript" src="scripts/libs/FileSaver/FileSaver.min.js"></script>
    <script type="text/javascript" src="scripts/libs/js-xlsx/xlsx.core.min.js"></script>
    <script type="text/javascript" src="scripts/libs/jsPDF/jspdf.min.js"></script>
    <script type="text/javascript" src="scripts/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>
    <script type="text/javascript" src="scripts/tableExport.min.js"></script>
    <script>
        function fnPrint()
        {
            $("#head").hide();
            window.print();
        }

        function fnDownload()
        {
            $("#divLoading").show();
            setTimeout(function(){
            <?php if($_REQUEST["type"]=="excel") { ?>
                $('#export').tableExport({type:'xlsx',htmlContent: true,fileName: '<?php echo $filename; ?>',worksheetName: '<?php echo $CategoryName; ?>'});
            <?php } ?>
            <?php if($_REQUEST["type"]=="word") { ?>
                $('#export').tableExport({type:'doc',htmlContent: true,fileName: '<?php echo $filename; ?>'});
            <?php } ?>
            <?php if($_REQUEST["type"]=="pdf") { ?>
                $('#export').tableExport({type:'pdf',htmlContent: true,fileName: '<?php echo $filename; ?>',
                           jspdf: {orientation: 'l',
                                   format: 'a2',
                                   margins: {left:10, right:10, top:20, bottom:20}

                                  }
                          });
            <?php } ?>
            <?php if($_REQUEST["type"]=="csv") { ?>
                $('#export').tableExport({type:'csv',htmlContent: true,fileName: '<?php echo $filename; ?>',worksheetName: '<?php echo $CategoryName; ?>'});
            <?php } ?>
            },1000);
		    // document.adminForm.action="<?php echo "../reports/types/".$_REQUEST["type"].".php?mode=".$_REQUEST["mode"]; ?>";
            // document.adminForm.submit();  
            setTimeout(function(){
                $("#divLoading").hide();  
            },1000);
               
        }
        var cnt = $( "#export thead tr th" ).length;
        $('#export thead tr:first').before('<tr><td align="center" colspan='+cnt+'><h2><?php echo $_SESSION["CompanyName"]." ".$_REQUEST["mode"]." Report"; ?><h2></td></tr>');
    </script>


</html>