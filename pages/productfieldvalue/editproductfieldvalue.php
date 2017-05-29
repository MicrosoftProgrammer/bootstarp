<?php        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    $sql="SELECT * FROM productfieldvalue where ProductFieldValueID='".$_REQUEST['Id']."'";
    $res=mysql_query($sql);
    echo mysql_error();
    $numrows=mysql_num_rows($res); 
    if ($numrows>0) 
    {
        $obj= mysql_fetch_object($res);			
    }

    if ($_REQUEST["mode"]=="update")
    { 
        $name = $_REQUEST["ProductFieldValue"];
        
        $sql="select * from productfieldvalue where ProductFieldValue='".$name."' and ProductFieldValueID!=".$_REQUEST['Id'];
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);

        if($num==0)
        {
            $sql = "UPDATE productfieldvalue SET ";
            $sql.= "ProductFieldValue	=	'".$_REQUEST['ProductFieldValue']."'";
            $sql.= " where ProductFieldValueID='".$_REQUEST['Id']."'";	
            mysql_query($sql);				

            header("location:viewproductfieldvalue.php?mode=edited");
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
                        <h1 class="page-header">Edit Product Field Value</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Edit Product Field Value
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
                                     <form name="adminForm" method="post" action="editproductfieldvalue.php?mode=update&Id=<?php echo($_REQUEST['Id']); ?>" enctype="multipart/form-data">   
                                        <div class="form-group col-md-6">
                                            <label>Product Field Value</label>
                                            <input type="text" class="form-control" name="ProductFieldValue" required value="<?php echo $obj->ProductFieldValue; ?>" />                                            
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