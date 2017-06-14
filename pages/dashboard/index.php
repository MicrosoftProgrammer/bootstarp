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
        <?php echo fnGraphCss(); ?>
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
                        <h1 class="page-header">Dashboard</h1>
                    </div>

                    <!-- /.col-lg-12 -->
                </div>
                <div class="row">
                    <?php 
                        $sql = "select c.CategoryID,c.CategoryName,count(p.ProductID) as cnt from products p inner join categories c on c.CategoryID=p.CategoryID
                        where p.Deleted=0 group by c.CategoryName";

                        $res=mysql_query($sql);
                        $panels = array("primary","green","yellow","red");
                        $fonts = array("tasks","comments","tags","cogs");
                        $count=0;
                        while($obj = mysql_fetch_object($res))
                        {
                            $index =  $count%count($panels);
                            echo ' <div class="col-lg-3 col-md-6">
                                    <div class="panel panel-'.$panels[$index].'">
                                        <div class="panel-heading">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    <i class="fa fa-'.$fonts[$index].' fa-5x"></i>
                                                </div>
                                                <div class="col-xs-9 text-right">
                                                    <div class="huge">'.$obj->cnt.'</div>
                                                    <div>'.$obj->CategoryName.'</div>
                                                </div>
                                            </div>
                                        </div>
                                        <a href="../product/viewproducts.php?Category='.$obj->CategoryID.'">
                                            <div class="panel-footer">
                                                <span class="pull-left">View Details</span>
                                                <span class="pull-right"><i class="fa fa-arrow-circle-right"></i></span>
                                                <div class="clearfix"></div>
                                            </div>
                                        </a>
                                    </div>
                                </div>';
                                $count++;
                        }
                    
                    ?>               
            </div>
                <div id="graph" class="row">
                    <div class="col-lg-12">
                    <!-- /.panel -->
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <i class="fa fa-bar-chart-o fa-fw"></i> Category
                            </div>
                            <div class="panel-body">
                                <div id="category"></div>
                            </div>
                            <!-- /.panel-body -->
                        </div>
                    </div>
                </div>
                <!-- /.row -->
            </div>
            <!-- /.container-fluid -->
        </div>
    </body>
    <?php echo fnScript(); ?>   
    <?php echo fnGraphScript(); ?>
                      <script>
                          $.ajax({
                            'async': true,
                            'global': false,
                            'url': '../reports/graph.php?mode=category',
                            'dataType': "json",
                            'success': function (data) {
                                var browsersChart = Morris.Donut({
                                    element: 'category',
                                    data   : data
                                });
                                browsersChart.options.data.forEach(function(label, i){
                                    var legendItem = $('<a href="javascript:void(0)" class="list-group-item"></a>').text(label['label']).prepend('<span>&nbsp;</span>');
                                    legendItem.find('span').css('backgroundColor', browsersChart.options.colors[i]);
                                    $('#legend').append(legendItem)
                                });
                            }
                        });
                      </script>
</html>