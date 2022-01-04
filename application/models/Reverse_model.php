<?php
class Reverse_model extends CI_Model {
	public $scima = 'IPL.COLLECTION_WEB_NEW';
 
   public function get_user() {
	   
	   $marid=$this->input->post('marid');
	    $marpass=$this->input->post('marpass');
		$query_user = "select * from ipl.users_bank where USERNAME='$marid' and PASSWORD='$marpass'";
		$q_u_find = $this->db->query($query_user);  
       return $q_u_find->result_array();
  }
  
     public function get_mreceipt() {
		$scima = $this->scima;
		$marid=$this->input->post('marid');
	    $mreceipt=$this->input->post('mreceipt');
		
		$query_mreceipt = "select STATUS from $scima where CR_NO='$mreceipt' and   trunc(sysdate) = trunc(RCPT_DATE) and USER_ID='$marid'";
		$q_mreceipt = $this->db->query($query_mreceipt);
		 
		foreach ($q_mreceipt->result() as $r_mreceipt):
		$status= $r_mreceipt->STATUS;
		endforeach;
		
		if (isset ($status) && $status=='SUCCESS'){
		$query_cancel = "UPDATE $scima SET STATUS='CANCEL' where  CR_NO='$mreceipt'";
		$find_cancel = $this->db->query($query_cancel);
		$query_mreceipt = "select trunc(sysdate) sdate, trunc(RCPT_DATE) redate,USER_ID,status, '' asd from $scima where CR_NO='$mreceipt'";
		}
		
		else if (isset ($status) && $status=='CANCEL'){
		$query_mreceipt = "select trunc(sysdate) sdate, trunc(RCPT_DATE) redate,USER_ID,status, 'ALREADY' asd from $scima where CR_NO='$mreceipt'";
		}
		$q_mreceipt = $this->db->query($query_mreceipt);  
       return $q_mreceipt->result_array();
  }
  
}
