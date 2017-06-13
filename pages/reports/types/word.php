<?php
    include('../../../includes/connection.php');
    include('../../../includes/helpers.php');
    include('../../../includes/templates.php');

    $CategoryID=$_REQUEST["Category"];
    $CategoryName = GetData("categories","CategoryID",$CategoryID,"CategoryName");
    $filename = slugify($CategoryName);

    header("Content-type: application/vnd.ms-word");
    header("Content-Disposition: attachment;Filename=".$filename.".doc");
?>

 <table border="1" cellpadding="2" cellspacing="2" style="border-collapse: collapse;border-color:blue"> 
    <thead>
        <tr>
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
                $objFields=mysql_fetch_object($res);
                $count=0;
                $data = json_decode($objFields->Fields, TRUE);
                
                foreach(array_keys($data) as $key) {
                    echo "<th>".$key."</th>";
                    $count++;
                }                    
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
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

                    $data = json_decode($obj->Fields, TRUE);
                    if($showdata && count($data)>=6) {                                
                    ?>
                        <tr>
                            <?php 
                                $count=0;                            
                                foreach(array_values($data) as $value) {
                                    echo "<td style='padding: 8px;
                                        line-height: 1.42857143;
                                        vertical-align: top;font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif;
                                        font-size: 14px;
                                        color: #333;'>".$value."</td>";
                                }                            
                            ?>                    
                        </tr>  
                        <?php
                    }
                }
            }                             
        ?>
    </tbody>
</table>