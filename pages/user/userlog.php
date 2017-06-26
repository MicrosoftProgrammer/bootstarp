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
                                                            <div id="myModal" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">User Actions Performed</h4>
                                        </div>
                                        <div class="modal-body">
                                            <p>Loading...</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                                        </div>
                                        </div>

                                    </div>
                                </div>
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
                                            MAC Address
                                        </th>
                                        <th>
                                            Logout Time
                                        </th>  
                                        <th>
                                            Browser
                                        </th>
                                         <th>
                                            Action
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
                                                            <?php echo ConvertToCustomDateTime($obj->LoggedInTime); ?>
                                                        </td>
                                                         <td>
                                                            <?php echo $obj->IPAddress; ?>
                                                        </td>
                                                         <td>
                                                            <?php echo $obj->MACAddress; ?>
                                                        </td>                                                        
                                                         <td>
                                                            <?php if($obj->LogoutTime=="0000-00-00 00:00:00") echo "UnExpected Logout"; else echo ConvertToCustomDateTime($obj->LogoutTime); ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $obj->Browser; ?>                                            
                                                        </td>
                                                         <td>
                                                           <a type="button" id="<?php echo $obj->LogID; ?>" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Actions</a>                                                                                     
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
        <script>
        $('#myModal').on('shown.bs.modal', function (e) {
           var link = $(e.relatedTarget);          
            $(this).find(".modal-body").load("../../includes/data.php?mode=log&LogID="+link[0].id);
        });
    </script>
</html>