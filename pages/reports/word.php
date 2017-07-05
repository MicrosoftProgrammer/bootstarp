<?php
function CreateWordDoc($header,$data,$type,$filename){
header("Content-type: application/vnd.ms-word");
header("Content-Disposition: attachment;Filename=".$filename.".doc");

        $html=$html.'<table cellpadding="3" cellspacing="0" border="0" width="100%"
                    style="width:100%;font-size:9px;font-family:Arial, Helvetica, sans-serif;color:#333333;
                    text-align:left;border-collapse:collapse;">
                    <tr><td align="center" colspan="'.count($header).'" style="font-size:15px;font-weight:bold">'.$type.' Report</td></tr>
                    <tr>';
        $html=$html.'<th width="40" style="background-color:#5BA11D;color:#FFF;padding:7px;font-weight: bold;">S.No</th>';

        for ($i=0;$i<count($header);$i++)
        {
            $html =$html.'<th style="background-color:#5BA11D;color:#FFF;padding:2px;font-weight: bold;">'.$header[$i].'</th>';

        }

        $html =$html.'</tr>';
        $cnt=0;
        if($type=="Product" || $type=="ProductHistory" || $type=="date"){
            foreach($data as $datum){
                $cnt++;
                if($cnt%2==0)
                    $class='style="background-color:#cfeae5;"';
                else
                    $class='style="background-color:#FFFFFF;"';

                $html =$html.'<tr '.$class.'>';
                foreach($header as $key){
                    $html =$html.'<td style="padding:2px;">'.$datum[$key].'</td>';
                }

                $html =$html.'</tr>';
            }
        }
        else if($type=="Overview"){
            foreach($data as $datum => $value){
                $cnt++;
                if($cnt%2==0)
                    $class='style="background-color:#cfeae5;"';
                else
                    $class='style="background-color:#FFFFFF;"';

                $html =$html.'<tr '.$class.'>';
                $html =$html.'<td style="padding:2px;">'.$cnt.'</td>';
                $html =$html.'<td style="padding:2px;">'.$datum.'</td>';
                $html =$html.'<td style="padding:2px;">'.$value.'</td>';

                $html =$html.'</tr>';
            }            
        }  

        $html=$html .'</table>';

        echo $html;
        exit();
}
?>