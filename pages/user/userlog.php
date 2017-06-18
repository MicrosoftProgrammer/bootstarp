<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if(!isSuperAdmin())
    {
        header("location:../../login.php");
    }

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>
        <?php echo fnDataTableCSS(); ?>
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
                        <h1 class="page-header">View User Logs</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            View User logs
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <form name="adminForm" method="post"> 
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTables-example">
                                <thead>
                                    <tr>
                                        <th>
                                            Name
                                        </th>                                    
                                        <th>
                                            LoggedIn Time
                                        </th>
                                        <th>
                                            IP Address
                                        </th>
                                        <th>
                                            Logout Time
                                        </th>  
                                        <th>
                                            Browser
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                        $sql = "select * from userlog ul inner join users u on u.UserID = ul.LoggedInUser";
                                        $sql.= " order by LoggedInTime desc";
                                        $res=mysql_query($sql);
                                        $numrows=mysql_num_rows($res);
                                        	if($numrows>0)
                                            {
                                                $cnt=0;
                                                while($obj=mysql_fetch_object($res))
                                                { 
                                                    $cnt++;
                                                    if($cnt%2==0) $class=""; else $class="class=alt";
                                                    ?>
                                                    <tr <?php echo $class; ?>>
                                
                                                        
                                                        <td>
                                                            <?php echo $obj->Name; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $obj->LoggedInTime; ?>
                                                        </td>
                                                         <td>
                                                            <?php echo $obj->IPAddress; ?>
                                                        </td>
                                                         <td>
                                                            <?php echo $obj->LogoutTime; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $obj->Browser; ?>                                            
                                                        </td>
                                                    </tr>  
                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                                echo '<tr class="alt"><td colspan="8"><b style="color:red;">No User found.</b></td></tr>';
                                            }                                
                                ?>
                                </tbody>
                            </table>
                            </form>                            
                            <!-- /.table-responsive -->    
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
    <?php echo fnDataTableScript(); ?>
</html>