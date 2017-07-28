<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');
$error="";
    if ($_REQUEST["mode"]=="Add")
    { 
    $CategoryName = str_replace("'","`",$_REQUEST["CategoryName"]);
    $CategoryDescription = str_replace("'","`",$_REQUEST["CategoryDescription"]);
    $ClientName = str_replace("'","`",$_REQUEST["ClientName"]);
    $ClientID = $_REQUEST["ClientID"];

    if($ClientName!=""){
        $sql="select * from client where ClientName='".$ClientName."'";
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);

        if($num==0)
        {
            $sql = "INSERT INTO client (ClientName)
            VALUES ('$ClientName')";        
            mysql_query($sql);     

            $ClientID = mysql_insert_id();       
        }
        else
        {
            $error="Client Already Exists";
        }        
    }
        
    if($error==""){
        $sql="select * from categories where CategoryName='".$CategoryName."'";
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);

        if($num==0)
        {
            $sql = "INSERT INTO categories (CategoryName,CategoryDescription,ClientID)
            VALUES ('$CategoryName','$CategoryDescription','$ClientID')";        
            mysql_query($sql);
            
            header("location:viewcategories.php?mode=added");
        }
        else
        {
            $error="Category Already Exists";
        }
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
                        <h1 class="page-header">Add Category</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add Category
                                <a href="../category/viewcategories.php" class="pull-right text-white">View Category</a>
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
                                   <form name="adminForm" method="post" action="addcategory.php?mode=Add" enctype="multipart/form-data">   
                                        <div class="form-group col-md-4 drop">
                                            <label>Client Name</label>
                                            <select class="form-control" name="Client" id="Client" required>
                                                <?php fnDropDown("Client","ClientName","ClientID","Client"); ?>
                                            </select>   
                                            <br/>
                                                <button type="button" onClick="javacript:showText()" class="btn btn-danger">Add New</button>                                           
                                        </div>
                                         <div class="form-group col-md-4 text" style="display:none">
                                            <label>Client Name</label>
                                            <input type="text" class="form-control" id="ClientName" name="ClientName" value="<?php echo $_REQUEST['ClientName']; ?>" />                                            
                                                <br/>
                                            <button type="button" onClick="javacript:showDrop()" class="btn btn-danger">Clear</button>   
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label>Category Name</label>
                                            <input type="text" class="form-control" name="CategoryName" required value="<?php echo $_REQUEST['CategoryName']; ?>" />                                            
                                        </div>
                                        
                 
                                        <input type="hidden" name="CategoryDescription" value="" />                          
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
    function showDrop(){
        $(".drop").show();
        $(".text").hide();
        $("#Client").attr("required","");
         $("#ClientName").removeAttr("required");
    }

        function showText(){
            $("#ClientName").val("");
                    $("#ClientName").attr("required","");
         $("#Client").removeAttr("required");
        $(".drop").hide();
        $(".text").show();
    }
    </script>
</html>