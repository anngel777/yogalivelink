<?php

class BaseClass extends Lib_BaseClass
{
    public $yoga_type_list          = '';
    
    
    // ----------- construction ---------------
    public function  __construct()
    {
        parent::__construct();
        
        global $YOGA_TYPE_LIST;
        $this->yoga_type_list = $YOGA_TYPE_LIST;
    }
    
    
    public function FormatDataForUpdate($FormArray)
    {
        $keys_values = '';
        foreach ($FormArray as $var => $val) {
            $val = addslashes($val);
            
            $keys_values   .= "`$var`='$val', ";
        }
        $keys_values = substr($keys_values, 0, -2);
        
        return $keys_values;
    }
    
    
    public function FormatDataForInsert($FormArray)
    {
        $keys   = '';
        $values = '';
        foreach ($FormArray as $var => $val) {
            $val = addslashes($val);
            
            $keys   .= "`$var`, ";
            $values .= "'$val', ";
        }
        $keys   = substr($keys, 0, -2);
        $values = substr($values, 0, -2);
        
        return "{$keys}||{$values}";
    }

    public function datefmt($date, $inFormat, $outFormat) 
    {
        /* A function to take a date in ($date) in specified inbound format (eg mm/dd/yy for 12/08/10) and
         * return date in $outFormat (eg yyyymmdd for 20101208)
         *    datefmt (
         *                        string $date - String containing the literal date that will be modified
         *                        string $inFormat - String containing the format $date is in (eg. mm-dd-yyyy)
         *                        string $outFormat - String containing the desired date output, format the same as date()
         *                    )
         *
         *
         *    ToDo:
         *        - Add some error checking and the sort?
         */

        $order = array('mon' => NULL, 'day' => NULL, 'year' => NULL);
       
        for ($i=0; $i<strlen($inFormat);$i++) {
            switch ($inFormat[$i]) {
                case "m":
                    $order['mon'] .= substr($date, $i, 1);
                    break;
                case "d":
                    $order['day'] .= substr($date, $i, 1);
                    break;
                case "y":
                    $order['year'] .= substr($date, $i, 1);
                    break;
            }
        }
       
        $unixtime = mktime(0, 0, 0, $order['mon'], $order['day'], $order['year']);
        $outDate = date($outFormat, $unixtime);

        if ($outDate == False) {
            return False;
        } else {
            return $outDate;
        }
    }
    
    public function convertDateTimeToJS($datetime)
    {
        # INPUT => 2010-10-28 21:30:00
        # OUTPUT => year,month,day,hours,minutes,seconds
        #
        # NOTE: Must remove any leading zeros or javascript will throw an 'octal' error
        #
        
        $parts = explode(' ', $datetime);
        $date = $parts[0];
        $time = $parts[1];
        
        $d_parts = explode('-', $date);
        $t_parts = explode(':', $time);
        
        #echo ArrayToStr($d_parts);
        #echo ArrayToStr($t_parts);
        
        $yr = $d_parts[0];
        #$mo = $this->RemoveFirstChar(($d_parts[1] - 1),'0');
        $mo = $this->RemoveFirstChar(($d_parts[1] ),'0');
        $mo = $mo - 1; // Shift the day back by one number
        
        $da = $this->RemoveFirstChar($d_parts[2],'0');
        $h  = $this->RemoveFirstChar($t_parts[0],'0');
        $m  = $this->RemoveFirstChar($t_parts[1],'0');
        $s  = $this->RemoveFirstChar($t_parts[2],'0');
        
        $output = "$yr,$mo,$da,$h,$m,$s";
        return $output;
    }
    
    public function CheckLastChar($string='', $char='')
    {
        $len        = strlen($string - 1);
        $last_char  = substr($string, $len, 1);
        $return     = ($last_char == $char) ? true : false;

        return $return;
    }
    
    public function RemoveLastChar($string='', $char='')
    {
        if ($this->CheckLastChar($string, $char)) {
            $string = substr($string, 0, -1);
        }
        return $string;
    }
    
    public function CheckFirstChar($string='', $char='')
    {
        $first_char = substr($string, 0, 1);
        $return     = ($first_char == $char) ? true : false;

        return $return;
    }
    
    public function RemoveFirstChar($string='', $char='')
    {
        if ($this->CheckFirstChar($string, $char)) {
            $string = substr($string, 1);
        }
        return $string;
    }
    
    
    public function CreateClientListingByAlphabet($link_location_class='', $type='customers', $action='link', $pagetitle='')
    {
        return $this->CreateListingByAlphabet($link_location_class, $type, $action, $pagetitle);
    }
    
    public function CreateListingByAlphabet($link_location_class='', $type='', $action='link')
    {
        # ===============================================================================
        # FUNCTION :: Will get all users in the system and output an alphabatized list.
        # ===============================================================================
        
        switch($type) {
            case 'customer':
            case 'customers':
                $where = " AND `type_customer`=1";
                $type = 'Customer';
            break;
            
            case 'instructor':
            case 'instructors':
                $where = " AND `type_instructor`=1";
                $type = 'Instructor';
            break;
            
            case 'administrator':
            case 'administrators':
                $where = " AND `type_administrator`=1";
                $type = 'Administrator';
            break;
            
            default:
                $where = "";
                $type = '';
            break;
        }
        
        $records = $this->SQL->GetArrayAll(array(
            'table' => 'contacts',
            'keys'  => 'first_name, last_name, email_address, wh_id, active',
            'where' => "active!=9 $where",
            'order' => 'last_name ASC',
        ));
        
        
        $output_active          = '<div style="min-width:400px;"><h2>ACTIVE</h2></div>';
        $output_inactive        = '<div style="min-width:400px;"><h2>INACTIVE</h2></div>';
        $initial_last_active    = '';
        $initial_last_inactive  = '';
        
        foreach ($records as $record) {
            $output         = '';
            $active         = $record['active'];
            $pagetitle      = "{$record['last_name']}, {$record['first_name']}";
            $initial_curr   = substr($record['last_name'], 0, 1);
            
            
            if ($active) {
                if ($initial_curr != $initial_last_active) {
                    $output .= "<div style='border-bottom:1px solid #000; font-size:16px; padding-top:20px;'>$initial_curr</div>";
                    $initial_last_active = $initial_curr;
                }
            } else {
                if ($initial_curr != $initial_last_inactive) {
                    $output .= "<div style='border-bottom:1px solid #000; font-size:16px; padding-top:20px;'>$initial_curr</div>";
                    $initial_last_inactive = $initial_curr;
                }
            }
            
            
            switch ($action) {
                case 'window':
                    $link       = getClassExecuteLinkNoAjax(EncryptQuery("class={$link_location_class};v1={$record['wh_id']}"));                    
                    $link      .= ";pagetitle={$type}: {$pagetitle}";
                    $script     = "top.parent.appformCreate('Window', '{$link}', 'apps'); return false;";
                    $output    .= "<div style='font-size:11px;'><a href='#' onclick=\"{$script}\" style='text-decoration:none;'>{$record['last_name']}, {$record['first_name']} ({$record['email_address']}) [#{$record['wh_id']}]</a></div>";
                break;
                
                case 'blank':
                default:
                    $link       = getClassExecuteLinkNoAjax(EncryptQuery("class={$link_location_class};v1={$record['wh_id']}"));
                    $link      .= ";pagetitle={$type}: {$pagetitle}";
                    $output    .= "<div style='font-size:11px;'><a href='$link' target='_blank' style='text-decoration:none;'>{$record['last_name']}, {$record['first_name']} ({$record['email_address']}) [#{$record['wh_id']}]</a></div>";
                break;
                
                case 'link':
                default:
                    $link       = getClassExecuteLinkNoAjax(EncryptQuery("class={$link_location_class};v1={$record['wh_id']}"));
                    $link      .= ";pagetitle={$type}: {$pagetitle}";
                    $output    .= "<div style='font-size:11px;'><a href='$link' style='text-decoration:none;'>{$record['last_name']}, {$record['first_name']} ({$record['email_address']}) [#{$record['wh_id']}]</a></div>";
                break;
            }
            
            
            if ($active) {
                $output_active .= $output;
            } else {
                $output_inactive .= $output;
            }
            
        }
        
        
        # PUT ALL CONTENTS INTO TABS
        # =====================================================
        $OBJ_TABS = new Tabs('tab_CreateListingByAlphabet', 'tab_edit');
        $OBJ_TABS->AddTab('Active', "<div id=\"admin_command_test_prcedures\">$output_active</div>");
        $OBJ_TABS->AddTab('Inactive', "<div id=\"admin_command_test_prcedures\">$output_inactive</div>");
        $tab_content = $OBJ_TABS->OutputTabs(true);
        
        return $tab_content;
    }

}