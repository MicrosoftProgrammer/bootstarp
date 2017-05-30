<?php
        
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

    if ($_REQUEST["mode"]=="Add")
    {         
        require_once '../../includes/excel/excel_reader2.php';
        require_once '../../includes/excel/SpreadsheetReader.php';
        $file =post_img($_FILES['file']['name'], $_FILES['file']['tmp_name'],"../../uploads");
        $Reader = new SpreadsheetReader("../../uploads/".$file);
        $Sheets = $Reader -> Sheets();
        $CategoryID= $_REQUEST["Category"];
        $i=0;
        $header = array();
        foreach ($Sheets as $Index => $Name)
        {
            $Reader -> ChangeSheet($Index);

            foreach ($Reader as $Row)
            {
               if($i==0){
                    $header =$Row;
               }
               else{
                   $jsonArray = array();
                    foreach (array_combine( $header, $Row ) as $name => $value) {                        
                        $jsonArray[$name] = $value;
                    }

                    $json = json_encode($jsonArray);
                    $sql ="insert into products(CategoryID,Fields) values('$CategoryID','$json')";
                    mysql_query($sql);
               }
              $i++;
            }
        }

        $text = "Products imported Successfully";
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
                        <h1 class="page-header">Import Excel Data</h1>
                        
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Bulk Product Import
                              
                        </div>
                        <div class="panel-body">
                            <?php if($error!=""){ ?>
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                Error! <strong><?php echo $error; ?></strong>
                            </div>
                            <?php } ?>
                            <?php if($text!=""){ ?>
                            <div class="alert alert-success alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                Success! <strong><?php echo $text; ?></strong>
                            </div>
                            <?php } ?>
                            <div class="row">
                                <div class="col-md-12">
                                   <form name="adminForm" method="post" action="bulkimport.php?mode=Add" enctype="multipart/form-data">   
                                        <div class="form-group col-md-6">
                                            <label>Category Name</label>
                                            <select class="form-control" name="Category" onchange="fnSubmit();" required>
                                                <?php fnDropDown("categories","CategoryName","CategoryID","Category"); ?>
                                            </select>                                               
                                        </div>
                                        <div class="form-group col-md-6">
                                            <?php if($_REQUEST["Category"]!="") { ?>    
<a href="../product/exportTemplate.php?CategoryID=<?php echo $_REQUEST['Category']; ?>">Get Template</a>
                                            <?php } ?>                             
                                        </div>

                                        <div class="form-group col-md-12">
                                            <label>Import Excel File</label>
                                            <input type="file" class="form-control" name="file" required />                                            
                                        </div>
                                        
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
            document.adminForm.action="bulkimport.php";
            document.adminForm.submit();
        }
    </script>
</html>