<?php
    include('../../includes/connection.php');
    include('../../includes/helpers.php');

    if($_REQUEST["AdmissionYear"]==""){
        $_REQUEST["AdmissionYear"]=date("Y")-4;
    }

    $AdmissionYear = $_REQUEST["AdmissionYear"];
    $LateralYear = $AdmissionYear + 1;
    $Correspondent = $AdmissionYear - 1;
    $Department = $_REQUEST["Department"];
    $Religion = $_REQUEST["Religion"];
    $Community = $_REQUEST["Community"];
    $FirstGraduate = $_REQUEST["FirstGraduate"];
    $Hostel = $_REQUEST["Hostel"];
    $Gender = $_REQUEST["Gender"];
    $AdmissionMode = $_REQUEST["admissionmode"];
    $Reason = $_REQUEST["Reason"];
    $Debar = $_REQUEST["Debar"];
    $Readmit = $_REQUEST["Readmit"];
    $State = $_REQUEST["State"];
    $Quota = $_REQUEST["Quota"];
    $TCA = $_REQUEST["TCA"];
    $TC = $_REQUEST["TC"];


switch($_REQUEST["mode"])
{
    case "category":
        $sql ="select c.CategoryName as Name,count(p.ProductID) as val from products p inner join categories c on c.CategoryID=p.CategoryID
                        where p.Deleted=0 group by c.CategoryName";
        $result = mysql_query($sql);
    break;
}

$array = array();

    while ( $row = mysql_fetch_assoc( $result ) ) 
    {
        array_push(
            $array,
            array(
                'label' => $row['Name'],
                'value' => $row['val']
            )
        );
    }


echo json_encode($array);

?>
