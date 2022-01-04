<?php

class Prime_model extends CI_Model {

    public function get_pol() {
        $marid = $this->input->post('marid');
        $marpass = $this->input->post('marpass');
        $policy_number = $this->input->post('polnum');
        
        
                
        $query_user = "select *  from ipl.users_bank  where USERNAME='$marid' and PASSWORD='$marpass'";
        $qu_find = $this->db->query($query_user);
        foreach ($qu_find->result() as $r_user):
            $user = $r_user->USERNAME;
            $bcode = $r_user->BCODE;
            $us[] = $r_user->USERNAME;
        endforeach;
     /*   
        foreach ($this->prime_model->get_user() as $r_user):
            $user = $r_user->USERNAME;
            $bcode = $r_user->BCODE;
            $us[] = $r_user->USERNAME;
        endforeach;
*/
        if (count($us) == 1) {
            /* 	
              $sql_prem= "select POLICY,totprem ,premno,paymode, pnextpay,NAME,datcom, DOB,SUSPENSE,STATUS,matdate+1 mm, MONTHS_BETWEEN(SYSDATE,PNEXTPAY) ss,
              IPL.LATE_FEE(POLICY,PRJ_CODE,PLAN,TERM,PAYMODE,TOTPREM,PNEXTPAY,
              case when paymode='5' then floor(months_between(sysdate,pnextpay))
              when paymode='1' then floor(floor(months_between(sysdate,pnextpay))/12)
              when paymode='2' then floor(floor(months_between(sysdate,pnextpay))/6)
              when paymode='4' then floor(floor(months_between(sysdate,pnextpay))/3)
              end,SYSDATE,0) LATEFEE,
              'plil'||lpad(VPC_MERCHTXNREF_SEQ.nextval+1,8,0) orderid,(plan||'-'||term) PLAN,SUMASS,PHONE,PRJ_CODE,(oc1 || '/' ||oc2|| '/' ||oc3|| '/' ||oc4|| '/' ||oc5||'/'||oc6||'/'||oc7||'/'||oc8||'/'||oc9||'/'||oc10)as org_setup,TOTPAID,DECODE(PRJ_CODE,'05','p','24','p','i') PRJ_TYPE
              from  IPL.ALL_POLICY where policy= '$policy_number'";
             */
            $sql_prem = "select POLICY,totprem ,premno,paymode, pnextpay,NAME,datcom, DOB,SUSPENSE,STATUS,matdate+1 mm, MONTHS_BETWEEN(SYSDATE,PNEXTPAY) ss,
                        IPL.LATE_FEE(POLICY,PRJ_CODE,PLAN,TERM,PAYMODE,TOTPREM,PNEXTPAY,1,SYSDATE,0) LATEFEE,
                        'plil'||lpad(VPC_MERCHTXNREF_SEQ.nextval+1,8,0) orderid,(plan||'-'||term) PLAN,SUMASS,PHONE,PRJ_CODE,(oc1 || '/' ||oc2|| '/' ||oc3|| '/' ||oc4|| '/' ||oc5||'/'||oc6||'/'||oc7||'/'||oc8||'/'||oc9||'/'||oc10)as org_setup,TOTPAID,DECODE(PRJ_CODE,'05','p','24','p','i') PRJ_TYPE
                        from  IPL.ALL_POLICY where policy= '$policy_number'";
            $query_prem = $this->db->query($sql_prem);

            foreach ($query_prem->result() as $row_prem):
                $policy = $row_prem->POLICY;
                $name = $row_prem->NAME;
                $datcom = $row_prem->DATCOM;
                $paymode = $row_prem->PAYMODE;
                $pnextpay = $row_prem->PNEXTPAY;
                $plan = $row_prem->PLAN;
                $sumass = $row_prem->SUMASS;
                $totprem = $row_prem->TOTPREM;
                $phone = $row_prem->PHONE;
                $prj_code = $row_prem->PRJ_CODE;
                $org_setup = $row_prem->ORG_SETUP;
                $premno = $row_prem->PREMNO;
                $totpaid = $row_prem->TOTPAID;
                $suspense = $row_prem->SUSPENSE;
                $latefee = $row_prem->LATEFEE;
                $status = $row_prem->STATUS;
                $mm = $row_prem->MM;
                $ss = $row_prem->SS;
                if ($status == 'M') {
                    $p_st = 0;
                    $pol_stat = "Policy Alrrady Matured";
                } elseif ($status == 'D') {
                    $p_st = 0;
                    $pol_stat = "Date claim Intimated";
                } elseif ($status == 'S') {
                    $p_st = 0;
                    $pol_stat = "Policy alrrady Surrenderad";
                } elseif ($status == 'C') {
                    $p_st = 0;
                    $pol_stat = "Policy is Cancelled";
                } elseif ($pnextpay == $mm) {
                    $p_st = 0;
                    $pol_stat = "Alrrady Matured";
                } elseif ($ss > 60) {
                    $p_st = 0;
                    $pol_stat = "Special Revival Requerd";
                } elseif ($ss > 3 && $ss < 60) {
                    $p_st = 1;
                    $pol_stat = "DGH Requerd";
                } else {
                    $p_st = 1;
                    $pol_stat = "Policy Inforce";
                }
                if ($paymode == '1') {
                    $term_month = '12';
                } elseif ($paymode == '2') {
                    $term_month = '6';
                } elseif ($paymode == '4') {
                    $term_month = '3';
                } elseif ($paymode == '5') {
                    $term_month = '1';
                }
                $n_du = (ceil($ss / $term_month));

                if ($n_du <= 0) {
                    $num_due = 1;
                } else {
                    $num_due = $n_du;
                }
                $prem_amnt = $totprem * 1;
                $tot_payable = $prem_amnt + $latefee;
                $discount = 0;
                $net_reciv = $tot_payable - $discount - $suspense;
                $up_suspanse = 0;
                $up_pol_premno = 1 + $premno;
                $up_pol_totpayed = $totpaid + $net_reciv;
                $orderid = $row_prem->ORDERID;
                $prj_type = $row_prem->PRJ_TYPE;
                $dob = $row_prem->DOB;
                $orde[] = $row_prem->ORDERID;
                $m_add = 1 * $term_month;
                if ($p_st == 1) {
                    $query = "INSERT INTO ipl.collection_web_new 
	(POLICY,NAME,DATCOM,PAYMODE,PNEXTPAY,PLAN,SUMASS,TOTPREM,PHONE,PRJ_CODE,ORG_SETUP,PREMNO,TOTPAID,UP_POL_PREMNO,UP_POL_TOTPAYED,UP_SUSPANSE,UP_PNEXTPAY,RCPT_DATE,NOS_PREMNO,PREM_AMNT,LATE_FEES,TOT_PAYABLE,DISCOUNT,SUSPANSE_AMT,NET_RECIV,ID_NO,USER_ID,PRJ_TYPE,BCODE,POL_STATUS)  
                  VALUES ('$policy','$name','$datcom','$paymode','$pnextpay','$plan','$sumass','$totprem','$phone','$prj_code','$org_setup','$premno','$totpaid','$up_pol_premno','$up_pol_totpayed','$up_suspanse',(add_months ('$pnextpay','$m_add')),sysdate,'$num_due','$prem_amnt','$latefee','$tot_payable','$discount','$suspense','$net_reciv',
										'$orderid','$user','$prj_type','$bcode','$pol_stat')";
                } else if ($p_st == 0) {
                    $query = "INSERT INTO ipl.collection_web_new 
		(POLICY,NAME,RCPT_DATE,USER_ID,PRJ_TYPE,BCODE,POL_STATUS,ID_NO)  
VALUES 
		('$policy','$name',sysdate,'$user','$prj_type','$bcode','$pol_stat','$orderid')";
                }
                $this->db->query($query);
            endforeach;

            if (isset($orde[0])) {
                $orderStr = implode("', '", $orde);

                $number = $net_reciv;
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
                // $query_all_data = "select POLICY policy_number,NAME name_of_policyholder,prj_code project_code,PNEXTPAY DUE_DATE, TOTPREM prm_installment,NOS_PREMNO n_due,PREM_AMNT tot_inst,LATE_FEES late_fee,SUSPANSE_AMT suspense,NET_RECIV total_dues,POL_STATUS status,ID_NO orderid,'1' NO_OF_SUB from ipl.collection_web_new WHERE ID_NO IN ('$orderStr')";
                $query_all_data = "select (select project  from ipl.project where PRJ_CODE=a.PRJ_CODE) PROJECT_NAME,prj_code project_code,POLICY policy_number,NAME name_of_policyholder,DATCOM comm_date,decode(PAYMODE ,'1','Yearly','2','Half Yearly','3','Quaterly','5','Monthly','') pay_mode,
                PNEXTPAY DUE_DATE,PLAN plan_term,SUMASS sum_assured,TOTPREM installment_prem,PHONE mobile_number,ORG_SETUP org_setup,UP_POL_PREMNO total_installment_paid,UP_POL_TOTPAYED total_prem_paid,UP_SUSPANSE suspense_balance,UP_PNEXTPAY next_due_date,CR_NO receipt_no,RCPT_DATE receipt_date,NOS_PREMNO nos_of_installment,PREM_AMNT premium_amount,LATE_FEES,TOT_PAYABLE,DISCOUNT waiver_discount,SUSPANSE_AMT,NET_RECIV net_receivable,ID_NO orderid,'$tk_in_w' tk_in_w,POL_STATUS,STATUS,
                '1' NO_OF_SUB from ipl.collection_web_new a WHERE ID_NO IN ('$orderStr')";
                $find_all_data = $this->db->query($query_all_data);
                return $find_all_data->result_array();
            }
        }
    }

    public function get_prop() {
        $marid = $this->input->post('marid');
        $marpass = $this->input->post('marpass');
        $policy_number = $this->input->post('polnum');
        foreach ($this->prime_model->get_user() as $r_user):
            $user = $r_user->USERNAME;
            $bcode = $r_user->BCODE;
            $us[] = $r_user->USERNAME;
        endforeach;
        if (count($us) == 1) {
            $p = 'i';
            $query_fp = "select PROPNO,totprem ,paymode, pnextpay,NAME,datcom, DOB,SUSPENSE,STATUS, 
                        'plil'||lpad(VPC_MERCHTXNREF_SEQ.nextval+1,8,0) orderid,(plan||'-'||term) PLAN,SUMASS,PHONE,PRJ_CODE,(oc1 || '/' ||oc2|| '/' ||oc3|| '/' ||oc4|| '/' ||oc5||'/'||oc6||'/'||oc7||'/'||oc8||'/'||oc9||'/'||oc10)as org_setup,TOTPAID,DECODE(PRJ_CODE,'05','p','24','p','i') PRJ_TYPE
                        from   IPL.TPOLICY where PROPNO='$policy_number'";
            $query_prem = $this->db->query($query_fp);
            foreach ($query_prem->result() as $row_prem):
                $tp = $row_prem->TOTPREM;
            endforeach;
            if (!isset($tp)) {
                $p = 'p';
                $query_fp = "select PROPNO,totprem ,paymode, pnextpay,NAME,datcom, DOB,SUSPENSE,STATUS, 
                        'plil'||lpad(VPC_MERCHTXNREF_SEQ.nextval+1,8,0) orderid,(plan||'-'||term) PLAN,SUMASS,PHONE,PRJ_CODE,(oc1 || '/' ||oc2|| '/' ||oc3|| '/' ||oc4|| '/' ||oc5||'/'||oc6||'/'||oc7||'/'||oc8||'/'||oc9||'/'||oc10)as org_setup,TOTPAID,DECODE(PRJ_CODE,'05','p','24','p','i') PRJ_TYPE
                        from  pbpib.PB_PROPOSAL where PROPNO='$policy_number'";
                $query_prem = $this->db->query($query_fp);
                foreach ($query_prem->result() as $row_prem):
                    $tp = $row_prem->TOTPREM;
                endforeach;
            }
            if (isset($tp) && $tp >= 1) {
                foreach ($query_prem->result() as $row_prem):
                    $policy = $row_prem->policy_number;
                    $name = $row_prem->NAME;
                    $datcom = $row_prem->DATCOM;
                    $paymode = $row_prem->PAYMODE;
                    $pnextpay = $row_prem->PNEXTPAY;
                    $plan = $row_prem->PLAN;
                    $sumass = $row_prem->SUMASS;
                    $totprem = $row_prem->TOTPREM;
                    $phone = $row_prem->PHONE;
                    $prj_code = $row_prem->PRJ_CODE;
                    $org_setup = $row_prem->ORG_SETUP;
                    $premno = 0;
                    $totpaid = $row_prem->TOTPAID;
                    $suspense = 0;
                    $latefee = 0;
                    $status = $row_prem->STATUS;
                    //$mm= $row_prem->MM;
                    //$ss= $row_prem->SS;
                    $prem_amnt = $totprem * 1;
                    $tot_payable = $prem_amnt + $latefee;
                    $discount = 0;
                    $net_reciv = $tot_payable - $discount - $suspense;
                    $up_suspanse = 0;
                    $up_pol_premno = 1 + $premno;
                    $up_pol_totpayed = $totpaid + $net_reciv;
                    $orderid = $row_prem->ORDERID;
                    $prj_type = $row_prem->PRJ_TYPE;
                    $dob = $row_prem->DOB;
                    $orde[] = $row_prem->ORDERID;
                    $m_add = '';
                endforeach;
            }
            $query = "INSERT INTO ipl.collection_web_new 
	(POLICY,NAME,DATCOM,PAYMODE,PNEXTPAY,PLAN,SUMASS,TOTPREM,PHONE,PRJ_CODE,ORG_SETUP,PREMNO,TOTPAID,UP_POL_PREMNO,UP_POL_TOTPAYED,UP_SUSPANSE,UP_PNEXTPAY,RCPT_DATE,NOS_PREMNO,PREM_AMNT,LATE_FEES,TOT_PAYABLE,DISCOUNT,SUSPANSE_AMT,NET_RECIV,ID_NO,USER_ID,PRJ_TYPE,BCODE,POL_STATUS)  
                  VALUES ('$policy','$name','$datcom','$paymode','$pnextpay','$plan','$sumass','$totprem','$phone','$prj_code','$org_setup','$premno','$totpaid','$up_pol_premno','$up_pol_totpayed','$up_suspanse',(add_months ('$pnextpay','$m_add')),sysdate,'$num_due','$prem_amnt','$latefee','$tot_payable','$discount','$suspense','$net_reciv',
										'$orderid','$user','$prj_type','$bcode','$pol_stat')";
            $query_prem = $this->db->query($query);
        }
        return $query_prem;
    }

    public function get_user() {
        $marid = $this->input->post('marid');
        $marpass = $this->input->post('marpass');
        $query_user = "select * from ipl.users_bank where USERNAME='$marid' and PASSWORD='$marpass'";
        $q_u_find = $this->db->query($query_user);
        return $q_u_find->result_array();
    }

}

?>