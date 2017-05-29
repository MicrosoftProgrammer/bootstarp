<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if ($_REQUEST["mode"]=="Add")
    { 
        $ProductFieldType = str_replace("'","`",$_REQUEST["ProductFieldType"]);
            
        $sql="select * from productfieldtype where ProductFieldType='".$ProductFieldType."'";
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);

        if($num==0)
        {
            $sql = "INSERT INTO productfieldtype (ProductFieldType)
            VALUES ('$ProductFieldType')";        
            mysql_query($sql);
            
            header("location:viewproductfieldtype.php?mode=added");
        }
        else
        {
            $error="Product Field Type Already Exists";
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
                        <h1 class="page-header">Add Product Field Type</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add Product Field Type
                                <a href="../productfieldtype/viewproductfieldtype.php" class="pull-right text-white">View Product Field Type</a>
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
                                   <form name="adminForm" method="post" action="addproductfieldtype.php?mode=Add" enctype="multipart/form-data">   
                                        <div class="form-group col-md-6">
                                            <label>Product Field Type</label>
                                            <input type="text" class="form-control" name="ProductFieldType" required value="<?php echo $_REQUEST['ProductFieldType']; ?>" />                                            
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