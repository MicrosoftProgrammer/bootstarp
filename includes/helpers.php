<?php
function fnMetaHeaders(){

    $html ='
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="TC Management Software">
    <meta name="author" content="Rapid Thoughts">';

    return $html;
}

function fnDataTableCSS(){
    $html ='<link href="../../vendor/datatables-plugins/dataTables.bootstrap.css" rel="stylesheet">
    <link href="../../vendor/datatables-responsive/dataTables.responsive.css" rel="stylesheet">';
    return $html;
}

function fnCss(){
    if(!LoggedInUser())
    {
        header("location:../../login.php");
    }
    $html ='
    <!-- Bootstrap Core CSS -->
    <link href="../../vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="../../vendor/metisMenu/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="../../dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="../../vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn`t work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->';

    return $html;
}

function fnScript(){
    $html =' 
    <script src="../../vendor/jquery/jquery.min.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="../../vendor/bootstrap/js/bootstrap.min.js"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="../../vendor/metisMenu/metisMenu.min.js"></script>

    <!-- Custom Theme JavaScript -->
    <script src="../../dist/js/sb-admin-2.js"></script>
    <script>
        $("#checkAll").click(function(){
            $(".check").not(this).prop("checked", this.checked);
        });
                    $(document).ready(function(){
                $(`[data-toggle="popover"]`).popover({"html":true}); 
            });
        
        function fnDelete(arg)
        {

	        if(confirm("Are you sure want to delete?"))
	        {
		        document.location.href="'.basename($_SERVER['PHP_SELF']).'?mode=del&&chkSelect[]=" + arg + "";
	        }
        }

        function fnBulk(arg) {
            if($(".check:checked").length >0){
                document.adminForm.target="_self";
                if(arg=="del") {
                    if(confirm("Are you sure want to delete?"))
                    {
                        document.adminForm.action="'.basename($_SERVER['PHP_SELF']).'?mode=del";
                        document.adminForm.submit();
                    }        
                }                 
            } else {
                alert("Please select atleast one item");
            } 
        }
        $("body").append(`<footer>
            <p>Copyright &copy; '.date("Y").' '.$_SESSION["CompanyName"].'. All Rights Reservered. Powered by Use Own Solutions</p>
        </footer>`);
    </script>';    
    return $html;
}

function fnDataTableScript(){
    $html =' 
    <script src="../../vendor/datatables/js/jquery.dataTables.min.js"></script>
    <script src="../../vendor/datatables-plugins/dataTables.bootstrap.min.js"></script>
    <script src="../../vendor/datatables-responsive/dataTables.responsive.js"></script>
        <script>
    $(document).ready(function() {
        $("#dataTables-example").DataTable({
            responsive: true,
             order: [ 1, "asc" ],
            columnDefs: [ { orderable: false, targets: [0] } ]
        });
    });
    </script>
    ';

    return $html;
}

function fnDataTableExportScript(){
    $html =' 
     <link href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css" rel="stylesheet" /> 
        <script src="//cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js" ></script>
        <script src="//cdn.datatables.net/buttons/1.3.1/js/buttons.print.min.js"></script>
         <script src="//cdn.datatables.net/buttons/1.3.1/js/buttons.flash.min.js"></script>
          <script src="//cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
           <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
            <script src="//cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
             <script src="//cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    ';

    return $html;
}


function fnGraphCSS(){
    $html ='<link href="../../vendor/morrisjs/morris.css" rel="stylesheet">';
    return $html;
}

function fnGraphScript(){
    $html ='  <script src="../../vendor/raphael/raphael.min.js"></script>
    <script src="../../vendor/morrisjs/morris.min.js"></script>';
    return $html;
}

function fnDatePickerCSS(){
    $html ='<link href="../../vendor/datepicker/bootstrap-datetimepicker.min.css" rel="stylesheet">';
    return $html;
}

function fnDatePickerScript(){
    $html ='  <script src="../../vendor/moment/moment.min.js"></script>
    <script src="../../vendor/datepicker/bootstrap-datetimepicker.min.js"></script>';
    return $html;
}

function fnDropDown($TableName,$ColumnName,$ColumnID,$VariableName)
{
    echo '<option value="" >Select</option>';
    $sql = "select * from ".strtolower($TableName)." where Deleted=0";        
    
    $res=mysql_query($sql);
    $numrows=mysql_num_rows($res);
    if($numrows>0)
    {
        while($obj=mysql_fetch_object($res))
        {
            if($_REQUEST[$VariableName]==$obj->$ColumnID)
            {
                echo '<option value="'.$obj->$ColumnID.'" selected="selected">'.$obj->$ColumnName.'</option>';
            }
            else
            {
                echo '<option value="'.$obj->$ColumnID.'">'.$obj->$ColumnName.'</option>';
            }
        }
    }
}

function fnGetFilter($FieldName,$FieldKey,$CategoryID)
{
    $html= '<option value="" >Select</option>';
    $sql = "select * from products where CategoryID=".$CategoryID;
    $sql.= " and Deleted=0 order by ProductID";     
    
    $res=mysql_query($sql);
    $numrows=mysql_num_rows($res);
    if($numrows>0)
    {
        $filter= array();
        while($obj=mysql_fetch_object($res))
        {
             $data = json_decode($obj->Fields, TRUE);
             if($data[$FieldName]!="" && !in_array($data[$FieldName],$filter)) {
                array_push($filter,$data[$FieldName]);
                if($_REQUEST[$FieldKey]==$data[$FieldName])
                {
                    $html.= '<option value="'.$data[$FieldName].'" selected="selected">'.$data[$FieldName].'</option>';
                }
                else
                {
                    $html.= '<option value="'.$data[$FieldName].'">'.$data[$FieldName].'</option>';
                }
             }
        }
    }

    return $html;
}

function fnFieldDropDown($FieldMappingID,$VariableName)
{
    $html= '<option value="" >Select</option>';
    $sql = "select * from productfieldvalue where Deleted=0 and FieldMappingID=".$FieldMappingID;        
    
    $res=mysql_query($sql);
    $numrows=mysql_num_rows($res);
    if($numrows>0)
    {
        while($obj=mysql_fetch_object($res))
        {
            if($_REQUEST[$VariableName]==$obj->ProductFieldValue)
            {
                $html.= '<option value="'.$obj->ProductFieldValue.'" selected>'.$obj->ProductFieldValue.'</option>';
            }
            else
            {
                $html.= '<option value="'.$obj->ProductFieldValue.'">'.$obj->ProductFieldValue.'</option>';
            }
        }
    }

    return $html;
}

function GetData($TableName,$ColumnID,$QueryID,$ReturnName)
{
    $sql = "select * from ".strtolower($TableName)." where ".$ColumnID."=".$QueryID;
    $res=mysql_query($sql);
    $obj=mysql_fetch_object($res);
    return $obj->$ReturnName;
}

function GetProductData($ProductID)
{
  $sql = "select * from products p inner join categories c on p.CategoryID =c.CategoryID 
        where p.ProductID=".$ProductID;
    $res=mysql_query($sql);
    $obj=mysql_fetch_object($res);
    $data = json_decode($obj->Fields,TRUE);
    return $data[$obj->ProductPrimaryName];
}

function LoggedInUser()
{
    if($_SESSION["UserID"]=="")
    {
        return false;
    }
    else
    {
        return true;
    }
}

function isSuperAdmin(){
    if($_SESSION["UserType"]=="1")
    {
        return true;
    }
    else
    {
        return false;
    }   
}

function slugify($string) {
   $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
   $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
   $string = preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
   $string = str_replace('-', '', $string);

   return $string;
}

function add_quotes($str) {
    return sprintf("'%s'", $str);
}

function post_img($fileName,$tempFile,$targetFolder)
{	
    if ($fileName!="")
	{
		if(!(is_dir($targetFolder)))
			mkdir($targetFolder);
		$counter=0;
		$NewFileName=$fileName;
		$NewFileName=str_replace(",","-",$NewFileName);
		$NewFileName=str_replace(" ","_",$NewFileName);	
		if(file_exists($targetFolder."/".$NewFileName))
		{
			do
			{ 
				$counter=$counter+1;
				$NewFileName=$counter."".$NewFileName;
			}
			while(file_exists($targetFolder."/".$NewFileName));
		}
		copy($tempFile, $targetFolder."/".$NewFileName);	
		return $NewFileName;
	}
}

function DateConverter($var){
    $date = str_replace('/', '-', $var);
    echo date('m-d-Y', strtotime($date));
}

function _group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}

function ConvertToCustomDateTime($dat){
    if($dat !="") {
        return date("d-m-Y H:i:s", strtotime($dat));
    }
}

function ConvertToCustomDate($dat){
    if($dat !="") {
        return date("d-m-Y", strtotime($dat));
    }
}

function ConvertToStdDate($dat){
    if($dat !="") {
        return date("Y-m-d", strtotime($dat));
    }
}

function ConvertToRupees($num){
    $explrestunits = "" ;
    if(strlen($num)>3){
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++){
            // creates each of the 2's group and adds a comma to the end
            if($i==0)
            {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            }else{
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return $thecash; // writes the final format where $currency is the currency symbol.
}

function convert_number_to_words($number) {

    $hyphen      = '-';
    $conjunction = ' and ';
    $separator   = ', ';
    $negative    = 'negative ';
    $decimal     = ' point ';
    $dictionary  = array(
        0                   => 'zero',
        1                   => 'one',
        2                   => 'two',
        3                   => 'three',
        4                   => 'four',
        5                   => 'five',
        6                   => 'six',
        7                   => 'seven',
        8                   => 'eight',
        9                   => 'nine',
        10                  => 'ten',
        11                  => 'eleven',
        12                  => 'twelve',
        13                  => 'thirteen',
        14                  => 'fourteen',
        15                  => 'fifteen',
        16                  => 'sixteen',
        17                  => 'seventeen',
        18                  => 'eighteen',
        19                  => 'nineteen',
        20                  => 'twenty',
        30                  => 'thirty',
        40                  => 'fourty',
        50                  => 'fifty',
        60                  => 'sixty',
        70                  => 'seventy',
        80                  => 'eighty',
        90                  => 'ninety',
        100                 => 'hundred',
        1000                => 'thousand',
        1000000             => 'million',
        1000000000          => 'billion',
        1000000000000       => 'trillion',
        1000000000000000    => 'quadrillion',
        1000000000000000000 => 'quintillion'
    );

    if (!is_numeric($number)) {
        return false;
    }

    if (($number >= 0 && (int) $number < 0) || (int) $number < 0 - PHP_INT_MAX) {
        // overflow
        trigger_error(
            'convert_number_to_words only accepts numbers between -' . PHP_INT_MAX . ' and ' . PHP_INT_MAX,
            E_USER_WARNING
        );
        return false;
    }

    if ($number < 0) {
        return $negative . convert_number_to_words(abs($number));
    }

    $string = $fraction = null;

    if (strpos($number, '.') !== false) {
        list($number, $fraction) = explode('.', $number);
    }

    switch (true) {
        case $number < 21:
            $string = $dictionary[$number];
            break;
        case $number < 100:
            $tens   = ((int) ($number / 10)) * 10;
            $units  = $number % 10;
            $string = $dictionary[$tens];
            if ($units) {
                $string .= $hyphen . $dictionary[$units];
            }
            break;
        case $number < 1000:
            $hundreds  = $number / 100;
            $remainder = $number % 100;
            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];
            if ($remainder) {
                $string .= $conjunction . convert_number_to_words($remainder);
            }
            break;
        default:
            $baseUnit = pow(1000, floor(log($number, 1000)));
            $numBaseUnits = (int) ($number / $baseUnit);
            $remainder = $number % $baseUnit;
            $string = convert_number_to_words($numBaseUnits) . ' ' . $dictionary[$baseUnit];
            if ($remainder) {
                $string .= $remainder < 100 ? $conjunction : $separator;
                $string .= convert_number_to_words($remainder);
            }
            break;
    }

    if (null !== $fraction && is_numeric($fraction)) {
        $string .= $decimal;
        $words = array();
        foreach (str_split((string) $fraction) as $number) {
            $words[] = $dictionary[$number];
        }
        $string .= implode(' ', $words);
    }

    return strtoupper($string);
}

function removeBOM($data) {
    if (0 === strpos(bin2hex($data), 'efbbbf')) {
       return substr($data, 3);
    }
    return $data;
}

function createColumnsArray($end_column, $first_letters = '')
{
  $columns = array();
  $length = strlen($end_column);
  $letters = range('A', 'Z');

  // Iterate over 26 letters.
  foreach ($letters as $letter) {
      // Paste the $first_letters before the next.
      $column = $first_letters . $letter;

      // Add the column to the final array.
      $columns[] = $column;

      // If it was the end column that was added, return the columns.
      if ($column == $end_column)
          return $columns;
  }

  // Add the column children.
  foreach ($columns as $column) {
      // Don't itterate if the $end_column was already set in a previous itteration.
      // Stop iterating if you've reached the maximum character length.
      if (!in_array($end_column, $columns) && strlen($column) < $length) {
          $new_columns = createColumnsArray($end_column, $column);
          // Merge the new columns which were created with the final columns array.
          $columns = array_merge($columns, $new_columns);
      }
  }

  return $columns;
}

function GetMAC(){
    ob_start();
    system('getmac');
    $Content = ob_get_contents();
    ob_clean();
    return substr($Content, strpos($Content,'\\')-20, 17);
}

function GetFieldCount($CategoryID){
$sql="select *, pft.ProductFieldType as Type from productfields pf 
    inner join fieldmapping fm on pf.ProductFieldID = fm.ProductFieldID
    inner join productfieldtype pft on pf.ProductFieldType = pft.ProductFieldTypeID   
    where fm.CategoryID=".$CategoryID." and fm.Deleted=0 order by fm.DisplayOrder";
    $res = mysql_query($sql);
    return mysql_num_rows($res);
}

?>