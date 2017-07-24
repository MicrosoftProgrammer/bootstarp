<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');
$filter = array();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>    
    </head>
    <body>
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <?php echo fnMobileMenu(); ?>
            <?php echo fnTopLinks(); ?>
            <?php echo fnSideBar(); ?>
        </nav>
        <div id="page-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12">
                        <h1 class="page-header">Product History Report</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Product History Report
                        </div>
                        <div class="panel-body">
                            <?php if($error!=""){ ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                Error! <strong><?php echo $error; ?></strong>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                   <form name="adminForm" method="post" action="reportgenerator.php?mode=Product" enctype="multipart/form-data">   
                                                                                                       <div class="form-group col-md-3">
                                        <label>Client Name</label>
                                            <select class="form-control" name="Client" onchange="fnSubmit();" required>
                                                <?php fnDropDown("Client","ClientName","ClientID","Client"); ?>
                                            </select>                                               
                                    </div>
                                        <div class="form-group col-md-3">
                                            <label>Category Name</label>
                                            <select class="form-control" name="Category" onchange="fnSubmit();" required>
                                                <?php fnDropDown("categories","CategoryName","CategoryID","Category"); ?>
                                            </select>                                               
                                        </div>
                                    <?php if($_REQUEST["Category"]!="") {
                                        
                                        $CategoryID = $_REQUEST["Category"];
                                            $sql="select *, pft.ProductFieldType as Type from productfields pf 
                                                inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                                inner join productfieldtype pft on pf.ProductFieldType = pft.ProductFieldTypeID   
                                                where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                                $res = mysql_query($sql);
                                                while($obj=mysql_fetch_object($res)){
                                                    if($obj->ShowInFilter=="1"){
                                                        array_push($filter,
                                                        array("Key"=>$obj->ProductFieldKey,
                                                        "Name"=>$obj->ProductFieldName));
                                                        echo ' <div class="form-group col-md-3">
                                                                <label>'.$obj->ProductFieldName.'</label>
                                                                    <select class="form-control" name="'.$obj->ProductFieldKey.'" onchange="fnSubmit();">
                                                                        '.fnGetFilter($obj->ProductFieldName,$obj->ProductFieldKey,$CategoryID ).'
                                                                    </select>                                               
                                                            </div>';
                                                    }
                                                }
                                                                        
                                                echo '<textarea name="filters" style="display:none;">'.json_encode($filter).'</textarea>';          
                                     ?>
                                     <div class="form-group col-md-4">
                                        <label>Product Name</label>
                                            <select class="form-control" name="Product" onchange="fnSubmit();" >  
                                            <option value="">Select</option>                                        
                                    <?php
                                        $sql = "select * from products p inner join categories c on p.CategoryID =c.CategoryID 
                                        where p.Deleted=0";
                                        if($_REQUEST["Category"]!=""){
                                            $sql= $sql." and p.CategoryID=".$_REQUEST["Category"];
                                        }
                                        
                                        $res=mysql_query($sql);
                                        $numrows=mysql_num_rows($res);
                                        	if($numrows>0)
                                            {
                                                $cnt=0;
                                                while($obj=mysql_fetch_object($res))
                                                { 
                                                    $cnt++;
                                                    $showdata =true;
                                                    if(count($filter)>0){
                                                         $data = json_decode($obj->Fields, TRUE);
                                                        
                                                         for($k=0;$k<count($filter);$k++){
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
                                                    if($showdata) {
                                                            $count=0;
                                                            $data = json_decode($obj->Fields, TRUE);
                                                               $selected="";
                                                               if($obj->ProductID==$_REQUEST["Product"])
                                                                {
                                                                    $selected="selected";
                                                                }
                                                                echo "<option ".$selected." value='".$obj->ProductID."'>".$data[$obj->ProductPrimaryName]."</option>";                                                          
                                                }
                                            }
                                        }                                    
                                    ?>
                                        </select>                                               
                                    </div>
      <?php
                                                      echo ' <div class="form-group col-md-12">
                                                               <a href="javascript:void(0)" onclick="fnReport(1)"><img src="../../images/excel.png"alt="excel" /></a>
                                                               <a href="javascript:void(0)" onclick="fnReport(2)"><img src="../../images/csv.png"alt="csv" /></a>       
                                                               <a href="javascript:void(0)" onclick="fnReport(3)"><img src="../../images/word.png"alt="word" /></a>  
                                                               <a href="javascript:void(0)" onclick="fnReport(4)"><img src="../../images/pdf.png"alt="pdf" /></a>                                          
                                                            </div>';

               echo '<div id="divLoading">
                                        <p>
                                        Loading, please wait...
                                        <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
                                    </p></div>';   
       ?>
       <?php 
       echo '<div class="col-md-12" style="width:100%;overflow-x:scroll">
       <table width="100%" class="table table-striped table-bordered table-hover" id="dataTable-example">';
        $sql="select * from productfields pf inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                        where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                        $res = mysql_query($sql);



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
 if($numrows>0){
                                                                                echo "<thead>";
                                        echo "<tr>";
                                        $header = array("S.No","Product Name","Owner","Invoice No","Purchase Date","Purchase Value","Due Date","Job Ref","LPO Ref","Quota Ref","Charge Details","Status");   

                                        foreach($header as $key){
                                            $cls="nprn";  
                                            if(strlen($key)>10){
                                                $cls="cell";
                                            }
                                            echo "<th  style='white-space: nowrap;' class='".$cls."'>".$key."</th>";  
                                        }

                                        echo "</tr>";
                                        echo "</thead>";
 }
        echo "<tbody>";
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
                                                        $cls="nprn";  
                                                        if(strlen($key)>9){
                                                            $cls="cell";
                                                        }
                                                        echo "<td class='".$cls."'>".$key."</td>";  
                                                    }

                                                    echo "</tr>";
                                                }
                                            }                                                                                                                                                                                
                                        } 
                                        else
                                            {
                                                echo '<tr  id="no" style="background-color: white!important;"><td><b style="color:red;">No Product History found.</b></td></tr>';
                                            } 
                                        echo "</tbody>";
                                        echo "</table></div>";            
    }   ?>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
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
 <?php echo fnDataTableExportScript(); ?>
    <script>
                $(document).ready(function() {
                         if($("#no")[0] === undefined) {
                            $("#dataTable-example").on( 'init.dt', function () {
                                        $("#divLoading").hide();
                                    } ).DataTable({
                            "bSort": false,
                                         "bSort": false,
                                    dom: 'Bfrtip',
                                       buttons: [
                                         'copy', 'csv', 'excel', 'pdf', 'print'
                                        ]
                                    });
                         }
                         else{
                               $("#divLoading").hide(); 
                         }
    });
        function fnSubmit(){
             document.adminForm.target="_self";
            document.adminForm.action="producthistoryreport.php";
            document.adminForm.submit();
        }

        function fnReport(arg){
             if($("#no")[0] === undefined) {
            document.adminForm.target="_blank";
            if(arg==1){
                document.adminForm.action="../reports/previewreport.php?mode=ProductHistory&type=excel";
                document.adminForm.submit();
            }
            if(arg==2){
                document.adminForm.action="../reports/previewreport.php?mode=ProductHistory&type=csv";
                document.adminForm.submit();  
            }            
            if(arg==3){
                document.adminForm.action="../reports/previewreport.php?mode=ProductHistory&type=word";
                document.adminForm.submit();  
            }
            if(arg==4){
                document.adminForm.action="../reports/previewreport.php?mode=ProductHistory&type=pdf";
                document.adminForm.submit();  
            }            
       
        }else{
             alert("No data available to generate report.");
        } 
 }  
        
    </script>
</html>