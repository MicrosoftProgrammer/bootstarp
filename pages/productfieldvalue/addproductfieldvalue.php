<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if(!isSuperAdmin())
    {
        header("location:../../login.php");
    }

    if ($_REQUEST["mode"]=="Add")
    { 
        $ProductFieldValue = str_replace("'","`",$_REQUEST["ProductFieldValue"]);
        $FieldMappingID =$_REQUEST["FieldMappingID"];
        $Category =$_REQUEST["Category"];
            
        $sql="select * from productfieldvalue where ProductFieldValue='".$ProductFieldValue."' and
        FieldMappingID='".$FieldMappingID."' and
        CategoryID='".$Category."'";
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);

        if($num==0)
        {
            $sql = "INSERT INTO productfieldvalue (ProductFieldValue,FieldMappingID,CategoryID)
            VALUES ('$ProductFieldValue','$FieldMappingID','$Category')";        
            mysql_query($sql);
            
            header("location:viewproductfieldvalue.php?mode=added");
        }
        else
        {
            $error="Product Field Value Already Exists";
        }
   }
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
                        <h1 class="page-header">Add Product Field Value</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add Product Field Value
                                <a href="../productfieldvalue/viewproductfieldvalue.php" class="pull-right text-white">View Product Field Value</a>
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
                                   <form name="adminForm" method="post" action="addproductfieldvalue.php?mode=Add" enctype="multipart/form-data">                                    
                                      <div class="form-group col-md-6">
                                            <label>Category Name</label>
                                            <select class="form-control" name="Category" onchange="fnSubmit();" required>
                                                <?php fnDropDown("categories","CategoryName","CategoryID","Category"); ?>
                                            </select>                                               
                                        </div>
                                           <div class="form-group col-md-6">
                                            <label>Product Field Type</label>
                                            <select class="form-control" name="FieldMappingID"  required>                                                
                                                <?php 
                                                $CategoryID = $_REQUEST["Category"];
                                                    echo '<option value="" >Select</option>';
                                                    $sql = "select * from productfields pf 
                                                            inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                                            where fm.CategoryID=".$CategoryID." and fm.Deleted=0 and ProductFieldType in ('2','3','9') order by fm.DisplayOrder";        
                                                    $res=mysql_query($sql);
                                                    $numrows=mysql_num_rows($res);
                                                    if($numrows>0)
                                                    {
                                                        while($obj=mysql_fetch_object($res))
                                                        {
                                                            echo '<option value="'.$obj->FieldMappingID.'">'.$obj->ProductFieldName.'</option>';
                                                        }
                                                    }
                                                
                                                ?>
                                            </select>                                               
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Product Field Value</label>
                                            <input type="text" class="form-control" name="ProductFieldValue" required value="<?php echo $_REQUEST['ProductFieldValue']; ?>" />                                            
                                        </div>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
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
            document.adminForm.action="addproductfieldvalue.php";
            document.adminForm.submit();
        }
    </script>
</html>