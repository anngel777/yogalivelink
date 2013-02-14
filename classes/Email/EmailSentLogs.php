<?php
class Email_EmailSentLogs extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();
        
        $this->ClassInfo = array(
            'Created By'  => 'Richard Witherspoon',
            'Created'     => '2011-01-01',
            'Updated By'  => '',
            'Created'     => '',
            'Version'     => '1.0',
            'Description' => 'Manage email sent logs in back-office -> tracks all emails sent by system - if logged',
        );

        $this->Table                = 'email_sent_logs';
        $this->Add_Submit_Name      = 'EMAIL_SENT_LOGS_SUBMIT_ADD';
        $this->Edit_Submit_Name     = 'EMAIL_SENT_LOGS_SUBMIT_EDIT';
        $this->Index_Name           = 'email_sent_logs_id';
        $this->Flash_Field          = 'email_sent_logs_id';
        $this->Default_Where        = '';  // additional search conditions
        $this->Default_Sort         = 'email_sent_logs_id';  // field for default table sort
        $this->Default_Fields       = 'email_complete_blob,wh_id,email_sent_datetime,email_bounced_datetime,user_unsubscribed_datetime,user_opened_datetime,user_responded_datetime';
        $this->Unique_Fields        = '';

        $this->Field_Titles = array(
            'email_sent_logs_id'            => 'Email Sent Logs Id',
            'email_q_send_id'               => 'Email Q Send Id',
            'email_setups_id'               => 'Email Setups Id',
            'email_complete_blob'           => 'Email Complete Blob',
            'wh_id'                         => 'Wh Id',
            'email_sent_datetime'           => 'Email Sent Datetime',
            'active'                        => 'Active',
            'updated'                       => 'Updated',
            'created'                       => 'Created'
        );

        
    $this->Edit_Links_Count  = '3';
    $this->Add_Link = '';

    $this->Edit_Links  = qqn("
        <td align=`center`><a href=`#` class=`row_view`   title=`View`   onclick=`tableViewClick('@IDX@','@VALUE@','@EQ@'); return false;`></a></td>
        <td align=`center`><a href=`#` class=`row_edit`   title=`Edit`   onclick=`tableEditClick('@IDX@','@VALUE@','@EQ@'); return false;`></a></td>
        <td align=`center`></td>");

        
        
        
    } // -------------- END __construct --------------


    public function SetFormArrays()
    {
        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Email Q Send Id|email_q_send_id|N|11|11',
            'text|Email Setups Id|email_setups_id|N|11|11',
            'text|Email Complete Blob|email_complete_blob|N||',
            'text|Wh Id|wh_id|N|11|11',
            'text|Sent Datetime|sent_datetime|N||',
            'text|Email Sent Datetime|email_sent_datetime|N||',
            'text|Email Bounced Datetime|email_bounced_datetime|N||',
            'text|User Unsubscribed Datetime|user_unsubscribed_datetime|N||',
            'text|User Opened Datetime|user_opened_datetime|N||',
            'text|User Responded Datetime|user_responded_datetime|N||',
        );

        if ($this->Action == 'ADD') {
            $base_array[] = "submit|Add Record|$this->Add_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Add = $base_array;
        } else {
            $base_array[] = 'checkbox|Active|active||1|0';
            $base_array[] = "submit|Update Record|$this->Edit_Submit_Name";
            $base_array[] = 'endform';
            $this->Form_Data_Array_Edit = $base_array;
        }
    }
    
    
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        # ============ WHEN VIEWING A TABLE ============
        
        parent::ProcessTableCell($field, $value, $td_options, $id);

        switch ($field) {
            case 'email_complete_blob':
                $email_complete_blob = unserialize($value);
                
                $value = "
                <div style='min-width:250px;'>
                    <b>Subject:</b> {$email_complete_blob['subject']}<br />
                    <b>To Name:</b> {$email_complete_blob['to_name']}<br />
                    <b>To Email:</b> {$email_complete_blob['to_email']}
                </div>
                ";
                
                //$value = TruncStr(strip_tags($value), 100);
            break;
            
            default:
            #global $CLASS_EXECUTE_LINK;
                $CLASS_EXECUTE_LINK     = '/office/class_execute';
                $eq                     = EncryptQuery("class=Email_EmailResend;v1={$id};");
                $link                   = $CLASS_EXECUTE_LINK . '?eq=' . $eq;
                $script                 = "top.parent.appformCreate('Window', '{$link}', 'apps'); return false;";
                #$output .= "<div class='btn_actions'><a href='#' onclick=\"{$script}\">resend</a></div>";
                
                $this->Edit_Links  = qqn("
                    <td align=`center`><a href=`#` class=`row_view`   title=`View`              onclick=`tableViewClick('@IDX@','@VALUE@','@EQ@'); return false;`></a></td>
                    <td align=`center`><a href=`#` class=`row_edit`   title=`Edit`              onclick=`tableEditClick('@IDX@','@VALUE@','@EQ@'); return false;`></a></td>
                    <td align=`center`><a href=`#` class=`row_mail`   title=`Resend Message`    onclick=`{$script}; return false;`></a></td>");
            break;
        }
    }
    
    public function ProcessRecordCell($field, &$value, &$td_options)
    {
        # ============ VIEWING / EDITING A RECORD ============
        
        switch ($field) {
            case 'description':
            case 'notes':
                $value = nl2br($value);
            break;
        }
    }
    
    
    
    
    
    /*
    public function OutputTable($search_array, $num_rows, $start_row, $row_count, $rows_only=false, $primary_sort_order='', $primary_sort_direction='')
    {
        global $THIS_PAGE_QUERY;

        $RESULT = '';


        if (!empty($search_array)) {

            $output_table = parent::OutputTable($search_array, $num_rows, $start_row, $row_count, $rows_only, $primary_sort_order, $primary_sort_direction);


            if ($rows_only) {
                $colcount = count($search_array[0]) + $this->Edit_Links_Count + 1;

                $query_where = trim(TextBetween('WHERE', 'LIMIT', $this->SQL->Db_Last_Query));
                $query_count = $num_rows; //db_count($this->Table, $query_where);

                $eq = EncryptQuery("resend_query=$query_where");

                $link = qqn("
                <a href=`#` class=`stdbuttoni`
                onclick=`top.parent.appformCreate(
                'Email Store Resend', 'email_store;eq=$eq', 'apps'); return false;`>Resend Messages (Count: $query_count)</a>");
                $RESULT = "\n<tr><td colspan=\"$colcount\" style=\"padding:10px;\">$link</td></tr>\n";

                $RESULT = "$RESULT$output_table";
            } else {
            //    $RESULT = mb_str_replace("<tbody>\n", "<tbody>\n$RESULT", $output_table);
                $RESULT = $output_table;
            }
        }
        return $RESULT;
    }
    */





}  // -------------- END CLASS --------------