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
    $Name = str_replace("'","`",$_REQUEST["Name"]);
    $Password = str_replace("'","`",$_REQUEST["Password"]);
    $Email = str_replace("'","`",$_REQUEST["Email"]);
    $ContactNo = str_replace("'","`",$_REQUEST["ContactNo"]);
    $UserType = str_replace("'","`",$_REQUEST["UserType"]);
    //$permissions ='[{"PageID":1,"PageName":"Dashboard","Page":"index.php","DisplayOrder":1,"Icon":"fa-dashboard","Path":"../dashboard/","Status":1,"SubPage":[]},{"PageID":2,"PageName":"Configuration","Page":"javascript:void(0)","DisplayOrder":2,"Icon":"fa-cogs","Path":"","Status":0,"SubPage":[{"PageID":3,"PageName":"Product Fields","Page":"viewproductfield.php","Icon":"fa-bars","Path":"../productfield/","DisplayOrder":1,"Status":0},{"PageID":4,"PageName":"Product Field Types","Page":"viewproductfieldtype.php","Icon":"fa-exchange","Path":"../productfieldtype/","DisplayOrder":2,"Status":0},{"PageID":5,"PageName":"Product Field Values","Page":"viewproductfieldvalue.php","Icon":"fa-flag","Path":"../productfieldvalue/","DisplayOrder":3,"Status":0},{"PageID":6,"PageName":"Category Field Mapping","Page":"fieldmapping.php","Icon":"fa-map","Path":"../mapping/","DisplayOrder":4,"Status":0}]},{"PageID":7,"PageName":"Users","Page":"javascript:void(0)","Icon":"fa-users","Path":"","DisplayOrder":3,"Status":0,"SubPage":[{"PageID":8,"PageName":"User","Page":"viewusers.php","Icon":"fa-user","Path":"../user/","DisplayOrder":1,"Status":0},{"PageID":9,"PageName":"Permissions","Page":"permissions.php","Icon":"fa-sitemap","Path":"../permissions/","DisplayOrder":2,"Status":0}]},{"PageID":10,"PageName":"Products","Page":"javascript:void(0)","DisplayOrder":4,"Icon":"fa-tags","Path":"","Status":0,"SubPage":[{"PageID":11,"PageName":"Categories","Page":"viewcategories.php","Icon":"fa-shopping-cart","Path":"../category/","DisplayOrder":1,"Status":0},{"PageID":12,"PageName":"Products","Page":"viewproducts.php","Icon":"fa-list","Path":"../product/","DisplayOrder":2,"Status":0}]},{"PageID":13,"PageName":"Reports","Page":"javascript:void(0)","Icon":"fa-files-o","Path":"","DisplayOrder":5,"Status":0,"SubPage":[{"PageID":14,"PageName":"Product Report","Page":"productreport.php","Icon":"fa-files-o","Path":"../reports/","DisplayOrder":1,"Status":0},{"PageID":15,"PageName":"Overview Report","Page":"overviewreport.php","Icon":"fa-file-excel-o","Path":"../reports/","DisplayOrder":2,"Status":0},{"PageID":16,"PageName":"Product History Report","Page":"producthistoryreport.php","Icon":"fa-history","Path":"../reports/","DisplayOrder":3,"Status":0},{"PageID":17,"PageName":"Date/Month Wise Report","Page":"datewisereport.php","Icon":"fa-calendar","Path":"../reports/","DisplayOrder":4,"Status":0},{"PageID":18,"PageName":"Invoice Generatort","Page":"invoice.php","Icon":"fa-file-pdf-o","Path":"../reports/","DisplayOrder":5,"Status":0}]}]';
    if($UserType=="2")
    {
        $permissions ='[{"PageID":1,"PageName":"Dashboard","Page":"index.php","DisplayOrder":1,"Icon":"fa-dashboard","Path":"../dashboard/","Status":1,"SubPage":[]},{"PageID":7,"PageName":"Users","Page":"javascript:void(0)","Icon":"fa-users","Path":"","DisplayOrder":3,"Status":1,"SubPage":[{"PageID":9,"PageName":"Permissions","Page":"permissions.php","Icon":"fa-sitemap","Path":"../permissions/","DisplayOrder":2,"Status":1}]},{"PageID":10,"PageName":"Products","Page":"javascript:void(0)","DisplayOrder":4,"Icon":"fa-tags","Path":"","Status":0,"SubPage":[{"PageID":11,"PageName":"Categories","Page":"viewcategories.php","Icon":"fa-shopping-cart","Path":"../category/","DisplayOrder":1,"Status":0},{"PageID":12,"PageName":"Products","Page":"viewproducts.php","Icon":"fa-list","Path":"../product/","DisplayOrder":2,"Status":0}]},{"PageID":13,"PageName":"Reports","Page":"javascript:void(0)","Icon":"fa-files-o","Path":"","DisplayOrder":5,"Status":0,"SubPage":[{"PageID":14,"PageName":"Product Report","Page":"productreport.php","Icon":"fa-files-o","Path":"../reports/","DisplayOrder":1,"Status":0},{"PageID":15,"PageName":"Overview Report","Page":"overviewreport.php","Icon":"fa-file-excel-o","Path":"../reports/","DisplayOrder":2,"Status":0},{"PageID":16,"PageName":"Product History Report","Page":"producthistoryreport.php","Icon":"fa-history","Path":"../reports/","DisplayOrder":3,"Status":0},{"PageID":17,"PageName":"Date/Month Wise Report","Page":"datewisereport.php","Icon":"fa-calendar","Path":"../reports/","DisplayOrder":4,"Status":0},{"PageID":18,"PageName":"Invoice Generator","Page":"invoice.php","Icon":"fa-file-pdf-o","Path":"../reports/","DisplayOrder":5,"Status":0},{"PageID":19,"PageName":"General Invoice","Page":"invoicegen.php","Icon":"fa-print","Path":"../reports/","DisplayOrder":6,"Status":0}]}]';
    }
    else if($UserType=="3")
    {
        $permissions ='[{"PageID":1,"PageName":"Dashboard","Page":"index.php","DisplayOrder":1,"Icon":"fa-dashboard","Path":"../dashboard/","Status":1,"SubPage":[]},{"PageID":10,"PageName":"Products","Page":"javascript:void(0)","DisplayOrder":4,"Icon":"fa-tags","Path":"","Status":0,"SubPage":[{"PageID":11,"PageName":"Categories","Page":"viewcategories.php","Icon":"fa-shopping-cart","Path":"../category/","DisplayOrder":1,"Status":0},{"PageID":12,"PageName":"Products","Page":"viewproducts.php","Icon":"fa-list","Path":"../product/","DisplayOrder":2,"Status":0}]},{"PageID":13,"PageName":"Reports","Page":"javascript:void(0)","Icon":"fa-files-o","Path":"","DisplayOrder":5,"Status":0,"SubPage":[{"PageID":14,"PageName":"Product Report","Page":"productreport.php","Icon":"fa-files-o","Path":"../reports/","DisplayOrder":1,"Status":0},{"PageID":15,"PageName":"Overview Report","Page":"overviewreport.php","Icon":"fa-file-excel-o","Path":"../reports/","DisplayOrder":2,"Status":0},{"PageID":16,"PageName":"Product History Report","Page":"producthistoryreport.php","Icon":"fa-history","Path":"../reports/","DisplayOrder":3,"Status":0},{"PageID":17,"PageName":"Date/Month Wise Report","Page":"datewisereport.php","Icon":"fa-calendar","Path":"../reports/","DisplayOrder":4,"Status":0},{"PageID":18,"PageName":"Invoice Generator","Page":"invoice.php","Icon":"fa-file-pdf-o","Path":"../reports/","DisplayOrder":5,"Status":0},{"PageID":19,"PageName":"General Invoice","Page":"invoicegen.php","Icon":"fa-print","Path":"../reports/","DisplayOrder":6,"Status":0}]}]';
    }

        
    $sql="select * from users where Email='".$Email."'";
    $res=mysql_query($sql);
    $num=mysql_num_rows($res);
    $Password = md5($Password);
    if($num==0)
    {
        $sql = "INSERT INTO users (Name,Password,Email,ContactNo,UserType,Permissions)
        VALUES ('$Name','$Password','$Email','$ContactNo','$UserType','$permissions')";        
        mysql_query($sql);

                    $UserAction = "<li>".$_SESSION["Name"]." added user ".$Name."  at ".date("d-m-Y H:i:s")."</li>";
            $sql="update userlog set UserAction=CONCAT(UserAction,'".$UserAction."') where LogID=".$_SESSION["SessionId"];
            mysql_query($sql);	 
        
        header("location:viewusers.php?mode=added");
    }
    else
    {
        $error="User Already Exists";
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
                        <h1 class="page-header">Add User</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add User
                                <a href="../user/viewusers.php" class="pull-right text-white">View User</a>
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
                                   <form name="adminForm" method="post" action="adduser.php?mode=Add" enctype="multipart/form-data">   
                                        <div class="form-group col-md-6">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="Name" required value="<?php echo $_REQUEST['Name']; ?>" />                                            
                                        </div>                                        
                                        <div class="form-group col-md-6">
                                            <label>Email</label>
                                             <input type="email" class="form-control" name="Email" required value="<?php echo $_REQUEST['Email']; ?>"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Password</label>
                                             <input type="password" class="form-control" name="Password" required value="<?php echo $_REQUEST['Password']; ?>"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Contact No</label>
                                             <input type="number" class="form-control" name="ContactNo" required value="<?php echo $_REQUEST['ContactNo']; ?>"/>
                                        </div>  
                                        <div class="form-group col-md-6">
                                            <label>User Type</label>
                                             <select class="form-control" name="UserType" required>
                                                <option value=''>Select</option>
                                                <option value='2'>Admin</option>
                                                <option value='3'>User</option>
                                             </select>
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