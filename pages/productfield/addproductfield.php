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
        $ProductFieldName = str_replace("'","`",$_REQUEST["ProductFieldName"]);
        $ProductFieldName = str_replace(","," ",$ProductFieldName);
        $ProductFieldType = $_REQUEST["ProductFieldType"];
        if(isset($_REQUEST['IsRequired']))
            $IsRequired=1;
        else
            $IsRequired=0;

        if(isset($_REQUEST['ShowInFilter']))
            $ShowInFilter=1;
        else
            $ShowInFilter=0;

        $ProductFieldKey = slugify($ProductFieldName);
            
        $sql="select * from productfields where ProductFieldName='".$ProductFieldName."'";
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);

        if($num==0)
        {
            $sql = "INSERT INTO productfields (ProductFieldName,ProductFieldType,IsRequired,ProductFieldKey,ShowInFilter)
            VALUES ('$ProductFieldName','$ProductFieldType','$IsRequired','$ProductFieldKey','$ShowInFilter')";        
            mysql_query($sql);
            header("location:viewproductfield.php?mode=added");
        }
        else
        {
            $error="Product Field Already Exists";
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
                        <h1 class="page-header">Add Product Field</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add Product Field
                                <a href="../productfield/viewproductfield.php" class="pull-right text-white">View Product Field</a>
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
                                   <form name="adminForm" method="post" action="addproductfield.php?mode=Add" enctype="multipart/form-data">   
                                        <div class="form-group col-md-6">
                                            <label>Product Field</label>
                                            <input type="text" class="form-control" name="ProductFieldName" required value="<?php echo $_REQUEST['ProductFieldName']; ?>" />                                            
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Product Field Type</label>
                                            <select class="form-control" name="ProductFieldType" required>
                                                <?php fnDropDown("ProductFieldType","ProductFieldType","ProductFieldTypeID","ProductFieldType"); ?>
                                            </select>                                            
                                        </div>    
                                        <div class="form-group col-md-6">
                                            <label>Is Required</label>
                                            <div class="checkbox">
                                                <label>
                                                    <input name="IsRequired" type="checkbox" value="1">
                                                </label>
                                            </div>                                          
                                        </div>   
                                        <div class="form-group col-md-6">
                                            <label>Show In Filter</label>
                                            <div class="checkbox">
                                                <label>
                                                    <input name="ShowInFilter" type="checkbox" value="1">
                                                </label>
                                            </div>                                          
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
</html>