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
		mysql_query("update productfieldsset Deleted=1 where ProductFieldID=".$_REQUEST['chkSelect'][$i]."");
	}

	header("location:viewproductfield.php?mode=deleted");
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
                        <h1 class="page-header">Product Field</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            View Product Field
                            <a href="../productfield/addproductfield.php" class="pull-right text-white">Add Product Field</a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                            <?php if($_REQUEST["mode"]=="added"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Product Field Added Successfully"; ?></strong>
                                </div>
                            <?php } ?>
                            <?php if($_REQUEST["mode"]=="edited"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Product Field Updated Successfully"; ?></strong>
                                </div>
                            <?php } ?>         
                            <?php if($_REQUEST["mode"]=="deleted"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Product Field Deleted Successfully"; ?></strong>
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
                                            Product Field
                                        </th>
                                         <th>
                                            Product Field Type
                                        </th>
                                         <th>
                                            Is Required
                                        </th>
                                    
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                        $sql = "select * from productfields pf inner join productfieldtype pt
                                        on pf.ProductFieldType= pt.ProductFieldTypeID where pf.Deleted=0";
                                        $sql.= " order by pf.ProductFieldID";
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
                                                            <input type="checkbox" name="chkSelect[]"  class="check" value="<?php echo $obj->ProductFieldID; ?>">
                                                        </td>                                  
                                                        
                                                        <td>
                                                            <?php echo $obj->ProductFieldName; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $obj->ProductFieldType; ?>
                                                        </td>
                                                        <td>
                                                            <?php if($obj->IsRequired) echo "Yes"; else echo "No"; ?>
                                                        </td>
                                                        <td class="action">
                                                            <a href='../productfield/editproductfield.php?mode=edit&Id=<?php echo $obj->ProductFieldID; ?>'>
                                                                <i class="fa fa-edit">&nbsp;</i>
                                                            </a>
                                                            <a  href="javascript:fnDelete('<?php echo $obj->ProductFieldID; ?>');" title="Delete">
                                                                <i class="fa fa-remove">&nbsp;</i>
                                                            </a>                                                     
                                                        </td>
                                                    </tr>  
                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                                echo '<tr class="alt"><td colspan="8"><b style="color:red;">No Product Field found.</b></td></tr>';
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