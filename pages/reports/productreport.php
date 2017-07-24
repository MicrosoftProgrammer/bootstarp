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
                        <h1 class="page-header">Report</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Product Report
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
                                   <form name="adminForm" method="post"  enctype="multipart/form-data">   
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
                                                                    <select class="form-control" onchange="fnSubmit();"  name="'.$obj->ProductFieldKey.'" onchange="fnSubmit();">
                                                                        '.fnGetFilter($obj->ProductFieldName,$obj->ProductFieldKey,$CategoryID ).'
                                                                    </select>                                               
                                                            </div>';
                                                    }
                                                }
                                                echo ' <div class="form-group col-md-12">
                                                               <a href="javascript:void(0)" onclick="fnReport(1)"><img src="../../images/excel.png"alt="excel" /></a>
                                                               <a href="javascript:void(0)" onclick="fnReport(2)"><img src="../../images/csv.png"alt="csv" /></a>       
                                                               <a href="javascript:void(0)" onclick="fnReport(3)"><img src="../../images/word.png"alt="word" /></a>  
                                                               <a href="javascript:void(0)" onclick="fnReport(4)"><img src="../../images/pdf.png"alt="pdf" /></a>                                          
                                                            </div>';
                                                echo '<textarea name="filters" style="display:none;">'.json_encode($filter).'</textarea>';          
                                    } ?>
                                       <?php if($_REQUEST["Category"]!="") { 
                                        $sql = "select * from products p inner join categories c on p.CategoryID =c.CategoryID 
                                                where p.Deleted=0";
                                        if($_REQUEST["Category"]!=""){
                                            $sql= $sql." and p.CategoryID=".$_REQUEST["Category"];
                                        }
                                        $sql.= " order by p.ProductID";
                                        $res=mysql_query($sql);
                                        $numrows=mysql_num_rows($res);
                                        $objFields=mysql_fetch_object($res);
                                        $data = json_decode($objFields->Fields, TRUE);
                                echo '<div id="divLoading">
                                        <p>
                                        Loading, please wait...
                                        <i class="fa fa-spinner fa-spin fa-2x fa-fw"></i>
                                    </p></div>';                                        
                                       ?>
                                       <div class="col-md-12" style="width:100%;overflow-x:scroll">
<table width="100%" class="table table-striped table-bordered table-hover" id="dataTable-example">
                                <thead>
                                    <tr>
                                        <?php
                                        $headers = array();
                                            if($numrows>0)
                                            {
                                                foreach(array_keys($data) as $key) {
                                                    $cls="nprn";      
                                                    if(strlen($key)>10){
                                                        $cls="cell";
                                                    }
                                                    echo "<th style='white-space: nowrap;' class='".$cls."'>".$key."</th>";
                                                    array_push($headers,$key);
                                                }                                                
                                            }
                                        ?>

                                </thead>
                                <tbody>
                                <?php
                              
                                        	if($numrows>0)
                                            {
                                                $cnt=0;
                                                $res=mysql_query($sql);
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
                                                    $data = json_decode($obj->Fields, TRUE);
                                                    
                                        
                                                    if($showdata && count($data)>0) {
                                                        
                                                    ?>
                                                       
                                                    <tr <?php echo $class; ?>>
                                                                                    
                                                        
                                                            <?php 
                                                            $count=0;
                                                            

                                                                foreach($headers as $value) {
                                                            $cls="nprn";                                                                   $cls="";
                                                           if(strlen($data[$value])>10){
                                                               $cls="cell";
                                                           }
                                                                    echo "<td><span class='".$cls."'>".$data[$value]."</span></td>";
                                                                }
                                                           
                                                           ?>
                                                        
                                                    </tr>  
                                                    <?php
                                                }
                                                }
                                            }
                                            else
                                            {
                                                echo '<tr id="no" class="alt"><td colspan="8"><b style="color:red;">No Product found.</b></td></tr>';
                                            }                                
                                ?>
                                </tbody>
                            </table>
                            </div>
                            <?php } ?>
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
                        $("#dataTable-example").on( 'draw.dt', function () {
                            $("#divLoading").hide();
                                }).DataTable({
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
            document.adminForm.action="productreport.php";
            document.adminForm.submit();
        }

        function fnReport(arg){
            if($("#no")[0] === undefined) {
                document.adminForm.target="_blank";
                if(arg==1){
                    document.adminForm.action="../reports/previewreport.php?mode=Product&type=excel";
                    document.adminForm.submit();
                }
                if(arg==2){
                    document.adminForm.action="../reports/previewreport.php?mode=Product&type=csv";
                    document.adminForm.submit();  
                }            
                if(arg==3){
                    document.adminForm.action="../reports/previewreport.php?mode=Product&type=word";
                    document.adminForm.submit();  
                }
                if(arg==4){
                    document.adminForm.action="../reports/previewreport.php?mode=Product&type=pdf";
                    document.adminForm.submit();  
                }
            }else{
                alert("No data available to generate report.");
            }
        }                
    </script>
</html>