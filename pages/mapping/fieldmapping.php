<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if(!isSuperAdmin())
    {
        header("location:../../login.php");
    }

    if ($_REQUEST['mode']=="update")
    {
        $CategoryID=$_REQUEST["Category"];
        $ProductFieldName=$_REQUEST["ProductFieldName"];
        $ProductFieldKey=$_REQUEST["ProductFieldKey"];
        $sql="update categories set ProductPrimaryName='$ProductFieldName',
        ProductPrimaryKey='$ProductFieldKey' where CategoryID=".$CategoryID;        
        mysql_query($sql);

        for ($i=0;$i<count($_REQUEST['field']);$i++)
        {
            $ProductFieldID = $_REQUEST['field'][$i];

            $sql="select * from fieldmapping where CategoryID=".$CategoryID." and ProductFieldID=".$ProductFieldID;
            $res=mysql_query($sql);
            $num=mysql_num_rows($res);

            if($num==0){
                $sql = "insert into fieldmapping(CategoryID,ProductFieldID) values('$CategoryID','$ProductFieldID')";
                mysql_query($sql);

                $sql = "select * from products where CategoryID=".$CategoryID;
                $res=mysql_query($sql);
                $num=mysql_num_rows($res);

                if($num>0){
                    while($obj1=mysql_fetch_object($res)){
                        $data = json_decode($obj1->Fields, TRUE);
                        $productField = GetData("productfields","ProductFieldID",$ProductFieldID,"ProductFieldName");
                        $data[$productField] = "";
                        mysql_query("update products set Fields='".json_encode($data)."' where ProductID=".$obj1->ProductID);

                    }
                }
            }
            else {
                $obj = mysql_fetch_object($res);
                $sql = "update fieldmapping set Deleted = 0 where CategoryID=".$CategoryID." and ProductFieldID=".$ProductFieldID;
                mysql_query($sql);                 
            }
        }

        $list = implode (", ", $_REQUEST['field']);
        $sql ="update fieldmapping set Deleted=1 where CategoryID=".$CategoryID." and ProductFieldID not in(".$list.")";
        mysql_query($sql);

                $sql = "select * from products where CategoryID=".$CategoryID;
                $res=mysql_query($sql);
                $num=mysql_num_rows($res);

                if($num>0){
                    while($obj1=mysql_fetch_object($res)){
                        $data = json_decode($obj1->Fields, TRUE);
                        $sql2 = "select * from productfields pf inner join fieldmapping fm
                            on fm.ProductFieldID = pf.ProductFieldID
                          where fm.CategoryID=".$CategoryID." and fm.ProductFieldID not in(".$list.")";
                        $res2=mysql_query($sql2);
                        $num2=mysql_num_rows($res2);
                        if($num2>0){
                            while($obj2=mysql_fetch_object($res2)){
                                unset($data[$obj2->ProductFieldName]);
                            }
                        }
                        mysql_query("update products set Fields='".json_encode($data)."' where ProductID=".$obj1->ProductID);

                    }
                } 

            $UserAction = "<li>".$_SESSION["Name"]." updated fiele mapping  at ".date("d-m-Y H:i:s")."</li>";
            $sql="update userlog set UserAction=CONCAT(UserAction,'".$UserAction."') where LogID=".$_SESSION["SessionId"];
            mysql_query($sql);	 

        $text="Field Updated Successfully";
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
    <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>
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
                        <h1 class="page-header">Category Field Mapping</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Category Field Mapping
                        </div>
                        <div class="panel-body">
                            <?php if($text!=""){ ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                Success! <strong><?php echo $text; ?></strong>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                   <form name="adminForm" method="post" action="fieldmapping.php?mode=update" enctype="multipart/form-data">   
                                                                                                          <div class="form-group col-md-3">
                                        <label>Client Name</label>
                                            <select class="form-control" name="Client" onchange="fnSubmit();" required>
                                                <?php fnDropDown("Client","ClientName","ClientID","Client"); ?>
                                            </select>                                               
                                    </div>
                                        <div class="form-group col-md-4">
                                            <label>Category Name</label>
                                            <select class="form-control" id="Category" name="Category" onchange="fnSubmit();" required>
                                                <?php fnDropDown("categories","CategoryName","CategoryID","Category"); ?>
                                            </select>                                               
                                        </div>
                                         <div class="form-group col-md-12">
                                        <?php if($_REQUEST['Category']!="") { 
                                        $sql= "select *,fm.deleted as unmapped,pf.ProductFieldID as ProductFieldID from productfields pf left join fieldmapping fm
                                        on fm.ProductFieldID = pf.ProductFieldID where pf.Deleted=0 order by fm.CategoryID,fm.DisplayOrder";
                                        $res=mysql_query($sql);
                                        echo '<ul>
                                                <li id="draggable" class="ui-state-highlight">Drag and Drop to change order</li>
                                            </ul>';
                                        echo "<ul id='sortable'>";  
                                        $fieldnames = array();   
                                        $fieldkeys = array();                                     
                                        while($obj =mysql_fetch_object($res)) {   
                                            if($obj->unmapped==0 && ($obj->CategoryID==$_REQUEST['Category'] || $obj->MandatoryField=="1")){            
                                                array_push($fieldnames,$obj->ProductFieldName);
                                                array_push($fieldkeys,$obj->ProductFieldKey);                        
                                            }

                                           
                                        ?>                                        
                                        <li <?php if($obj->unmapped=="0" && $obj->CategoryID==$_REQUEST['Category']) echo 'id='.$obj->FieldMappingID.''; ?> class="form-group col-md-3 <?php if(($obj->unmapped!="0" || $obj->CategoryID!=$_REQUEST['Category']) &&  $obj->MandatoryField!="1") echo 'unsortable'; ?>" style="list-style:none">
                                            <label class="checkbox-inline">
                                                <input <?php if($obj->MandatoryField=="1") echo 'class="mandatory"';?> name="field[]" <?php if($obj->unmapped==0 && ($obj->CategoryID==$_REQUEST['Category'] || $obj->MandatoryField=="1")) echo "checked"; ?> value="<?php echo $obj->ProductFieldID; ?>" type="checkbox"><?php echo $obj->ProductFieldName;
                                                 ?>
                                            </label>
                                        </li>
                                        <?php } ?>
                                        </div>
                                        <?php if(count($fieldnames) >0) { 
                                               $sql="select * from categories where CategoryID=".$_REQUEST["Category"];
                                                $res = mysql_query($sql);
                                                $obj = mysql_fetch_object($res); 
                                        ?>
                                          <div class="form-group col-md-4">
                                            <label>Product Primary Name</label>
                                            <select class="form-control" id="ProductFieldName" name="ProductFieldName" required>
                                            <?php 
                                                foreach($fieldnames as $fieldname){
                                                    if($obj->ProductPrimaryName==$fieldname)
                                                        echo '<option selected value="'.$fieldname.'">'.$fieldname.'</option>';
                                                    else
                                                        echo '<option value="'.$fieldname.'">'.$fieldname.'</option>';
                                                }
                                             ?>
                                            </select>                                               
                                        </div>
                                          <div class="form-group col-md-4">
                                            <label>Product Primary Key</label>
                                            <select class="form-control" id="ProductFieldKey" name="ProductFieldKey" required>
                                            <?php 
                                                foreach($fieldkeys as $fieldkey){
                                                    if($obj->ProductPrimaryKey==$fieldkey)
                                                        echo '<option selected value="'.$fieldkey.'">'.$fieldkey.'</option>';
                                                    else
                                                        echo '<option value="'.$fieldkey.'">'.$fieldkey.'</option>';
                                                }
                                             ?>                                            
                                            </select>                                               
                                        </div>
                                        <?php } ?>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
                                        </div>
                                        </ul>
                                        <?php } ?>
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
     <script src="../../vendor/jqueryui/jquery-ui.min.js"></script>
    <script>
        function fnSubmit(){
            document.adminForm.action="fieldmapping.php";
            document.adminForm.submit();
        }


    $( function() {
        $(".mandatory").on("click", function (e) {
            var checkbox = $(this);
            if (!checkbox.is(":checked")) {
                // do the confirmation thing here
                e.preventDefault();
                return true;
            }
        });

		$( "#sortable" ).sortable({
             items: "li:not(.unsortable)",
			revert: true,
            update: function (event, ui) {

                 var data="";
                $("#sortable li").each(function(i) {
                    if (data=='')
                        data = $(this).attr('id');
                    else
                        data += "," + $(this).attr('id');
                });

                // POST to server using $.post or $.ajax
                $.ajax({
                    data: {order : data,category : $("#Category").val()},
                    type: 'POST',
                    url: '../../includes/data.php?mode=sorting'
                });
            }
		});

		$( "#draggable" ).draggable({
			connectToSortable: "#sortable",
			helper: "clone",
			revert: "invalid"
		});
		$( "ul, li" ).disableSelection();
	});

    </script>
</html>