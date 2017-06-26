<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');
$filter = array();
 $TransactionID="";

 if($_REQUEST["TransactionID"]!=""){
     $TransactionID = $_REQUEST["TransactionID"];
 }

$text="";
if ($_REQUEST['mode']=="Add" && $TransactionID=="")
{
    $InvoiceNo	 = str_replace("'","`",$_REQUEST["InvoiceNo"]);
    $exist = GetData("producttransactions","InvoiceNo",$InvoiceNo,"TransactionID");
    echo $exist;

    if($exist==""){
        $ProductID = 0;
        $Owner = str_replace("'","`",$_REQUEST["Owner"]);
        $PurchaseDate = ConvertToStdDate(str_replace("/","-",$_REQUEST["PurchaseDate"]));
        $DueDate = ConvertToStdDate(str_replace("/","-",$_REQUEST["DueDate"]));
        $InvoiceNo	 = str_replace("'","`",$_REQUEST["InvoiceNo"]);
        $JobRef	 = str_replace("'","`",$_REQUEST["JobRef"]);
        $LPORef	 = str_replace("'","`",$_REQUEST["LPORef"]);
        $QuotaRef	 = str_replace("'","`",$_REQUEST["QuotaRef"]);
        $ChargeDetails	 = "Rented ".GetProductData($ProductID)." From ".ConvertToCustomDate($PurchaseDate)." to ".ConvertToCustomDate($DueDate)." , ".str_replace("'","`",$_REQUEST["ChargeDetails"]);
        $PurchaseValue	 = $_REQUEST["PurchaseValue"];

        if($InvoiceNo!=""){
            $sql="insert into producttransactions(Owner,ProductID,PurchaseDate,DueDate,InvoiceNo,JobRef,
                    LPORef,QuotaRef,ChargeDetails,PurchaseValue) values(
                        '$Owner','$ProductID','$PurchaseDate','$DueDate','$InvoiceNo','$JobRef',
                        '$LPORef','$QuotaRef','$ChargeDetails','$PurchaseValue'
                    )";

            mysql_query($sql);
            $TransactionID= mysql_insert_id();

            header("location:geninvoicecreator.php?mode=added&TransactionID=".$TransactionID);
        }
    }
    else{
         $text ="Invoice No Already Exists"; 
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>
        <?php echo fnDatePickerCSS(); ?>
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
                        <h1 class="page-header">Create Invoice</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Create Invoice
                             <a href="../reports/invoicegen.php" class="pull-right text-white">View Invoice</a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                            <?php if($_REQUEST["mode"]=="added"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Invoice Generated Successfully"; ?></strong>
                                </div>
                            <?php } ?>
                            <?php if($TransactionID!=""){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <a href="invoicegenerator.php?TransactionID=<?php echo $TransactionID; ?>" target="_blank"><i class="fa fa-print"></i>
                                    Success! <strong><?php echo "Click to Print Invoice"; ?></strong></a>
                                </div>
                            <?php } ?>         
                            <?php if($text!=""){ ?>
                                <div class="alert alert-danger alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Error! <strong><?php echo $text; ?></strong>
                                </div>
                            <?php } ?>                                                     
                            
                            <form name="adminForm" action="geninvoicecreator.php?mode=Add" method="post"> 
                                <div class="col-md-12">
                                   
                                                   
                                    <div class="form-group col-md-4">
                                        <label>Owner</label>
                                        <input type="text" class="form-control" name="Owner" required value="<?php echo $_REQUEST['Owner']; ?>" />                                            
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Purchase Date</label>
                                        <div class="input-group date">
                                            <input type="text" 
                                                    class="form-control " 
                                                    name="PurchaseDate"
                                                    <?php if($_REQUEST["PurchaseDate"]!="") echo "value='".$_REQUEST["PurchaseDate"]."'"; ?> 
                                                    required/>  
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>   
                                        </div>                                       
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Due Date</label>
                                        <div class="input-group date">
                                            <input type="text" 
                                                    class="form-control " 
                                                    <?php if($_REQUEST["DueDate"]!="") echo "value='".$_REQUEST["DueDate"]."'"; ?>
                                                    name="DueDate" 
                                                    required/>  
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>   
                                        </div>                                       
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Invoice No</label>
                                        <input type="text" class="form-control" name="InvoiceNo" required value="<?php echo $_REQUEST['InvoiceNo']; ?>" />                                            
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Job Ref</label>
                                        <input type="text" class="form-control" name="JobRef" required value="<?php echo $_REQUEST['JobRef']; ?>" />                                            
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>LPO Ref</label>
                                        <input type="text" class="form-control" name="LPORef" required value="<?php echo $_REQUEST['LPORef']; ?>" />                                            
                                    </div>  
                                    <div class="form-group col-md-4">
                                        <label>Quota Ref</label>
                                        <input type="text" class="form-control" name="QuotaRef" required value="<?php echo $_REQUEST['QuotaRef']; ?>" />                                            
                                    </div>   
                                    <div class="form-group col-md-4">
                                        <label>Purchase Value</label>
                                        <input type="text" class="form-control" name="PurchaseValue" required value="<?php echo $_REQUEST['PurchaseValue']; ?>" />                                            
                                    </div>                                       
                                    <div class="form-group col-md-4">
                                        <label>Charge Details</label>
                                        <textarea type="text" class="form-control" rows="5" name="ChargeDetails" required><?php echo $_REQUEST["ChargeDetails"]; ?></textarea>                                            
                                    </div>  
   

                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
                                        </div>  
                                </div>
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

            <script>
        function fnSubmit(){
            document.adminForm.action="geninvoicecreator.php";
            document.adminForm.submit();
        }
    </script>
             <?php echo fnDatePickerScript(); ?>
     <script type="text/javascript">
            $(function () {
                $('.date').datetimepicker({
                    format: 'DD/MMM/YYYY'
                });
            });
        </script>
</html>