<?php        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');


    if ($_REQUEST["mode"]=="update")
    {       
        
        // print_r($_FILES);
        $files = array();
        $fileImage = array();
        $invalid = array();
        if(count($_FILES)>0){
            foreach(array_keys($_FILES) as $file){
                $name = GetData("productfields","ProductFieldKey","'".$file."'","ProductFieldName");
                $files[]=$name;
            }
            $cnt=0;
            foreach ($_FILES as $file){
                if($file['name']==""){
                    $invalid[] = $files[$cnt];
                }
                else {
                    $fileImage[$files[$cnt]] =post_img($file['name'], $file['tmp_name'],"../../images/products");
                }
                $cnt++;
            }
        }
        if(count($invalid)>0) {
           
             $fields = GetData("products","ProductID",$_REQUEST['Id'],"Fields");
             $fields = json_decode($fields,TRUE);
             $sql ="select * from products p inner join fieldmapping fm on p.CategoryID=fm.CategoryID
             inner join productfields pf on pf.ProductFieldID=fm.ProductFieldID where pf.ProductFieldType=10
             and p.ProductID=".$_REQUEST['Id'];

            $res=mysql_query($sql);
            while($row = mysql_fetch_object($res))
            {
                foreach ($invalid as $file){
                    if($file== $row->ProductFieldName){
                        $fileImage[$row->ProductFieldName] = $fields[$row->ProductFieldName];
                    }
                }
            }
        }

        $keys = implode (", ", array_map('add_quotes', array_keys($_POST)));
        $sql = "SELECT ProductFieldName FROM productfields where `ProductFieldKey`in($keys)
         ORDER BY FIELD(ProductFieldKey,$keys)";

        $res=mysql_query($sql);
        $keys = "";
        while($row = mysql_fetch_object($res))
        {
            $keys = $keys.$row->ProductFieldName.",";
        }

       $keys = substr($keys, 0, -1);     
       $keys = explode (",", $keys);      
       $json = array_combine($keys, array_values($_POST));
       $Productlog = "<li>Product Updated by ".$_SESSION["Name"]." on ".date("Y-m-d H:i:s")."</li>";

        foreach ($fileImage as $key => $value){
            $json[$key]=$value;
        }

     
       $json=json_encode($json);
       $UserID = $_SESSION["UserID"];

            $sql = "UPDATE products SET ";
            $sql.= "Fields	=	'".$json."',";
            $sql.= "Productlog	=CONCAT(Productlog,'".$Productlog."'),";
            $sql.= "LastUpdatedBy	='".$UserID."',";
            $sql.= "LastUpdatedOn	=CURRENT_TIMESTAMP";
            $sql.= " where ProductID=".$_REQUEST['Id']."";

 
        
        mysql_query($sql);		

        $UserAction = "<li>".$_SESSION["Name"]." updated Product with ID ".$_REQUEST['Id']."  at ".date("d-m-Y H:i:s")."</li>";
        $sql="update userlog set UserAction=CONCAT(UserAction,'".$UserAction."') where LogID=".$_SESSION["SessionId"];
        mysql_query($sql);		
        header("location:viewproducts.php?mode=edited");           
    }

        $sql="SELECT * FROM products where ProductID='".$_REQUEST['Id']."'";
    $res=mysql_query($sql);
    echo mysql_error();
    $numrows=mysql_num_rows($res); 
    if ($numrows>0) 
    {
        $obj= mysql_fetch_object($res);	
        $productfield = json_decode($obj->Fields, true);
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
                        <h1 class="page-header">Edit Product</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Edit Product
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
                                     <form name="adminForm" method="post" action="editproduct.php?mode=update&Id=<?php echo($_REQUEST['Id']); ?>" enctype="multipart/form-data">   
                                        <?php
                                            $CategoryID = $obj->CategoryID;
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
                                                                    <input type="'.$type.'" 
                                                                            class="form-control" 
                                                                            id="'.$obj->ProductFieldKey.'" 
                                                                            name="'.$obj->ProductFieldKey.'" 
                                                                            placeholder="'.$obj->ProductFieldName.'"
                                                                            value="'.$productfield[$obj->ProductFieldName].'"
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
                                                                            id="'.$obj->ProductFieldKey.'" 
                                                                            placeholder="'.$obj->ProductFieldName.'"
                                                                            value="'.$productfield[$obj->ProductFieldName].'"
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
                                                                    <textarea id="'.$obj->ProductFieldKey.'"  class="form-control" rows="4" name="'.$obj->ProductFieldKey.'" 
                                                                    placeholder="'.$obj->ProductFieldName.'"
                                                                    '.$isRequired.'>'.$productfield[$obj->ProductFieldName].'</textarea>                                        
                                                                </div>';
                                                    } 
                                                    else  if($obj->Type=="CheckBox"){
                                                        echo ' <div class="form-group col-md-4">
                                                                    <label>'.$obj->ProductFieldName.'</label>
                                                                    <input type="checkbox" class="form-control" name="'.$obj->ProductFieldKey.'" />                                         
                                                                </div>';
                                                    } 
                                                    else  if($obj->Type=="hidden"){
                                                        echo '<input type="hidden" class="form-control" name="'.$obj->ProductFieldKey.'" value="'.$productfield[$obj->ProductFieldName].'" />                                         
                                                                ';
                                                    }                                                     
                                                    else  if($obj->Type=="file"){
                                                        echo ' <div class="form-group col-md-4">
                                                                    <label>'.$obj->ProductFieldName.'</label>
                                                                    <input type="file" class="form-control" value="'.$productfield[$obj->ProductFieldName].'" name="'.$obj->ProductFieldKey.'" />  
                                                                    <a target="_blank" href="../../images/products/'.$productfield[$obj->ProductFieldName].'">'.$productfield[$obj->ProductFieldName].'</a>                                       
                                                                </div>';
                                                    } 
                                                    else  if($obj->Type=="Select"){
                                             
                                                        if($_REQUEST[$obj->ProductFieldKey]=="")
                                                        {
                                                            $_REQUEST[$obj->ProductFieldKey]=$productfield[$obj->ProductFieldName];
                                                        }
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
     <?php echo fnDatePickerScript(); ?>
     <script type="text/javascript">
            $(function () {
                $('.date').datetimepicker({
                    format: 'DD/MMM/YYYY'
                });
            });

            $(".date").on("dp.change", function(e) {
                if($(e.currentTarget).find("input")[0].id=="CalibrationDate"){
                    $("#CalibrationDueDate").val(e.date.add('years', 1).format("DD/MMM/YYYY"));
                }
            });
        </script>
</html>