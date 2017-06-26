<?php

    include("includes/connection.php");
    include("includes/helpers.php");

    if ($_REQUEST["mode"]=="login")
    {
        $Email = $_REQUEST["Email"];
        $Password = $_REQUEST["Password"];
        $sql="select * from users where Deleted=0 and Email='".$Email."' and Password='".md5($Password)."'";
        $res=mysql_query($sql);
        $num=mysql_num_rows($res);
        
        if($num==0)
        {
            $text="Invalid Email / Password.";
        }
        else
        {
            $obj=mysql_fetch_object($res);
            $_SESSION["UserType"]=$obj->UserType;
            $_SESSION["Name"]=$obj->Name;
            $_SESSION["Email"]=$obj->Email;
            $_SESSION["UserID"]=$obj->UserID;
            $_SESSION["Permissions"]=$obj->Permissions;
            $ip = $_SERVER['REMOTE_ADDR'];
            $browser= $_REQUEST['browser'];
            $UserAction = "<li>".$_SESSION["Name"]." logged in at ".date("d-m-Y H:i:s")."</li>";
            $Mac = GetMAC();
            $sql="insert into userlog(LoggedInUser,IPAddress,Browser,UserAction,MACAddress) values('$obj->UserID','$ip','$browser','$UserAction','$Mac')";
            mysql_query($sql);
            
            $_SESSION["SessionId"]= mysql_insert_id();
            header("location:pages/dashboard/index.php");
        }
    }

    if ($_REQUEST["mode"]=="logout")
    {
        $_SESSION["Email"]="";
        $_SESSION["UserType"]="";
        $_SESSION["UserID"]="";
        $_SESSION["Name"]="";
        $_SESSION["Permissions"]="";

        $sql ="update `userlog` set `LogoutTime`=CURRENT_TIMESTAMP where LogID=".$_SESSION["SessionId"];
        mysql_query($sql);
        $_SESSION["SessionId"]="";
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
                    <div class="panel-heading" align="center">
                        <img src="images/<?php echo $_SESSION['Logo']; ?>" alt="Logo" class="logo" />
                        <h3 class="text-center"><?php echo $_SESSION["CompanyName"]; ?></h3>
                    </div>
                    <div class="panel-body">
                        <form name="adminForm" method="post" action="login.php?mode=login">
                            <fieldset>
                             <input name="browser" type="hidden" id="browser" />
                                <div class="form-group">
                                    <input name="Email" required type="email" class="form-control" placeholder="User Name" autofocus/>
                                </div>
                                <div class="form-group">
                                    <input class="form-control" required placeholder="Password" name="Password" type="password" />
                                </div>
                                <!-- Change this to a button or input when using this as a form -->
                                <button type="submit" class="btn btn-lg btn-primary btn-block">Login</button>
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
<script>
    var browser = '';
var browserVersion = 0;

if (/Opera[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
    browser = 'Opera';
} else if (/MSIE (\d+\.\d+);/.test(navigator.userAgent)) {
    browser = 'MSIE';
} else if (/Navigator[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
    browser = 'Netscape';
} else if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
    browser = 'Chrome';
} else if (/Safari[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
    browser = 'Safari';
    /Version[\/\s](\d+\.\d+)/.test(navigator.userAgent);
    browserVersion = new Number(RegExp.$1);
} else if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)) {
    browser = 'Firefox';
}
if(browserVersion === 0){
    browserVersion = parseFloat(new Number(RegExp.$1));
}
$("#browser").val(browser + " " + browserVersion);

</script>
</body>

</html>
