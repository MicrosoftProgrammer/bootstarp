<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');
$filter = array();
if ($_REQUEST['mode']=="del")
{
	for ($i=0;$i<count($_REQUEST['chkSelect']);$i++)
	{
		mysql_query("update products set Deleted=1 where ProductID=".$_REQUEST['chkSelect'][$i]."");
	}

	header("location:viewproducts.php?mode=deleted");
	die();
}

$cols="";
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo $_SESSION["CompanyName"]; ?></title>
        <?php echo fnCss(); ?>
        <?php echo fnDataTableCSS(); ?>
        <style>

        table{
            overflow-x:scroll !important;
            display :inline-block;
        }


            th.search input{
                width:80% !important;
            }

            #fieldList {
                display: none;
            }

            #button:hover #fieldList {
                display: block;
                background:#347FA9;
                color:white;position:
                absolute;z-index:9;
                padding-left: 10px;
                border-radius: 6px
            }

            span.cell{
                display: block !important;
            }

            .cell{
                white-space: nowrap;
                width:120px !important;
                text-overflow: ellipsis;
                cursor: pointer;
                word-break: break-all;
                overflow:hidden;
                white-space: nowrap;
            }

            span.cell:hover{
                overflow: visible; 
                width:auto !important;  /* just added this line */
            }

        </style>
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
                        <h1 class="page-header">View Product</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            View Product
                            <a href="../product/bulkimport.php" class="pull-right text-white">Bulk Import</a>
                            <a href="../product/addproduct.php" style="margin-right:30px;" class="pull-right text-white">Add Product</a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">

                            <?php if($_REQUEST["mode"]=="added"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Product Added Successfully"; ?></strong>
                                </div>
                            <?php } ?>
                            <?php if($_REQUEST["mode"]=="edited"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Product Updated Successfully"; ?></strong>
                                </div>
                            <?php } ?>         
                            <?php if($_REQUEST["mode"]=="deleted"){ ?>
                                <div class="alert alert-success alert-dismissable">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-remove"></i></button>
                                    Success! <strong><?php echo "Product Deleted Successfully"; ?></strong>
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

                                                 echo '<textarea name="filters" style="display:none;">'.json_encode($filter).'</textarea>';                
                                    } ?>
                                
                                 </div>
                            <?php if($_REQUEST["Category"]!="") { 
                                    $sql = "select * from products p inner join categories c on p.CategoryID =c.CategoryID 
                                        where p.Deleted=0";
                                        if($_REQUEST["Category"]!=""){
                                            $sql= $sql." and p.CategoryID=".$_REQUEST["Category"];
                                        }
                                        $sql.= " order by p.ProductID";
                                        $res=mysql_query($sql);
                                        $numrows=mysql_num_rows($res);
                                        

                            
                            ?>
                                                                    <?php
                                            if($numrows>0)
                                            {
                                                echo ' <div class="form-group col-md-3">
                                                               <a href="javascript:void(0)" onclick="fnReport(1)"><img src="../../images/excel.png"alt="excel" /></a>
                                                               <a href="javascript:void(0)" onclick="fnReport(2)"><img src="../../images/csv.png"alt="csv" /></a>       
                                                               <a href="javascript:void(0)" onclick="fnReport(3)"><img src="../../images/word.png"alt="word" /></a>  
                                                               <a href="javascript:void(0)" onclick="fnReport(4)"><img src="../../images/pdf.png"alt="pdf" /></a>                                          
                                                            </div>';

                                                $objFields=mysql_fetch_object($res);
                                                $count=0;
                                                $data = json_decode($objFields->Fields, TRUE);
                                                echo '<div class="form-group  pull-right" id="button"><a href="javascript:void(0)"  type="button" class="btn btn-primary" >Show/Hide Columns</a>';
                                                echo '<div class="form-group" id="fieldList">
                                                <div class="mask"><i class="fa fa-spinner fa-spin fa-2x fa-fw">&nbsp;</i>Loading...</div>
                                                <div class="checkbox">
                                                                    <label>
                                        <input id="fieldcheck" type="checkbox">Select All
                                    </label>
                                    <hr>
                                    </div>
                                                ';
                                                $count=0;
                                                foreach(array_keys($data) as $key) {
                                                    $checked="";
                                                    if($count>4){
                                                        $cols= $cols.(string)$count.",";
                                                    }
                                                    else{
                                                        $checked="checked";
                                                    }
                                                  
  $count++;
                                                    echo '
                                                    <div class="checkbox">
                                    <label>
                                        <input class="fieldcheck" type="checkbox" '.$checked.' onclick="fnShowHide('.$count.');" value="">'.$key.'
                                    </label>
                                </div>';

                                                  
                                                    
                                                }
                                              echo '</div></div><br clear="all" />' ; 
                                            }
                                             $cols = rtrim($cols,",");
                                        ?>
                                        
                            <table width="100%" class="table table-striped table-bordered table-hover" id="example">
                                <thead>
                                    <tr>
                                        <th>
                                            <input type="checkbox" id="checkAll" />
                                        </th>
                                        <?php
                                            if($numrows>0)
                                            {
                                                $objFields=mysql_fetch_object($res);
                                                $count=0;
                                                $data = json_decode($objFields->Fields, TRUE);
                                                
                                                    foreach(array_keys($data) as $key) {
                                                        echo "<th class='cell'>".$key."</th>";
                                                        $count++;
                                                    }
                                                
                                            }
                                        ?>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                        <tr class="searchtr">
                                        <th>
                                        </th>
                                        <?php
                                            if($numrows>0)
                                            {
                                                $objFields=mysql_fetch_object($res);
                                                $count=0;
                                                $data = json_decode($objFields->Fields, TRUE);
                                                
                                                    foreach(array_keys($data) as $key) {
                                                        echo "<th class='search cell'>".$key."</th>";
                                                        $count++;
                                                    }
                                                
                                            }
                                        ?>
                                        <th>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                              
                                        	if($numrows>0)
                                            {
                                                $cnt=0;
                                                $res=mysql_query($sql);
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
                                                    $data = json_decode($obj->Fields, TRUE);
                                                    
                                        
                                                    if($showdata && count($data)>0) {
                                                        
                                                    ?>
                                                       
                                                    <tr <?php echo $class; ?>>
                                                        <td>
                                                     
                                                            <input type="checkbox" name="chkSelect[]"  class="check" value="<?php echo $obj->ProductID; ?>">
                                                        </td>                                                                                      
                                                        
                                                            <?php 
                                                            $count=0;
                                                            
                                                           
                                                                foreach(array_values($data) as $value) {
                                                                    echo "<td><span class='cell'>".$value."</span></td>";
                                                                }
                                                           
                                                           ?>
                                                        
                                                        <td class="action">
                                                            <a href='../product/editproduct.php?mode=edit&Id=<?php echo $obj->ProductID; ?>'>
                                                                <i class="fa fa-edit">&nbsp;</i>
                                                            </a>
                                                            <a  href="javascript:fnDelete('<?php echo $obj->ProductID; ?>');" title="Delete">
                                                                <i class="fa fa-remove">&nbsp;</i>
                                                            </a>    
                                                            <a  href="../product/viewproduct.php?ProductID=<?php echo $obj->ProductID; ?>" title="View">
                                                                <i class="fa fa-search">&nbsp;</i>
                                                            </a>                                                 
                                                        </td>
                                                    </tr>  
                                                    <?php
                                                }
                                                }
                                            }
                                            else
                                            {
                                                echo '<tr class="alt"><td colspan="8"><b style="color:red;">No Product found.</b></td></tr>';
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
                            <?php }
                            } ?>  
                             
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
    <script src="colResizable-1.5.min.js"></script>
    <script>
    
        $('#example thead th.search').each(function() {
            var title = $('#example thead th').eq($(this).index()).text();
            $(this).html('<input type="text" class="form-control" placeholder="Search" />');
        });
        
        // DataTable
        var example = 
            $('#example').DataTable({
                "autoWidth": false,
                //"responsive" :true,
                "columnDefs": [ {
                    "targets": 0,
                    "orderable": false
                },
            {
                "targets": [ <?php echo $cols; ?> ],
                "visible": false
            }
             ],
                order: [ 1, 'desc' ]            
            });

        example.columns().eq(0).each(function(colIdx) {
            $('input', example.column(colIdx).header()).on('keyup change', function() {
                example
                    .column(colIdx)
                    .search(this.value)
                    .draw();
            });
        
            $('input', example.column(colIdx).header()).on('click', function(e) {
                e.stopPropagation();
            });
        });

        function fnShowHide( iCol )
        {
            /* Get the DataTables object again - this is not a recreation, just a get of the object */
            var oTable = $('#example').dataTable();
            
            var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
            oTable.fnSetColumnVis( iCol, bVis ? false : true );
        }

        function fnSubmit(){
            document.adminForm.target="_self";
            document.adminForm.action="viewproducts.php";
            document.adminForm.submit();
        }

        $("#fieldcheck").click(function(){
            $('.mask').addClass('ajax');
            $('.fieldcheck').not(this).prop('checked', this.checked);
            setTimeout(function(){
                var oTable = $('#example').dataTable();
                for(i=0;i<oTable.fnSettings().aoColumns.length;i++){
                    oTable.fnSetColumnVis( i, document.getElementById('fieldcheck').checked );
                }  
                $('.mask').removeClass('ajax');
            },1000);          
        });

        function fnReport(arg){
            document.adminForm.target="_blank";
            if(arg==1){
                document.adminForm.action="../reports/previewreport.php?mode=Product&type=excel";
                document.adminForm.submit();
            }
            if(arg==2){
                document.adminForm.action="../reports/previewreport.php?mode=Product&type=csv";
                document.adminForm.submit();  
            }            
            if(arg==3){
                document.adminForm.action="../reports/previewreport.php?mode=Product&type=word";
                document.adminForm.submit();  
            }
            if(arg==4){
                document.adminForm.action="../reports/previewreport.php?mode=Product&type=pdf";
                document.adminForm.submit();  
            }
        }        

    </script>
</html>