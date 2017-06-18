<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if(!isSuperAdmin())
    {
        header("location:../../login.php");
    }

if ($_REQUEST['mode']=="del")
{
	for ($i=0;$i<count($_REQUEST['chkSelect']);$i++)
	{
		mysql_query("update users set Deleted=1 where UserID=".$_REQUEST['chkSelect'][$i]."");
	}

	header("location:viewusers.php?mode=deleted");
	die();
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
                        <h1 class="page-header">View User</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            View User
                            <a href="../user/adduser.php" class="pull-right text-white">Add User</a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                            <?php if($_REQUEST["mode"]=="added"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "User Added Successfully"; ?></strong>
                                </div>
                            <?php } ?>
                            <?php if($_REQUEST["mode"]=="edited"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "User Updated Successfully"; ?></strong>
                                </div>
                            <?php } ?>         
                            <?php if($_REQUEST["mode"]=="deleted"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "User Deleted Successfully"; ?></strong>
                                </div>
                            <?php } ?>                                                     
                            
                           <div id="viewModal" class="modal fade">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <!-- Content will be loaded here from "remote.php" file -->
                                    </div>
                                </div>
                            </div>
                            <form name="adminForm" method="post"> 
                                <div id="myModal" class="modal fade" role="dialog">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            <h4 class="modal-title">Permissions</h4>
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
                                            <input type="checkbox" id="checkAll" />
                                        </th>
                                        <th>
                                            Name
                                        </th>                                    
                                        <th>
                                            Email
                                        </th>
                                        <th>
                                            Contact No
                                        </th>
                                        <th>
                                            User Type
                                        </th>  
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                        $sql = "select * from users where Deleted=0 and UserType!=1";
                                        $sql.= " order by userID";
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
                                                            <input type="checkbox" name="chkSelect[]"  class="check" value="<?php echo $obj->UserID; ?>">
                                                        </td>                                  
                                                        
                                                        <td>
                                                            <?php echo $obj->Name; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $obj->Email; ?>
                                                        </td>
                                                         <td>
                                                            <?php echo $obj->ContactNo; ?>
                                                        </td>
                                                         <td>
                                                            <?php if($obj->UserType=="1") echo "Super Admin";
                                                            else if($obj->UserType=="2") echo "Admin"; 
                                                            else if($obj->UserType=="3") echo "User";  ?>
                                                        </td>
                                                        <td class="action">
                                                            <a href='../user/edituser.php?mode=edit&Id=<?php echo $obj->UserID; ?>'>
                                                                <i class="fa fa-edit">&nbsp;</i>
                                                            </a>
                                                          
                                                            <a  href="javascript:fnDelete('<?php echo $obj->UserID; ?>');" title="Delete">
                                                                <i class="fa fa-remove">&nbsp;</i>
                                                            </a>  
                                                            <a type="button" id="<?php echo $obj->UserID; ?>" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Permissions</a>                                              
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
                            <?php if($numrows>0) { ?>
                             <div class="form-group col-md-4">
                                            <label>Bulk Actions</label>
                                            <select class="form-control" onChange="fnBulk(this.value);" name ="bulk">
                                                <option value="">Select</option>
                                                <option value="del">Delete</option>
                                            </select>
                                        </div>
                            <?php } ?>  
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
            $(this).find(".modal-body").load("../../includes/data.php?mode=permissions&UserID="+link[0].id);
        });
    </script>
</html>