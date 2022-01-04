<?php

foreach ($user_find as $u_find):
    $re_url = $u_find['RETURN_URL'];
    $user_id = $u_find['USERNAME'];
    $bcode = $u_find['BCODE'];
endforeach;

//if (base_url() == 'http://118.127.100.101/banktrans/') {
    $cuser = count($user_find);
    if ($cuser == 1) {

        foreach ($col_found as $cul_find):
            $pinfoarray[] = $cul_find;
        endforeach;
    }
//}
$jpinfoarray = json_encode($pinfoarray);

echo $jpinfoarray;
/*
  echo "<script type='text/javascript'>
  function submitform()
  {
  feeds.submit();
  }
  </script>

  <form name = 'feeds' action='".$re_url."' method='post'>
  <input type='hidden'  name='returnplil' value ='".$jpinfoarray."' />
  </form>
  <script>
  window.onload = submitform;
  </script>";
 */
?>
