<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

if ($_REQUEST['mode']=="del")
{
	for ($i=0;$i<count($_REQUEST['chkSelect']);$i++)
	{
		mysql_query("update categories set Deleted=1 where CategoryID=".$_REQUEST['chkSelect'][$i]."");
	}

	header("location:viewcategories.php?mode=deleted");
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
                        <h1 class="page-header">View Category</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            View Category
                            <a href="../category/addcategory.php" class="pull-right text-white">Add Category</a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                            <?php if($_REQUEST["mode"]=="added"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Category Added Successfully"; ?></strong>
                                </div>
                            <?php } ?>
                            <?php if($_REQUEST["mode"]=="edited"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Category Updated Successfully"; ?></strong>
                                </div>
                            <?php } ?>         
                            <?php if($_REQUEST["mode"]=="deleted"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Category Deleted Successfully"; ?></strong>
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
                                            Description
                                        </th>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                        $sql = "select * from categories where Deleted=0";
                                        $sql.= " order by CategoryName";
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
                                                            <input type="checkbox" name="chkSelect[]"  class="check"value="<?php echo $obj->CategoryID; ?>">
                                                        </td>                                  
                                                        
                                                        <td>
                                                            <?php echo $obj->CategoryName; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $obj->CategoryDescription; ?>
                                                        </td>
                                                        <td class="action">
                                                            <a href='../category/editcategory.php?mode=edit&Id=<?php echo $obj->CategoryID; ?>'>
                                                                <i class="fa fa-edit">&nbsp;</i>
                                                            </a>
                                                            <a  href="javascript:fnDelete('<?php echo $obj->CategoryID; ?>');" title="Delete">
                                                                <i class="fa fa-remove">&nbsp;</i>
                                                            </a>                                                     
                                                        </td>
                                                    </tr>  
                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                                echo '<tr class="alt"><td colspan="8"><b style="color:red;">No Category found.</b></td></tr>';
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
</html>