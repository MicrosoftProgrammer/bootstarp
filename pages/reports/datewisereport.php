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
         <?php echo fnDatePickerCss(); ?>
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
                        <h1 class="page-header">Product Transaction Report</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Product Transaction Datewise/Month Report
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
                                    } ?>
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
                                    <div class="form-group col-md-4">
                                        <label>From Date</label>
                                        <div class="input-group date">
                                            <input type="text" 
                                                    class="form-control " 
                                                    name="FromDate" 
                                                    required/>  
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>   
                                        </div>                                       
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>To Date</label>
                                        <div class="input-group date">
                                            <input type="text" 
                                                    class="form-control " 
                                                    name="ToDate" 
                                                    required/>  
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>   
                                        </div>                                       
                                    </div>
                                         <div class="form-group col-md-12">
                                                               <a href="javascript:void(0)" onclick="fnReport(1)" class="btn btn-primary" type="button"><i class="fa fa-file-excel-o fa-2x"></i></a>
                                                               <a href="javascript:void(0)" onclick="fnReport(2)" class="btn btn-primary" type="button"><i class="fa fa-file fa-2x"></i></a>       
                                                               <a href="javascript:void(0)" onclick="fnReport(3)" class="btn btn-primary" type="button"><i class="fa fa-file-word-o fa-2x"></i></a>  
                                                               <a href="javascript:void(0)" onclick="fnReport(4)" class="btn btn-primary" type="button"><i class="fa fa-file-pdf-o fa-2x"></i></a>                                          
                                                            </div>
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
            <script>
        function fnSubmit(){
            document.adminForm.action="datewisereport.php";
            document.adminForm.submit();
        }

        function fnReport(arg){
            if(arg==1){
                document.adminForm.action="../reports/types/excel.php?mode=date";
                document.adminForm.submit();
            }
            if(arg==2){
                document.adminForm.action="../reports/types/csv.php?mode=date";
                document.adminForm.submit();  
            }            
            if(arg==3){
                document.adminForm.action="../reports/types/word.php?mode=date";
                document.adminForm.submit();  
            }
        }   

        
    </script>
                 <?php echo fnDatePickerScript(); ?>
     <script type="text/javascript">
            $(function () {
                $('.date').datetimepicker({
                    format: 'DD/MMM/YYYY'
                });
            });
        </script>
</html>