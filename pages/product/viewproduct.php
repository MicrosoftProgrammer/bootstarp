<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');
    include('../../includes/templates.php');

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
                        <h1 class="page-header">Product Detail</h1>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
               <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            View Product Detail
                             
                            <a href="../product/viewproducts.php" class="pull-right text-white">View Products</a>
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">                                                                          
                            <form name="adminForm" method="post"> 
        
                                <?php
                                        $sql = "select * from products p inner join categories c on p.CategoryID =c.CategoryID 
                                        where p.Deleted=0 and p.ProductID=".$_REQUEST["ProductID"];
                                        $sql.= " order by p.ProductID";
                                        $res=mysql_query($sql);
                                        $numrows=mysql_num_rows($res);
                                        	if($numrows>0)
                                            {
                                                $cnt=0;
                                                $obj=mysql_fetch_object($res);
                                                    $cnt++;
                                                    if($cnt%2==0) $class=""; else $class="class=alt";
                                                    ?>
                                                   
                                                            <?php 
                                                            $count=0;
                                                            echo "<table class='table table-hover table-bordered'>";
                                                            $data = json_decode($obj->Fields, TRUE);
                                                           foreach($data as $key => $value) {    
                                                               $sql1="select * from productfields where ProductFieldName='".$key."' and ProductFieldType=10 and deleted=0";
                                                            
                                                                $res1=mysql_query($sql1);
                                                                $numrows1=mysql_num_rows($res1);
                                                               if($numrows1 > 0){
                                                                   $value="<a href='../../images/products/".$value."'><img src='../../images/products/".$value."' width='100' alt='".$value."' /></a>";
                                                               }                                                           
                                                               if($count%2==0)
                                                                echo '<tr>';
                                                            echo "<th>".$key."</th>";
                                                            echo "<td>".$value."</td>";
                                                            $count++;
                                                             if($count%2==0)
                                                                echo '</tr>';
                                                        
                                                           } 
                                                           echo "</table>";
                                                           ?>
                                                     
                                                    
                                                    <?php
                                            }
                                                                     
                                ?>
                                <div class="panel panel-success">
                                    <div class="panel-heading">
                                        <i class="fa fa-bell fa-fw"></i> Product History
                                    </div>
                                    <!-- /.panel-heading -->
                                    <div class="panel-body">
                                        <div class="list-group">
                                        <ul>
                                          <?php echo $obj->Productlog; ?>
                                        </ul>                           
                                        </div>
                                        <!-- /.list-group -->
                                    </div>
                                    <!-- /.panel-body -->
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
</html>