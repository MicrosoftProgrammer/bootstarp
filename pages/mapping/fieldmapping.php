<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if ($_REQUEST['mode']=="update")
    {
        $CategoryID=$_REQUEST["Category"];
        for ($i=0;$i<count($_REQUEST['field']);$i++)
        {
            $ProductFieldID = $_REQUEST['field'][$i];

            $sql="select * from fieldmapping where CategoryID=".$CategoryID." and ProductFieldID=".$ProductFieldID;
            $res=mysql_query($sql);
            $num=mysql_num_rows($res);

            if($num==0){
                $sql = "insert into fieldmapping(CategoryID,ProductFieldID) values('$CategoryID','$ProductFieldID')";
                mysql_query($sql);
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
                        <h1 class="page-header">Product Field Mapping</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Product Field Mapping
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
                                        <div class="form-group col-md-12">
                                            <label>Category Name</label>
                                            <select class="form-control" name="Category" onchange="fnSubmit();" required>
                                                <?php fnDropDown("categories","CategoryName","CategoryID","Category"); ?>
                                            </select>                                               
                                        </div>
                                        
                                        <?php if($_REQUEST['Category']!="") { 
                                        $sql= "select *,fm.deleted as unmapped,pf.ProductFieldID as ProductFieldID from productfields pf left join fieldmapping fm
                                        on fm.ProductFieldID = pf.ProductFieldID where pf.Deleted=0";
                                      
                                        $res=mysql_query($sql);

                                        if(mysql_num_rows($res) == 0){
                                            $sql= "select * from productfields where Deleted=0";
                                              echo $sql;
                                            $res=mysql_query($sql);
                                        }
                                        while($obj =mysql_fetch_object($res)) {                                        
                                        ?>
                                        <div class="form-group col-md-3">
                                            <label class="checkbox-inline">
                                                <input name="field[]" <?php if($obj->unmapped=="0") echo "checked"; ?> value="<?php echo $obj->ProductFieldID; ?>" type="checkbox"><?php echo $obj->ProductFieldName; ?>
                                            </label>
                                        </div>
                                        <?php } ?>
                                        <div class="form-group col-md-12">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                            <button type="reset" class="btn btn-danger">Reset</button>
                                        </div>
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
    <script>
        function fnSubmit(){
            document.adminForm.action="fieldmapping.php";
            document.adminForm.submit();
        }
    </script>
</html>