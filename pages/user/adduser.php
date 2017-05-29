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
        
    $sql="select * from users where Email='".$Email."'";
    $res=mysql_query($sql);
    $num=mysql_num_rows($res);
    $Password = md5($Password);
    if($num==0)
    {
        $sql = "INSERT INTO users (Name,Password,Email,ContactNo,UserType)
        VALUES ('$Name','$Password','$Email','$ContactNo','$UserType')";        
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