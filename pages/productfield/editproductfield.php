<?php        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    $sql="SELECT * FROM productfields where ProductFieldID='".$_REQUEST['Id']."'";
    $res=mysql_query($sql);
    echo mysql_error();
    $numrows=mysql_num_rows($res); 
    if ($numrows>0) 
    {
        $obj= mysql_fetch_object($res);			
    }

    if ($_REQUEST["mode"]=="update")
    { 
        $ProductFieldName = str_replace("'","`",$_REQUEST["ProductFieldName"]);
        $ProductFieldType = $_REQUEST["ProductFieldType"];
        if(isset($_REQUEST['IsRequired']))
            $IsRequired=1;
        else
            $IsRequired=0;
        $ProductFieldKey = slugify($ProductFieldName);
        
        $sql="select * from productfields where ProductFieldName='".$ProductFieldName."' and ProductFieldID!=".$_REQUEST['Id'];
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);

        if($num==0)
        {
            $sql = "UPDATE productfields SET ";
            $sql.= "ProductFieldName	=	'".$ProductFieldName."',";
            $sql.= "ProductFieldType	=	'".$ProductFieldType."',";
            $sql.= "IsRequired	=	'".$IsRequired."',";
            $sql.= "ProductFieldKey	=	'".$ProductFieldKey."'";
            $sql.= " where ProductFieldID='".$_REQUEST['Id']."'";	
            mysql_query($sql);				

            header("location:viewproductfield.php?mode=edited");
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
                        <h1 class="page-header">Edit Product Field</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Edit Product Field
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
                                     <form name="adminForm" method="post" action="editproductfield.php?mode=update&Id=<?php echo($_REQUEST['Id']); ?>" enctype="multipart/form-data">   
                                        <div class="form-group col-md-6">
                                            <label>Product Field</label>
                                            <input type="text" class="form-control" name="ProductFieldName" required value="<?php echo $obj->ProductFieldName; ?>" />                                            
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Product Field Type</label>
                                            <select class="form-control" name="ProductFieldType" required>
                                                <?php 
                                                if($_REQUEST["ProductFieldType"]=="")
                                                    $_REQUEST["ProductFieldType"] = $obj->ProductFieldType;
                                                fnDropDown("ProductFieldType","ProductFieldType","ProductFieldTypeID","ProductFieldType"); ?>
                                            </select>                                            
                                        </div>    
                                        <div class="form-group col-md-6">
                                            <label>Is Required</label>
                                            <div class="checkbox">
                                                <label>
                                                    <input name="IsRequired" <?php if($obj->IsRequired=="1") echo "checked"; ?> type="checkbox" value="1">
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