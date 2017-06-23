<?php        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');



    if ($_REQUEST["mode"]=="update")
    { 
        $Name = str_replace("'","`",$_REQUEST["Name"]);
        $Password = str_replace("'","`",$_REQUEST["Password"]);
        $ContactNo = str_replace("'","`",$_REQUEST["ContactNo"]);
        
        $sql="select * from users where Email='".$Email."' and UserID!=".$_SESSION['UserID'];
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);

        if($num==0)
        {
            $sql = "UPDATE users SET ";
            $sql.= "Name	=	'".$Name."', ";
            if($Password!=""){
                $Password = md5($Password);
                $sql.= "Password	=	'".$Password."',";
            }
            $sql.= "ContactNo	=	'".$ContactNo."'";
            $sql.= " where UserID='".$_SESSION['UserID']."'";	
            mysql_query($sql);	

            $UserAction = "<li>".$_SESSION["Name"]." updatedprofile  at ".date("d-m-Y H:i:s")."</li>";
            $sql="update userlog set UserAction=CONCAT(UserAction,'".$UserAction."') where LogID=".$_SESSION["SessionId"];
            mysql_query($sql);	 			

            $text ="Profile Updated Successfully";
        }
        else
        {
            $error="User Already Exists";
        }        
    }

    $sql="SELECT * FROM users where UserID='".$_SESSION['UserID']."'";
    $res=mysql_query($sql);
    echo mysql_error();
    $numrows=mysql_num_rows($res); 
    if ($numrows>0) 
    {
        $obj= mysql_fetch_object($res);			
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
                        <h1 class="page-header">My Account</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Profile Information
                        </div>
                        <div class="panel-body">
                            <?php if($error!=""){ ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                Error! <strong><?php echo $error; ?></strong>
                            </div>
                            <?php } ?>
                             <?php if($text!=""){ ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                Success! <strong><?php echo $text; ?></strong>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                     <form name="adminForm" method="post" action="profile.php?mode=update" enctype="multipart/form-data">   
                                        <div class="form-group col-md-6">
                                            <label>Name</label>
                                            <input type="text" class="form-control" name="Name" required value="<?php echo $obj->Name; ?>" />                                            
                                        </div>                                        
                                        <div class="form-group col-md-6">
                                            <label>Email</label>
                                             <input type="email" class="form-control" name="Email" readonly value="<?php echo $obj->Email; ?>"/>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Password</label>
                                             <input type="password" class="form-control" name="Password" value=""/>
                                             <p class="help-block">Leave blank, if you are not wishing to change password</p>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label>Contact No</label>
                                             <input type="number" class="form-control" name="ContactNo" required value="<?php echo $obj->ContactNo; ?>"/>
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