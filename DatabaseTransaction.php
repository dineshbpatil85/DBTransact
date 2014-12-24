<?php
/********************************************************************************
*       Filename: database.php
*       Purpose: All the generic database related functions and global object functions are located in this
*   file. The main pages call the global database functions and depending on the parameters passed,
*       queries are generated dynamically and results are returned to the processing script.

***********************************************************************************/

include_once("SiteConfig.php");

Class DBTransact {

        var $row_cnt = 0;

        function Connect()
        {
                mysql_connect(HST, USR, PWD) OR die("Failed Connecting To Mysql");
                mysql_select_db(DBN) OR die("Failed Connecting To Database");
        }

        function Close()
        {
                mysql_close() OR die("Failed To Close Connection.");
        }

        /*************************************************************************
        *       Table of Contents: Global Database functions
        ***************************************************************************/

        function customqry($qry, $prn)
        {
                $rs=@mysql_query($qry);
                if($prn)
                        echo $qry;
                return $rs;
        }

        /**************************************************************************************
        *       Table of Contents: Global Database functions
        *       1. globalselect($tblname, $wherefields, $wherevalues, $orderbyfield, $ad)
        *       Select records from table.
        ***************************************************************************************/

        function cgs($tbl, $sf, $wf, $wv, $ob, $ot, $prn)
        {

                $sql = "SELECT ";
                if(is_array($sf))
                {
                        $fields = implode(",", $sf);
                }
                else
                {
                        if($sf)
                        $fields = $sf;
                        else
                        $fields = "*";
                }
                if(is_array($wf))
                {
                        if(sizeof($wf) > 0)
                        {
                                for($j=0; $j<sizeof($wf); $j++)
                                {
                                        if(is_numeric($wv[$j]))//strstr($wv[$j],".") && !strstr($wv[$j],"@"))
                                        $condition.= " " . $wf[$j] ."='". mysql_real_escape_string($wv[$j]). "' ";
                                        else
                                        $condition.= " " . $wf[$j] ."='". mysql_real_escape_string($wv[$j]). "' ";

                                        if($j<sizeof($wf)-1)
                                        $condition .= " and ";
                                }
                        }
                }
                else
                {
                        if($wf)
                        $condition = " $wf = '$wv' ";
                        else
                        $condition ="1";
                }


                $query = $sql.''.$fields." FROM ".$tbl." WHERE ".$condition;
                if($ob)
                {
                        $query.=" ORDER BY ".$ob;
                }

                if($ot)
                {
                        $query.=" ".$ot;
                }
                if($prn)
                {
                        echo $query;
                }

                $result = @mysql_query($query);// or die(mysql_error());
                $num = mysql_num_rows($result);
                if($num<1)
                {
                        $retval = "n";
                }
                else
                {
                        $retval = $result;
                }

                return $retval;
        }
/*****************************************************************************************************************
*       Table of Contents: Global Database functions
*       1. globalchkexist($tblname,$wherefield,$wherevalue);
*       Check wheather the particular value exist in the table or not.
*****************************************************************************************************************/
        function globalchkexist($tblname, $wherefields, $wherevalues, $prn)
        {
                 $query.=" SELECT * FROM  ".$tblname ;
                if(is_array($wherefields))
                {
                        if(sizeof($wherefields) > 0)
                        {
                                for($j=0; $j<sizeof($wherefields); $j++)
                                {
                                        if(strstr($wherevalues[$j],".") && !strstr($wherevalues[$j],"@"))
                                        $condition.= " $wherefields[$j] = '$wherevalues[$j]' ";
                                        else
                                        $condition.= " $wherefields[$j] = '$wherevalues[$j]' ";

                                        if($j<sizeof($wherefields)-1)
                                        $condition .= " and ";
                                }
                        }
                }
                else
                {
                        if($wherefields)
                         $condition = " $wherefields = '$wherevalues' ";
                        else
                        $condition ="1";
                }

                /*if($wherefield)
                {
                        $condition.=" $wherefield = '$wherevalue' ";
                }
                else
                {
                        $condition ="1";
                }*/

                 $query.=" WHERE $condition ";
                //echo "<br>asa".$query;
                //die;

                if($prn==1)
                {
                        echo $query; exit;
                }
                $result = @mysql_query($query);// or die(mysql_error());

                //return (@mysql_num_rows($result) > 0)?true:false;
                $mm = @mysql_num_rows($result);
                if($mm=="")
        {
                        $a =0;
                }
                else if($mm==0)
                {
                        $a = 0;
                }
                else
                {
                        $a = $mm;
                }
                return $a;

        }

function cgi($tbl, $fl, $vl, $prn)
        {

        $tblname = $tbl;
        $fields = $fl;
        $values = $vl;

                $sql.= "INSERT INTO ".$tblname." ";

                $fieldnames.="(";
                if(is_array($fields))
                {
                        for($i=0; $i<sizeof($fields); $i++)
                        {
                                $fieldnames.= $fields[$i];
                                if($i<sizeof($fields)-1)
                                $fieldnames.= ", ";
                        }
                        $fieldnames.= ") ";

                        $value.= " VALUES (";
                        if(sizeof($values) > 0)
                        {
                                for($i=0; $i<sizeof($values); $i++)
                                {
                                        $value.= "'".$this->sanitize($values[$i])."'";
                                        if($i<sizeof($values)-1)
                                        $value.= ", ";
                                }
                        }
                        $value.= ")";
                }
                else
                {
                        $fieldnames .= $fields.')';
                        $value = " VALUES "."('".$values."')";
                }
                 $query = $sql.$fieldnames.$value;
                if($prn)
                {
                        echo $query;
                }

                $result = @mysql_query($query);// or die(mysql_error());
                return mysql_insert_id();
        }


        /******************************************************************
        *       Table of Contents: Global Database functions 
        *       1. globaldelete($tblname,$wherefield,$wherevalue);
        *       Delete particular record from the table.
        **********************************************************************/

        function gdel($tbl, $wf, $wv, $prn)
        {

                $query.=" DELETE FROM  ".$tbl ;
                if(is_array($wf))
                {
                        if(sizeof($wf) > 0)
                        {
                                for($j=0; $j<sizeof($wf); $j++)
                                {

                                        $condition.=" $wf[$j] = '$wv[$j]'";
                                        if($j<sizeof($wf)-1)

                                        $condition.=" and";

                                }
                        }
                }
                else
                {
                        $condition = "$wf = '$wv'";
                }
                $query.=" WHERE $condition ";
                if($prn)
                {
                        echo $query;
                        exit;
                }
                $result = @mysql_query($query);// or die(mysql_error());
                return $result;
        }

        /***********************************************************************************
        *       Table of Contents: Global Database functions
        *       1. cupdt($tblname,$setfield,$setvalue,$wherefields,$wherevalues);
        *       Update record in the table.
        *******************************************************************************************/

function cupdt($tbl, $sf, $sv, $wf, $wv, $prn)
        {
                $query.=" UPDATE ".$tbl." SET " ;

                /* Here updating fields and values are composed */

                if(is_array($sf))
                {
                        if(sizeof($sf) > 0)
                        {
                                for($j=0; $j<sizeof($sf); $j++)
                                {
                                        $update_vars.= " $sf[$j] = '".$this->sanitize($sv[$j])."' ";

                                        if($j<sizeof($sf)-1)
                                        $update_vars .= ", ";
                                }
                        }
                }
                else
                {
                        $update_vars.= " $sf = '$sv' ";
                }

                $query.= $update_vars;

                /*Here condition is created*/

                if(is_array($wf))
                {
                        if(sizeof($wf) > 0)
                        {
                                for($k=0; $k<sizeof($wf); $k++)
                                {
                                        $condition.= " $wf[$k] = '$wv[$k]' ";

                                        if($k<sizeof($wf)-1)
                                        $condition .= " and ";
                                }
                        }
                }
                else
                {
                        if($wf)
                                $condition = $wf." = '$wv' ";
                        else
                                $condition="1";
                }
                $query.= " WHERE $condition ";
                if($prn==1)
                {
                        echo $query;
                }
                $result = @mysql_query($query);// or die(mysql_error());
                return $result;
        }

function cupdt_trial($tbl, $sf, $sv, $wf, $wv, $prn)
        {
                $query.=" UPDATE ".$tbl." SET " ;

                /* Here updating fields and values are composed */

                if(is_array($sf))
                {
                        if(sizeof($sf) > 0)
                        {
                                for($j=0; $j<sizeof($sf); $j++)
                                {
                                        $update_vars.= " $sf[$j] = '".$this->sanitize($sv[$j])."' ";

                                        if($j<sizeof($sf)-1)
                                        $update_vars .= ", ";
                                }
                        }
                }
                else
                {
                        $update_vars.= " $sf = '$sv' ";
                }

                $query.= $update_vars;

                /*Here condition is created*/

                if(is_array($wf))
                {
                        if(sizeof($wf) > 0)
                        {
                                for($k=0; $k<sizeof($wf); $k++)
                                {
                                        $condition.= " $wf[$k] = '$wv[$k]' ";

                                        if($k<sizeof($wf)-1)
                                        $condition .= " and ";
                                }
                        }
                }
                else
                {
                        if($wf)
                                $condition = $wf." = '$wv' ";
                        else
                                $condition="1";
                }
                $query.= " WHERE $condition ";
                if($prn==1)
                {
                        echo $query;
                }
                $result = @mysql_query($query);// or die(mysql_error());
                return $result;
        }


        /*********************************************************************************
        * Function : Creating for complex join query by passing directly condition string
        *
        * Validation Type: PHP Server Side
        * globaljoinquery($tblname, $selectfields , $condition, $orderbyfield, $groupby, $ad, $limit)
        *********************************************************************************/

        function gj($tbl, $sf , $cd, $ob, $gb, $ad, $l, $prn)
        {
                if(is_array($sf))
                {
                        $fields = implode(",", $sf);
                }
                else
                {
                        if($sf)
                        $fields = $sf;
                        else
                        $fields = "*";
                }

                $query="SELECT ".$fields." FROM  ".$tbl ;

                $query.=" WHERE $cd ";

                if($gb)
                $query.=" group by ".$gb;

                if($ob)
                $query.=" order by ".$ob." ".$ad;



                if($l)
                $query.=" limit ".$l;
                if($prn!="")
                {
                echo $query;
                }
                $result = @mysql_query($query);// or die(mysql_error());
                $num = @mysql_num_rows($result);
                if($num<1)
                {
                        $result = 'n';
                }
                return $result;
        }


function remaining_balance()
{
$balance=0;


if($_SESSION['csUserId'])
{
$userid=$_SESSION['csUserId'];

//SELECT symbol FROM tbl_users AS u, mast_currency AS m WHERE u.currency_id = m.currencyid AND u.userid = '19'
$cd="u.currency_id = m.currencyid AND u.userid = ".$userid;

$rs=$this->gj("tbl_users AS u, mast_currency AS m","symbol",$cd,"","","","","");
if($rs!='n')
{
$rs1=mysql_fetch_assoc($rs);
$symbol=$rs1['symbol'];
}

//select sum(u.amount),u.description from tbl_user_account u,mast_user_account as m where
//u.mast_account_id=m.mast_account_id and m.user_id='19'
//group by u.description
//gj($tbl, $sf , $cd, $ob, $gb, $ad, $l, $prn)
//$cnd="u.mast_account_id=m.mast_account_id and m.user_id=".$userid;

//$cnd="user_id=19";
$cnd="user_id=".$userid;
$rs1=$this->gj("mast_user_account","available_amount",$cnd,"","","","","");
                if($rs1!='n')
                {
                        while($result1=mysql_fetch_assoc($rs1))
                        {
                        $available_amount=$result1['available_amount'];
                        }
                }
//echo $available_amount;
$balance=$available_amount;

//$cnd="u.mast_account_id = m.mast_account_id and m.user_id=19";
$cnd="u.mast_account_id = m.mast_account_id and m.user_id=".$userid;
$rs=$this->gj("tbl_user_account u,mast_user_account as m","sum(u.amount) as amt,u.description",$cnd,"","u.description","","","");
                if($rs!='n')
                {
                        while($result=mysql_fetch_assoc($rs))
                        {
                        if($result['description']=='deposit')
                        $balance=$balance+$result['amt'];
                        elseif($result['description']=='transfer')
                        $balance=$balance-$result['amt'];
                        elseif($result['description']=='withdraw')
                        $balance=$balance-$result['amt'];
                        }
                }
//echo $balance;
//exit;
                if($balance < 0 || $balance == '')
                {
                $balance=0;
                }
$balance=$symbol.$balance;

return($balance);
}


}//function end

function remaining_balance_admin($userid)
{

$balance=0;

//SELECT symbol FROM tbl_users AS u, mast_currency AS m WHERE u.currency_id = m.currencyid AND u.userid = '19'
$cd="u.currency_id = m.currencyid AND u.userid = ".$userid;

$rs=$this->gj("tbl_users AS u, mast_currency AS m","symbol",$cd,"","","","","");
if($rs!='n')
{
$rs1=mysql_fetch_assoc($rs);
$symbol=$rs1['symbol'];
}

//select sum(u.amount),u.description from tbl_user_account u,mast_user_account as m where
//u.mast_account_id=m.mast_account_id and m.user_id='19'
//group by u.description
//gj($tbl, $sf , $cd, $ob, $gb, $ad, $l, $prn)
//$cnd="u.mast_account_id=m.mast_account_id and m.user_id=".$userid;

//$cnd="user_id=19";
$cnd="user_id=".$userid;
$rs1=$this->gj("mast_user_account","available_amount",$cnd,"","","","","");
                if($rs1!='n')
                {
                        while($result1=mysql_fetch_assoc($rs1))
                        {
                        $available_amount=$result1['available_amount'];
                        }
                }
//echo $available_amount;
$balance=$available_amount;

//$cnd="u.mast_account_id = m.mast_account_id and m.user_id=19";
$cnd="u.mast_account_id = m.mast_account_id and m.user_id=".$userid;
$rs=$this->gj("tbl_user_account u,mast_user_account as m","sum(u.amount) as amt,u.description",$cnd,"","u.description","","","");
                if($rs!='n')
                {
                        while($result=mysql_fetch_assoc($rs))
                        {
                        if($result['description']=='deposit')
                        $balance=$balance+$result['amt'];
                        elseif($result['description']=='transfer')
                        $balance=$balance-$result['amt'];
                        elseif($result['description']=='withdraw')
                        $balance=$balance-$result['amt'];
                        }
                }
//echo $balance;
//exit;
                if($balance < 0 || $balance == '')
                {
                $balance=0;
                }

$balance=$symbol.$balance;

return($balance);

}//function end



        /*****************************************************************************************************************
        *       Table of Contents: Global Database functions 
        *       1. globaldropdown($tblname, $valfield, $showfield, $orderbyfield, $condition, $selvalue)
        *
        *****************************************************************************************************************/
        function cddSmarty($tbl, $valfield, $showfield, $ob, $cdn, $selvalue, $prn)
        {
                //echo "hello";
                $query.=" SELECT ".$showfield.", ".$valfield." FROM  ".$tbl ;
                $query.=" WHERE $cdn ORDER BY ".$ob;
                if($prn)
                {
                        echo $query;
                }
                $opt    ='';
                $result = @mysql_query($query);// or die(mysql_error());
                $num = mysql_num_rows($result);
                if($num<1)
                {
                        return "n";
                }
                else
                {
                        for($k=0; $k<mysql_num_rows($result); $k++)
                        {
                                $row=mysql_fetch_array($result);

                                if($selvalue == $row[$valfield])
                                $selected = " selected";
                                else
                                $selected = "";

                                $opt    .= "<option value='".$row[$valfield]."' ".$selected.">".$row[$showfield]."</option>\n";

                        }
                        return $opt;
                }
        }

        /*
        ######################################################################################################
        function name:-is_login
        description:check whether user login or not
        #######################################################################################################*/


                function not_login()
                {
                        if(!$_SESSION['csUserId'])
                        {
                                header("Location:".SITEROOT."/modules/login");
                        }
                }

                /******************Function to get subcategory ids**********************/
                function getsubcatid($catid,$cat_tree,$max_level=0,$catstr)
         {

               $sql = "SELECT * FROM tbl_questions_category WHERE status='1' and parent_id='".$catid."' ORDER BY category_name ASC";
               $run_qry = @mysql_query($sql);
               $nums = @mysql_num_rows($run_qry);

              if($nums)
              {
                    while($catids = @mysql_fetch_array($run_qry))
                     {
                      $max_level=$max_level+ 1;
                      if($catstr=="")
                       {
                         $catstr=$catids['id'];
                       }else
                       {
                        $catstr=$catstr.",".$catids['id'];
                       }
                       $catstr=$this->getsubcatid($catids['id'], $cat_tree, $max_level,$catstr);
                     }
          }
          return $catstr;

 }

 /*********************************************************************************

* Function : ETranslate($id, $lang)

* Description : This function will use to sanitize your data

*********************************************************************************/

        function sanitize($data)
        {
                // remove whitespaces (not a must though)
                $data = trim($data);

                // apply stripslashes if magic_quotes_gpc is enabled
                if(get_magic_quotes_gpc())
                {
                $data = stripslashes($data);
                }

                // a mySQL connection is required before using this function
                $data = mysql_real_escape_string($data);

                return $data;
        }

        function getInfo($table_name='',$wantedfield='',$wherefield='',$fieldValue='')
        {
        $user = $this->cgs($table_name,$wantedfield,$wherefield, $fieldValue, "", "", false);
                                        if($user != 'n')
                                        {
                                        $rowsuse = mysql_fetch_array($user);
                                        $value = ucfirst($rowsuse[$wantedfield]);
                                        if($rowsuse[$wantedfield] == "")
                                        {  $value = "NA"; }
                                        }
                                        else
                                        { $value = "" ;}
                                        return $value;
        }





/**********************Function to check for valid browser agent********************/


function is_valid_browser()

{

                $arr = $this->browser_info();

                if($arr['name'] == 'msie' && $arr['version'] < 7)

                {

                                $_SESSION['is_valid_browser'] = 0;

                }

                else

                                $_SESSION['is_valid_browser'] = 1;

                return   $_SESSION['is_valid_browser'];

}





//Function get browser information

function browser_info($agent=null)

{

                // Declare known browsers to look for

                $known = array('msie', 'firefox', 'safari', 'webkit', 'opera', 'netscape',

                'konqueror', 'gecko');



                // Clean up agent and build regex that matches phrases for known browsers

                // (e.g. "Firefox/2.0" or "MSIE 6.0" (This only matches the major and minor

                // version numbers.  E.g. "2.0.0.6" is parsed as simply "2.0"

                $agent = strtolower($agent ? $agent : $_SERVER['HTTP_USER_AGENT']);

               // $pattern = '#(?<browser>' . join('|', $known) .

                //')[/ ]+(?<version>[0-9]+(?:\.[0-9]+)?)#';



                // Find all phrases (or return empty array if none found)

                //if (!preg_match_all($pattern, $agent, $matches)) return array();



                // Since some UAs have more than one phrase (e.g Firefox has a Gecko phrase,

                // Opera 7,8 have a MSIE phrase), use the last one found (the right-most one

                // in the UA).  That's usually the most correct.

                $i = count($matches['browser'])-1;



                //return array($matches['browser'][$i] => $matches['version'][$i]);

                $arr['name'] = $matches['browser'][$i];

                $arr['version'] = $matches['version'][$i];

                return $arr;

}



}

$dbObj = new DBTransact();
$dbObj->Connect();

?>
