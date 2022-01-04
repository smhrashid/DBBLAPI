<?php

class Mricipt_model extends CI_Model {

    public $scima = 'IPL.COLLECTION_WEB_NEW';

    public function get_user() {
        $marid = $this->input->post('marid');
        $marpass = $this->input->post('marpass');
        $query_user = "select * from ipl.users_bank where USERNAME='$marid' and PASSWORD='$marpass'";
        $q_u_find = $this->db->query($query_user);
        return $q_u_find->result_array();
    }

    public function get_col() {
        $scima = $this->scima;
        $marid = $this->input->post('marid');
        $marpass = $this->input->post('marpass');
        $query_user = "select *  from ipl.users_bank  where USERNAME='$marid' and PASSWORD='$marpass'";

        $qu_find = $this->db->query($query_user);

        foreach ($qu_find->result() as $r_user):
            $user = $r_user->USERNAME;
            $bcode = $r_user->BCODE;
            $us[] = $r_user->USERNAME;
        endforeach;

        if (count($us) == 1) {
            $orderid = $this->input->post('orderid');
            $netrec = $this->input->post('netrec');

            $query_up_val = "UPDATE $scima SET CR_NO=('AB'||lpad(VPC_MERCHTXNREF_SEQ.nextval+1,8,0)),STATUS='test',NOS_PREMNO=1 where USER_ID='$user' and id_no ='$orderid' and status is null and CR_NO is null";
            $find_up_val = $this->db->query($query_up_val);

            /**
             * Created by PhpStorm.
             * User: sakthikarthi
             * Date: 9/22/14
             * Time: 11:26 AM
             * Converting Currency Numbers to words currency format
             */
            $number = $netrec;
            $no = round($number);
            $point = round($number - $no, 2) * 100;
            $hundred = null;
            $digits_1 = strlen($no);
            $i = 0;
            $str = array();

            $words = array('0' => '', '1' => 'One', '2' => 'Two',
                '3' => 'Three', '4' => 'Four', '5' => 'Five', '6' => 'Six',
                '7' => 'Seven', '8' => 'Eight', '9' => 'Nine',
                '10' => 'Ten', '11' => 'Eleven', '12' => 'Twelve',
                '13' => 'Thirteen', '14' => 'Fourteen',
                '15' => 'Fifteen', '16' => 'Sixteen', '17' => 'Seventeen',
                '18' => 'Eighteen', '19' => 'Nineteen', '20' => 'Twenty',
                '30' => 'Thirty', '40' => 'Forty', '50' => 'Fifty',
                '60' => 'Sixty', '70' => 'Seventy',
                '80' => 'Eighty', '90' => 'Ninety');
            $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
            while ($i < $digits_1) {
                $divider = ($i == 2) ? 10 : 100;
                $number = floor($no % $divider);
                $no = floor($no / $divider);
                $i += ($divider == 10) ? 1 : 2;
                if ($number) {
                    $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
                    $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
                    $str [] = ($number < 21) ? $words[$number] .
                            " " . $digits[$counter] . $plural . " " . $hundred :
                            $words[floor($number / 10) * 10]
                            . " " . $words[$number % 10] . " "
                            . $digits[$counter] . $plural . " " . $hundred;
                } else
                    $str[] = null;
            }
            $str = array_reverse($str);
            $result = implode('', $str);
            $points = ($point) ?
                    "." . $words[$point / 10] . " " .
                    $words[$point = $point % 10] : '';
            $tk_in_w = $result . "Taka" . $points . "";

            $query_col = "select (select project  from ipl.project where PRJ_CODE=a.PRJ_CODE) PROJECT_NAME,POLICY policy_number,NAME name_of_policyholder,DATCOM comm_date,decode(PAYMODE ,'1','Yearly','2','Half Yearly','3','Quaterly','5','Monthly','') pay_mode,
PNEXTPAY DUE_DATE,PLAN plan_term,SUMASS sum_assured,TOTPREM installment_prem,PHONE mobile_number,ORG_SETUP org_setup,UP_POL_PREMNO total_installment_paid,UP_POL_TOTPAYED total_prem_paid,UP_SUSPANSE suspense_balance,UP_PNEXTPAY next_due_date,CR_NO receipt_no,RCPT_DATE receipt_date,NOS_PREMNO nos_of_installment,PREM_AMNT premium_amount,LATE_FEES,TOT_PAYABLE,DISCOUNT waiver_discount,SUSPANSE_AMT,NET_RECIV net_receivable,'$tk_in_w' tk_in_w,POL_STATUS,STATUS,
'2' NO_OF_SUB from $scima a where ID_NO='$orderid'";
            $find_col = $this->db->query($query_col);
            return $find_col->result_array();
        }
    }

}

?>