<?php
    include('../../includes/connection.php');
    include('../../includes/reportHelper.php');
    include('../../includes/templates.php');

    $filename="";
    $CategoryID=$_REQUEST["Category"];
    $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
    $filename = slugify($CategoryName)."_".date("Y-m-d H:i:s")."_".$_REQUEST["mode"];    

    if($_REQUEST["mode"]=="Download"){
                             
    $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
            where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
            $res = mysql_query($sql);    
            $header = array();
            $cols =0;
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

            if($numrows>0)
            {
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
                        foreach ($header as $key) {
                            $dataVal[$key] = trim($datum[$key]); 
                        }
                        $data[] = $dataVal;
                    }
                }
            }

            CreateReport($header,$data,$_REQUEST["type"],$filename);                                                      
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
                            <textarea name="filters" style="display:none;"><?php echo $_REQUEST["filters"]; ?></textarea>
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
                                        $filename = slugify($CategoryName)."-date.xlsx";

                                        echo "<thead>";
                                        echo "<tr>";
                                        $header = array("S.No","Product Name","Invoice No","Owner","Purchase Date","Purchase Value","Due Date","Job Ref","LPO Ref","Quota Ref","Charge Details","Status");   

                                        foreach($header as $key){
                                        echo "<th>".$key."</th>";  
                                        }

                                        echo "</tr>";
                                        echo "</thead>";

                                        $data = array();

                                        $sql = "select * from producttransactions pt left join products p on p.ProductID=pt.ProductID 
                                        left join categories c on p.CategoryID =c.CategoryID 
                                        where (p.Deleted=0 or pt.ProductID=0)";
                                    
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
            $("#export").on( 'init.dt', function () {
                    $("#divLoading").hide();
                } ).DataTable({
                "bSort" : false,
                "bPaginate": false,
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'print',
                        customize: function ( win ) {
                            $(win.document.body)
                                .css( 'font-size', '10pt' )
                                .prepend(
                                    '<img src="http://datatables.net/media/images/logo-fade.png" style="position:absolute; top:0; left:0;" />'
                                );
        
                            $(win.document.body).find( 'table' )
                                .addClass( 'compact' )
                                .css( 'font-size', 'inherit' );
                        }
                    }
                ]
            });

            $(".dt-button").addClass("btn btn-danger");
        });
    </script>

</html>