<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if ($_REQUEST["mode"]=="Add")
    { 
        $keys = implode (", ", array_map('add_quotes', array_keys($_POST)));
        $sql = "SELECT ProductFieldName FROM productfields where `ProductFieldKey`in($keys)
         ORDER BY FIELD(ProductFieldKey,$keys)";

        $res=mysql_query($sql);
        $keys = "";
        while($row = mysql_fetch_object($res))
        {
            $keys = $keys.$row->ProductFieldName.",";
        }

        $data = $_POST;
        unset($data["Category"]);

       $keys = substr($keys, 0, -1);     
       $keys = explode (",", $keys);      
       $json = array_combine($keys, array_values(array_map('trim',$data)));
       $json=json_encode($json);       

        $CategoryID = $_REQUEST["Category"];
        $UserID = $_SESSION["UserID"];
        $Productlog = "Product Created by ".$_SESSION["name"];
        $sql = "INSERT INTO products (CategoryID,Fields,CreatedBy,Productlog)
        VALUES ('$CategoryID','$json','$UserID','$Productlog')";        
        mysql_query($sql);

        $Owner = str_replace("'","`",$_REQUEST["Owner"]);
        $ProductID = mysql_insert_id();
        $PurchaseDate = ConvertToStdDate(str_replace("/","-",$_REQUEST["PurchaseDate"]));
        $DueDate = ConvertToStdDate(str_replace("/","-",$_REQUEST["DueDate"]));
        $InvoiceNo	 = str_replace("'","`",$_REQUEST["InvoiceNo"]);
        $JobRef	 = str_replace("'","`",$_REQUEST["JobRef"]);
        $LPORef	 = str_replace("'","`",$_REQUEST["LPORef"]);
        $QuotaRef	 = str_replace("'","`",$_REQUEST["QuotaRef"]);
        $ChargeDetails	 = str_replace("'","`",$_REQUEST["ChargeDetails"]);
        $PurchaseValue	 = $_REQUEST["PurchaseValue"];

        if($InvoiceNo!=""){
            $sql="insert into producttransactions(Owner,ProductID,PurchaseDate,DueDate,InvoiceNo,JobRef,
                    LPORef,QuotaRef,ChargeDetails,PurchaseValue) values(
                        '$Owner','$ProductID','$PurchaseDate','$DueDate','$InvoiceNo','$JobRef',
                        '$LPORef','$QuotaRef','$ChargeDetails','$PurchaseValue'
                    )";

            mysql_query($sql);     
        }
        
        header("location:viewproducts.php?mode=added");   
   }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>
        <?php echo fnDatePickerCss(); ?>
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
                        <h1 class="page-header">Add Product</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add Product
                                <a href="../product/viewproducts.php" class="pull-right text-white">View Product</a>
                        </div>
                        <div class="panel-body">
                            <?php if($error!=""){ ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                Error! <strong><?php echo $error; ?></strong>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                   <form name="adminForm" method="post" action="addproduct.php?mode=Add" enctype="multipart/form-data">   
                                       <div class="form-group col-md-4">
                                            <label>Category Name</label>
                                            <select class="form-control" name="Category" onchange="fnSubmit();" required>
                                                <?php fnDropDown("categories","CategoryName","CategoryID","Category"); ?>
                                            </select>                                               
                                        </div>
                                        <?php
                                            $CategoryID = $_REQUEST["Category"];
                                            if($CategoryID!==""){
                                                $sql="select *, pft.ProductFieldType as Type from productfields pf 
                                                inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
                                                inner join productfieldtype pft on pf.ProductFieldType = pft.ProductFieldTypeID   
                                                where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
                                                $res = mysql_query($sql);
                                                while($obj=mysql_fetch_object($res)){
                                                     $isRequired="";
                                                      if($obj->IsRequired=="1"){
                                                            $isRequired = "required";
                                                        }
                                                    if($obj->Type=="TextBox" || $obj->Type=="Number" || $obj->Type=="Date" || $obj->Type=="Email"){
                                                       
                                                        $type = "text";
                                                        switch($obj->Type){
                                                            case "Number":
                                                                $type="number";
                                                                break;
                                                            case "Date":
                                                                $type="date";
                                                                break;
                                                            case "Email":
                                                                $type="email";
                                                                break;                                                              
                                                        }
                                                    if($obj->Type!="Date") { 
                                                        echo ' <div class="form-group col-md-4">
                                                                    <label>'.$obj->ProductFieldName.'</label>
                                                                    <input type="'.$type.'" class="form-control" name="'.$obj->ProductFieldKey.'" 
                                                                    value="'.$_REQUEST[$obj->ProductFieldKey].'" 
                                                                    placeholder="'.$obj->ProductFieldName.'"
                                                                    '.$isRequired.'/>                                            
                                                                </div>';
                                                    }
                                                    else{
                                                          echo ' <div class="form-group col-md-4">
                                                                    <label>'.$obj->ProductFieldName.'</label>

                                                                     <div class="input-group date">
                                                                    <input type="text" 
                                                                            class="form-control " 
                                                                            name="'.$obj->ProductFieldKey.'" 
                                                                            placeholder="'.$obj->ProductFieldName.'"
                                                                            '.$isRequired.'/>  
                                                                    <span class="input-group-addon">
                                                                        <span class="fa fa-calendar"></span>
                                                                     </span>   
                                                                     </div>                                       
                                                                </div>';
                                                      }
                                                    }
                                                    else  if($obj->Type=="TextArea"){
                                                        echo ' <div class="form-group col-md-4">
                                                                    <label>'.$obj->ProductFieldName.'</label>
                                                                    <textarea class="form-control" rows="4" name="'.$obj->ProductFieldKey.'" 
                                                                    placeholder="'.$obj->ProductFieldName.'"
                                                                    '.$isRequired.'></textarea>                                        
                                                                </div>';
                                                    } 
                                                    else  if($obj->Type=="file"){
                                                        echo ' <div class="form-group col-md-4">
                                                                    <label>'.$obj->ProductFieldName.'</label>
                                                                    <input type="file" class="form-control" name="'.$obj->ProductFieldKey.'" />                                         
                                                                </div>';
                                                    } 
                                                    else  if($obj->Type=="CheckBox"){
                                                        echo ' <div class="form-group col-md-4">
                                                                    <label>'.$obj->ProductFieldName.'</label>
                                                                    <input type="checkbox" class="form-control" name="'.$obj->ProductFieldKey.'" />                                         
                                                                </div>';
                                                    } 
                                                    else  if($obj->Type=="Select"){
                                                        echo ' <div class="form-group col-md-4">
                                                                    <label>'.$obj->ProductFieldName.'</label>
                                                                    <select '.$isRequired.' class="form-control" name="'.$obj->ProductFieldKey.'">
                                                                        '.fnFieldDropDown($obj->FieldMappingID,$obj->ProductFieldKey).'
                                                                    </select>                                         
                                                                </div>';
                                                    } 

                                                }
                                            }
                                        ?>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
                                        </div>
                                    </form>
                                </div>
                                <!-- /.col-lg-6 (nested) -->
                            </div>
                            <!-- /.row (nested) -->
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
            document.adminForm.action="addproduct.php";
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