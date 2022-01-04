<?php

$mreceipt = $this->input->post('mreceipt');

foreach ($user_find as $u_find):
    $re_url = $u_find['RETURN_URL'];
    $user_id = $u_find['USERNAME'];
endforeach;

foreach ($col_mreceipt as $mreceipt):
    //$d_hour= $mreceipt['DIFF_HOUR'];
    $sdate = $mreceipt['SDATE'];
    $rdate = $mreceipt['REDATE'];
    $u_id = $mreceipt['USER_ID'];
    $status = $mreceipt['STATUS'];
    $already = $mreceipt['ASD'];
endforeach;

if (base_url() == 'http://118.127.100.101/banktrans/') {

//echo $already.$status;

    if ($user_id == $u_id) {
        if ($sdate <> $rdate) {
            $pinfoarray[] = array
                (
                STATUS => "NOTCANCEL",
                NO_OF_SUB => 4
            );
        } else if ($status == 'CANCEL') {
            $pinfoarray[] = array
                (
                STATUS => $already . "CANCEL",
                NO_OF_SUB => 4
            );
        }
    } else {
        $pinfoarray[] = array
            (
            STATUS => "User/pass Not ok",
            NO_OF_SUB => 4
        );
    }
} else {
    $pinfoarray[] = array
        (
        STATUS => "URL/User/pass Not ok",
        NO_OF_SUB => 4
    );
}

$jpinfoarray = json_encode($pinfoarray);
echo $jpinfoarray;
?>