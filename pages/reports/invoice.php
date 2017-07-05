<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

if ($_REQUEST['mode']=="del")
{
	for ($i=0;$i<count($_REQUEST['chkSelect']);$i++)
	{
		mysql_query("update producttransactions set Deleted=1 where TransactionID=".$_REQUEST['chkSelect'][$i]."");
	}

	header("location:invoice.php?mode=deleted");
	die();
}

if ($_REQUEST['mode']=="ret")
{
	for ($i=0;$i<count($_REQUEST['chkSelect']);$i++)
	{
		mysql_query("update producttransactions set Status=1 where TransactionID=".$_REQUEST['chkSelect'][$i]."");
        $sql="select * from products p inner join producttransactions pt on p.ProductID = pt.ProductID where pt.TransactionID=".$_REQUEST['chkSelect'][$i];
        $res = mysql_query($sql);
        $obj=mysql_fetch_object($res);
        $data = json_decode($obj->Fields,TRUE);
        $data["Status"] = "Returned";
        $Productlog = "<li>Product Returned to ".$_SESSION["Name"]." on ".date("Y-m-d H:i:s")."</li>";

        $sql= "update products set Fields='".json_encode($data)."' 
        ,Productlog	=CONCAT(Productlog,'".$Productlog."')
        where ProductID=".$obj->ProductID;
        mysql_query($sql);  

        $UserAction = "<li>".$_SESSION["Name"]." updated invoice with TransactionID ".$_REQUEST['chkSelect'][$i]."  at ".date("d-m-Y H:i:s")."</li>";
        $sql="update userlog set UserAction=CONCAT(UserAction,'".$UserAction."') where LogID=".$_SESSION["SessionId"];
        mysql_query($sql);	  
	}

	header("location:invoice.php?mode=updated");
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
                        <h1 class="page-header">Invoice</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            View Invoice
                            <a href="../reports/invoicecreator.php" class="pull-right text-white">Add Invoice</a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                            <?php if($_REQUEST["mode"]=="added"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Invoice Added Successfully"; ?></strong>
                                </div>
                            <?php } ?>
                            <?php if($_REQUEST["mode"]=="edited"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Invoice Updated Successfully"; ?></strong>
                                </div>
                            <?php } ?>         
                            <?php if($_REQUEST["mode"]=="deleted"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Invoice Deleted Successfully"; ?></strong>
                                </div>
                            <?php } ?>                                                     
                            
                            <form name="adminForm" method="post"> 
                            <table width="100%" class="table table-striped table-bordered table-hover" id="dataTable-example">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="checkAll" />
                                        </th>
                                        <th>
                                            Invoice No
                                        </th>
                                        <th>
                                            Product
                                        </th>                                          
                                        <th>
                                            Purchase Date
                                        </th>
                                        <th>
                                            Purchase Value
                                        </th>   
                                        <th>
                                            Status
                                        </th>                                                                                                                                                             
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                        $sql = "select *,pt.Status as ProductStatus from producttransactions pt inner join products p
                                        on p.ProductID = pt.ProductID inner join categories c on
                                        c.CategoryID = p.CategoryID
                                         where pt.Deleted=0";
                                        $sql.= " order by TransactionID desc";
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
                                                            <input type="checkbox" name="chkSelect[]"  class="check"value="<?php echo $obj->TransactionID; ?>">
                                                        </td>                                  
                                                        
                                                        <td>
                                                            <?php echo $obj->InvoiceNo; ?>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            $data = json_decode($obj->Fields,true);
                                                             echo $data[$obj->ProductPrimaryName]; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $obj->PurchaseDate; ?>
                                                        </td>
                                                        <td>
                                                            <?php echo $_SESSION["CurrencyType"]." ".ConvertToRupees($obj->PurchaseValue); ?>
                                                        </td>
                                                        <td>
                                                            <?php if($obj->ProductStatus=="1") echo "Returned"; else echo "Rented"; ?>
                                                        </td>                                                                                                                                                                                                                                
                                                        <td class="action">
                                                            <a  href="javascript:fnDelete('<?php echo $obj->TransactionID; ?>');" title="Delete">
                                                                <i class="fa fa-remove">&nbsp;</i>
                                                            </a>     
                                                            <a target="_blank" href='../reports/invoicegenerator.php?TransactionID=<?php echo $obj->TransactionID; ?>'>
                                                                <i class="fa fa-print">&nbsp;</i>
                                                            </a>  
                                                            <?php if($obj->ProductStatus=="0") { ?>     
                                                            <a href="javascript:fnStatus('<?php echo $obj->TransactionID; ?>');" title="Status">
                                                                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                                                            </a>      
                                                            <?php } ?>                                                                                                                                                                  
                                                        </td>
                                                    </tr>  
                                                    <?php
                                                }
                                            }
                                            else
                                            {
                                                echo '<tr class="alt"><td colspan="8"><b style="color:red;">No Invoice found.</b></td></tr>';
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
                    $(document).ready(function() {
        $("#dataTable-example").DataTable({
            responsive: true,
            "aaSorting": [],
            columnDefs: [ { orderable: false, targets: [0,1] } ]
        });
    });
        function fnStatus(arg)
        {
	        if(confirm("Are you sure want to return?"))
	        {
		        document.location.href="invoice.php?mode=ret&&chkSelect[]=" + arg + "";
	        }
        }
    </script>
</html>