<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');
$filter = array();
 $TransactionID="";

 if($_REQUEST["Product"]!=""){
     $sql="select * from producttransactions where ProductID=".$_REQUEST["Product"];
     $res=mysql_query($sql);
     $obj=mysql_fetch_object($res);
     if($obj->ProductStatus=="0"){
         $TransactionID=$obj->TransactionID;
     }
 }

if ($_REQUEST['mode']=="Add" && $TransactionID=="")
{
        $Owner = str_replace("'","`",$_REQUEST["OwnerDetails"]);
        $ProductID = $_REQUEST["Product"];
        $RentedDate = ConvertToStdDate(str_replace("/","-",$_REQUEST["RentedDate"]));
        $ReturnedDate = ConvertToStdDate(str_replace("/","-",$_REQUEST["ReturnedDate"]));
        $InvoiceNo	 = str_replace("'","`",$_REQUEST["InvoiceNo"]);
        $JobRef	 = str_replace("'","`",$_REQUEST["JobRef"]);
        $LPORef	 = str_replace("'","`",$_REQUEST["LPORef"]);
        $QuotaRef	 = str_replace("'","`",$_REQUEST["QuotaRef"]);
        $ChargeDetails	 = str_replace("'","`",$_REQUEST["ChargeDetails"]);
        $Amount	 = $_REQUEST["Amount"];
        $IsSold = 0;
        
        if(isset($_REQUEST['IsSold']))
            $IsSold=1;

        $sql="insert into producttransactions(Owner,ProductID,RentedDate,ReturnedDate,InvoiceNo,JobRef,
        LPORef,QuotaRef,ChargeDetails,Amount,IsSold) values(
            '$Owner','$ProductID','$RentedDate','$ReturnedDate','$InvoiceNo','$JobRef',
            '$LPORef','$QuotaRef','$ChargeDetails','$Amount','$IsSold'
        )";

    mysql_query($sql);
    $TransactionID= mysql_insert_id();
	$_REQUEST["mode"]="added";
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
                        <h1 class="page-header">Rent/Sell Product</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Rent/Sell Product
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                            <?php if($_REQUEST["mode"]=="added"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Product Added Successfully"; ?></strong>
                                </div>
                            <?php } ?>
                            <?php if($TransactionID!=""){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <a href="invoicegenerator.php?TransactionID=<?php echo $TransactionID; ?>" target="_blank"><i class="fa fa-print"></i>
                                    Success! <strong><?php echo "Click to Print Invoice"; ?></strong></a>
                                </div>
                            <?php } ?>         
                            <?php if($_REQUEST["mode"]=="deleted"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Product Deleted Successfully"; ?></strong>
                                </div>
                            <?php } ?>                                                     
                            
                            <form name="adminForm" action="invoice.php?mode=Add" method="post"> 
                                <div class="col-md-12">
                                    <div class="form-group col-md-3">
                                        <label>Category Name</label>
                                            <select class="form-control" name="Category" onchange="fnSubmit();" required>
                                                <?php fnDropDown("categories","CategoryName","CategoryID","Category"); ?>
                                            </select>                                               
                                    </div>
                                    <?php if($_REQUEST["Category"]!="") {
                                        
                                        $CategoryID = $_REQUEST["Category"];
                                            $sql="select *, pft.ProductFieldType as Type from productfields pf 
                                                inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                                inner join productfieldtype pft on pf.ProductFieldType = pft.ProductFieldTypeID   
                                                where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                                $res = mysql_query($sql);
                                                while($obj=mysql_fetch_object($res)){
                                                    if($obj->ShowInFilter=="1"){
                                                        array_push($filter,
                                                        array("Key"=>$obj->ProductFieldKey,
                                                        "Name"=>$obj->ProductFieldName));
                                                        echo ' <div class="form-group col-md-3">
                                                                <label>'.$obj->ProductFieldName.'</label>
                                                                    <select class="form-control" name="'.$obj->ProductFieldKey.'" onchange="fnSubmit();">
                                                                        '.fnGetFilter($obj->ProductFieldName,$obj->ProductFieldKey,$CategoryID ).'
                                                                    </select>                                               
                                                            </div>';
                                                    }
                                                }          
                                    } ?>      
                                                                                           
                                    <div class="form-group col-md-3">
                                        <label>Product Name</label>
                                            <select class="form-control" name="Product" onchange="fnSubmit();"  required>  
                                            <option value="">Select</option>                                        
                                    <?php
                                        $sql = "select * from products p inner join categories c on p.CategoryID =c.CategoryID 
                                        where p.Deleted=0";
                                        if($_REQUEST["Category"]!=""){
                                            $sql= $sql." and p.CategoryID=".$_REQUEST["Category"];
                                        }
                                        $sql.= " order by p.ProductID";
                                        $res=mysql_query($sql);
                                        $numrows=mysql_num_rows($res);
                                        	if($numrows>0)
                                            {
                                                $cnt=0;
                                                while($obj=mysql_fetch_object($res))
                                                { 
                                                    $cnt++;
                                                    $showdata =true;
                                                    if(count($filter)>0){
                                                         $data = json_decode($obj->Fields, TRUE);
                                                        
                                                         for($k=0;$k<count($filter);$k++){
                                                            $filterkey = $_REQUEST[$filter[$k]["Key"]];
                                                  
                                                            $filterdata = $filter[$k]["Name"];
                                                            if($filterkey !=""){
                                                                if($filterkey!=$data[$filterdata]){
                                                                    $showdata= false;
                                                                }
                                                            }
                                                         }
                                                    }

                                                    if($cnt%2==0) $class=""; else $class="class=alt";
                                                    if($showdata) {
                                                            $count=0;
                                                            $data = json_decode($obj->Fields, TRUE);
                                                           foreach($data as $key => $value) {
                                                               $selected="";
                                                               if($obj->ProductID==$_REQUEST["Product"])
                                                                {
                                                                    $selected="selected";
                                                                }
                                                                echo "<option ".$selected." value='".$obj->ProductID."'>".$key." = ".$value."</option>";                                                          
                                                                 break;                                                            
                                                           } 
                                                }
                                            }
                                        }                                    
                                    ?>
                                        </select>                                               
                                    </div>
                                     <?php if($TransactionID=="") { ?>  
                                    <div class="form-group col-md-4">
                                        <label>Owner</label>
                                        <input type="text" class="form-control" name="OwnerDetails" required value="<?php echo $_REQUEST['OwnerDetails']; ?>" />                                            
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Rented Date</label>
                                        <div class="input-group date">
                                            <input type="text" 
                                                    class="form-control " 
                                                    name="RentedDate" 
                                                    required/>  
                                            <span class="input-group-addon">
                                                <span class="fa fa-calendar"></span>
                                            </span>   
                                        </div>                                       
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label>Return Date</label>
                                        <div class="input-group date">
                                            <input type="text" 
                                                    class="form-control " 
                                                    name="ReturnedDate" 
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
                                        <label>Charge Details</label>
                                        <textarea type="text" class="form-control" rows="5" name="ChargeDetails" required><?php echo $_REQUEST["ChargeDetails"]; ?></textarea>                                            
                                    </div>  
                                    <div class="form-group col-md-4">
                                        <label>Amount</label>
                                        <input type="text" class="form-control" name="Amount" required value="<?php echo $_REQUEST['Amount']; ?>" />                                            
                                    </div>     
                                    <div class="form-group col-md-4">
                                        <label>Is Sold</label>
                                        <input type="checkbox" class="form-control" name="IsSold" required value="<?php echo $_REQUEST['IsSold']; ?>" />                                            
                                    </div>  
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
                                        </div>  
                                    <?php } ?>                                                                                                                                                                                                                                                                                                                                                             
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
            document.adminForm.action="invoice.php";
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