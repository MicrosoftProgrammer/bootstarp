<?php

    include("includes/connection.php");
    include("includes/helpers.php");

    if ($_REQUEST["mode"]=="login")
    {
        $_SESSION["admin"]="1";
        $_SESSION["id"]="1";
        $_SESSION["name"]="Name";
        $_SESSION["superadmin"]="1";
            
        header("location:pages/dashboard/index.php");
    }

    if ($_REQUEST["mode"]=="logout")
    {
        $_SESSION["admin"]="";
        $_SESSION["superadmin"]="";
        $_SESSION["id"]="";
        $_SESSION["name"]="";
    }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php echo fnMetaHeaders() ?>
    <title><?php echo $_SESSION["CompanyName"]; ?></title>

    <!-- Bootstrap Core CSS -->
    <link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

    <div class="container">
        <div class="row">

            <div class="col-md-4 col-md-offset-4">
                                       
                <div class="login-panel panel panel-default">
                       <?php if($text!=""){ ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Login Error! <strong><?php echo $text; ?></strong>
                                </div>
                            <?php } ?>  
                    <div class="panel-heading">
                        <img src="images/logo.png" alt="Logo" class="img-responsive" />
                        <h1>Company Name</h1>
                    </div>
                    <div class="panel-body">
                        <form name="adminForm" method="post" action="login.php?mode=login">
                            <fieldset>
                                <div class="form-group">
                                    <input name="UserName" required type="text" class="form-control" placeholder="User Name" autofocus/>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" required placeholder="Password" name="Password" type="password" />
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" class="btn btn-lg btn-success btn-block">Login</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery -->
    <script src="vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="dist/js/sb-admin-2.js"></script>

</body>

</html>
