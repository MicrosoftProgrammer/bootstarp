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
                        <h1 class="page-header">Overview Report</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Product Overview Report
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
                                   <form name="adminForm" method="post" enctype="multipart/form-data">   
                                        <div class="form-group col-md-3">
                                            <label>Category Name</label>
                                            <select class="form-control" name="Category" onchange="fnSubmit();" required>
                                                <?php fnDropDown("categories","CategoryName","CategoryID","Category"); ?>
                                            </select>                                               
                                        </div>
                                    <?php if($_REQUEST["Category"]!="" ) {

                                        $html ='<div class="form-group col-md-3">
                                            <label>Sum By</label>
                                            <select class="form-control" name="Sum"  onchange="fnSubmit();"  required>';
                                        
                                        $CategoryID = $_REQUEST["Category"];
                                            $sql="select *, pft.ProductFieldType as Type from productfields pf 
                                                inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                                inner join productfieldtype pft on pf.ProductFieldType = pft.ProductFieldTypeID   
                                                where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                                $res = mysql_query($sql);
                                                echo ' <div class="form-group col-md-6">
                                                        <label style="display:block">Group By</label>';
                                                        $x=0;
                                                        $y=0;
                                                        $groupheader="";
                                                        $sumheader="";
                                                while($obj=mysql_fetch_object($res)){
                                                    $selected="";
                                                    if($obj->Type=="Number") {
                                                          if($y==0 && $_REQUEST["Sum"]==""){
                                                            $_REQUEST["Sum"] = $obj->ProductFieldName;
                                                             $selected ="selected";
                                                        }
                                                         else if($_REQUEST["Sum"] == $obj->ProductFieldName){
                                                            $selected ="selected";
                                                        }

                                                        if($selected!=""){
                                                            $sumheader=$obj->ProductFieldName;
                                                        }
                                                        $html=$html.'<option '.$selected.' value="'.$obj->ProductFieldName.'">'.$obj->ProductFieldName.'</option>';
                                                    } 

                                                    if($obj->ShowInFilter=="1"){
                                                        array_push($filter,
                                                        array("Key"=>$obj->ProductFieldKey,
                                                        "Name"=>$obj->ProductFieldName));
                                                        $checked ="";
                                                        if($x==0 && $_REQUEST["groups"]==""){
                                                            $checked ="checked";
                                                            $_REQUEST["groups"] = $obj->ProductFieldKey;
                                                            $x++;
                                                        }
                                                        else if($_REQUEST["groups"] == $obj->ProductFieldKey){
                                                            $checked ="checked";
                                                        }

                                                        if($checked!=""){
                                                            $groupheader=$obj->ProductFieldKey;
                                                        }
                                                        echo '         
                                                        <label class="radio-inline">
                                                            <input type="radio"  onchange="fnSubmit();"  onchange="fnSubmit();"  name="groups" id="'.$obj->ProductFieldKey.'" value="'.$obj->ProductFieldKey.'" '.$checked.' >'.$obj->ProductFieldName.'
                                                        </label>';
                                                       
                                                    }
                                                }

                                                $html=$html.'</select></div>';
                                                
                                                echo '</div>'.$html.'<textarea name="filters" style="display:none;">'.json_encode($filter).'</textarea>';   
                                                if($sumheader!="" && $groupheader!=""){
                                                echo ' <div class="form-group col-md-12">
                                                               <a href="javascript:void(0)" onclick="fnReport(1)"><img src="../../images/excel.png"alt="excel" /></a>
                                                               <a href="javascript:void(0)" onclick="fnReport(2)"><img src="../../images/csv.png"alt="csv" /></a>       
                                                               <a href="javascript:void(0)" onclick="fnReport(3)"><img src="../../images/word.png"alt="word" /></a>  
                                                               <a href="javascript:void(0)" onclick="fnReport(4)"><img src="../../images/pdf.png"alt="pdf" /></a>                                          
                                                            </div>';   
                                                }
            
                                echo '<table width="100%" class="table table-striped table-bordered table-hover" id="dataTable-example">';
                                    if($sumheader!="" && $groupheader!="") {
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
                                       
                                        echo "<th>".$groupheader."</th>";
                                         echo "<th>".$sumheader."</th>";
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
                                    else{
                                       echo "<tr>";
                                                echo "<td>No data Available</td>";
                                                echo "</tr>";   
                                    }

                                     echo "</table>";                  
                                    } ?>


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
    <script>
    $(document).ready(function() {
        $("#dataTable-example").DataTable({
        });
    });
        function fnSubmit(){
            document.adminForm.target="_self";
            document.adminForm.action="overviewreport.php";
            document.adminForm.submit();
        }

        function fnReport(arg){
            document.adminForm.target="_blank";
            if(arg==1){
                document.adminForm.action="../reports/previewreport.php?mode=Overview&type=excel";
                document.adminForm.submit();
            }
            if(arg==2){
                document.adminForm.action="../reports/previewreport.php?mode=Overview&type=csv";
                document.adminForm.submit();  
            }            
            if(arg==3){
                document.adminForm.action="../reports/previewreport.php?mode=Overview&type=word";
                document.adminForm.submit();  
            }
            if(arg==4){
                document.adminForm.action="../reports/previewreport.php?mode=Overview&type=pdf";
                document.adminForm.submit();  
            }
        }              
    </script>
</html>