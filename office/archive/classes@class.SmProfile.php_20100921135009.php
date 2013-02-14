<?php
class SmProfile
{
    private $Log_Directory = 'abcd/logs';

/*  ----------- example SM Profile Data
addr1 =
addr2 =
addr3 =
addr4 =
alldisti-lar-rol =
alldisti-lar-status =
ccap-role =
ccap-status =
ccdp-role =
ccdp-status =
city =
contact-type =
cst-id = 100002331624
ctry =
email = michael@mailwh.com
fax =
first-name = Michael
geo-ovrd =
ipd-bs-role =
ipd-bs-status =
ipd-nbs-role =
ipd-nbs-status =
ipp-role =
ipp-status =
lang =
last-name = Petrovich
login-id = michael@mailwh.com
misc = generic_affiliate, michael@mailwh.com
org-id = 0
org-name =
peg-role =
peg-status =
phone =
state =
zip =
*/


    public function WhidFromSmProfile()
    {
        global $ROOT;
        $SMPROFILE = Session('SMPROFILE');

        if ($SMPROFILE) {
            //$intel_customer_id = intOnly(ArrayValue($SMPROFILE, 'cst-id'));
            $intel_login_id = db_QuoteValue(ArrayValue($SMPROFILE, 'login-id'));

            if (!ArrayValue($SMPROFILE, 'login-id')) {
                return 0;
            }

            //$WHID = db_GetValue('contacts', 'wh_id', "intel_customer_id=$intel_customer_id");
            $WHID = db_GetValue('contacts', 'wh_id', "intel_login_id=$intel_login_id");
            if ($WHID) {
                $_SESSION['WHID'] = $WHID;
                $CONTACT = new Contacts;
                $_SESSION['WH_CONTACT_RECORD'] = $CONTACT->GetAllContactDetails($WHID);
            } else {
                
                $result = $this->CreateNewContactFromSmProfile();
                
                if (!$result) {                    
                    // -------------- write log file ---------------
                    $date = date('Y-m-d H:i:s');
                    $fdate = date('Y-m');
                    $filename = "$ROOT/{$this->Log_Directory}/SMFAIL-$fdate.log";
                    $profile = implode('|', $SMPROFILE);
                    $line = "$date|$profile\n";
                    append_file($filename, $line);
                } else {
                    $WHID = $_SESSION['WHID'];
                }
            }
            return $WHID;
        } else {
            return 0;
        }
    }


    public function CreateNewContactFromSmProfile()
    {
        global $DB_LAST_INSERT_ID;
        $SMP = Session('SMPROFILE');
        
        if(!$SMP) {
            return false;
        }

        $primary_cotact = (ArrayValue($SMP, 'contact-type') == 'PRIM_CONT')? 1 : 0;
        $first_name = ArrayValue($SMP, 'first-name');
        $last_name  = ArrayValue($SMP, 'last-name');

        //------ check if company exists, else create it ------
        $intel_account_id = intOnly(ArrayValue($SMP, 'org-id'));

        if ($intel_account_id) {
        
            $wh_cid = db_GetValue('companies', 'wh_cid', "intel_account_id=$intel_account_id");

            if (!$wh_cid) {
                // need to create a company record
                $keys   =  "intel_account_id, company_name,
                            address_1, address_2, address_3,
                            city, state, country_code,
                            postal_code, phone_number, fax_number,
                            created";
                $values =  db_Values( array( $intel_account_id, ArrayValue($SMP, 'org-name'),
                            ArrayValue($SMP, 'addr1'), ArrayValue($SMP, 'addr2'), ArrayValue($SMP, 'addr3'),
                            ArrayValue($SMP, 'city'), ArrayValue($SMP, 'state'), ArrayValue($SMP, 'ctry'),
                            ArrayValue($SMP, 'zip'), ArrayValue($SMP, 'phone'), ArrayValue($SMP, 'fax'),
                            'NOW()'
                           ));

                if (db_AddRecord('companies', $keys, $values)) {
                    $wh_cid = db_GetValue('companies', 'wh_cid', "companies_id=$DB_LAST_INSERT_ID");
                } else {
                    $wh_cid = 0;
                }
            }
            
        } else {
            $wh_cid = 0;
        }

        $record = array(
            'wh_cid'               =>  $wh_cid,
            'intel_account_id'     =>  $intel_account_id,
            'first_name'           =>  $first_name,
            'last_name'            =>  $last_name,
            'name_badge'           =>  trim("$first_name $last_name"),
            'address_1'            =>  ArrayValue($SMP, 'addr1'),
            'address_2'            =>  ArrayValue($SMP, 'addr2'),
            'address_3'            =>  ArrayValue($SMP, 'addr3'),
            'city'                 =>  ArrayValue($SMP, 'city'),
            'state'                =>  ArrayValue($SMP, 'state'),
            'country_code'         =>  ArrayValue($SMP, 'ctry'),
            'postal_code'          =>  ArrayValue($SMP, 'zip'),
            'phone_number'         =>  ArrayValue($SMP, 'phone'),
            'fax_number'           =>  ArrayValue($SMP, 'fax'),
            'email_address'        =>  ArrayValue($SMP, 'email'),
            'intel_customer_id'    =>  ArrayValue($SMP, 'cst-id'),
            'intel_login_id'       =>  ArrayValue($SMP, 'login-id'),
            'language_code'        =>  ArrayValue($SMP, 'lang'),
            'intel_employee_flag'  =>  0,
            'test_account_flag'    =>  0,
            'primary_contact'      =>  $primary_cotact,
            'new_contact_association_status'  =>  'SM PROFILE',
            'created'              =>  'NOW()'
        );
        
        $keys   = db_Keys($record);
        $values = db_Values($record);
        $RESULT = db_AddRecord('contacts', $keys, $values);
        
        if ($RESULT) {        
            $WHID    = db_GetValue('contacts', 'wh_id', "contacts_id=$DB_LAST_INSERT_ID");        
            $_SESSION['WHID'] = $WHID;
            $CONTACT = new Contacts;
            $_SESSION['WH_CONTACT_RECORD'] = $CONTACT->GetAllContactDetails($WHID);
        }
        
        return $RESULT;
    }

    public function SetSmProfileFromWhid($WHID)
    {
        $WHID = intOnly($WHID);

        if (isset($_SESSION['SMPROFILE'])) unset($_SESSION['SMPROFILE']);

        if (!$WHID) {
            return;
        }

        $_SESSION['WHID'] = $WHID;

        $CONTACT = new Contacts;

        if (Session('WH_CONTACT_RECORD')) {
            $record  = Session('WH_CONTACT_RECORD');
        } else {
            $record  = $CONTACT->GetAllContactDetails($WHID);
            if ($record) {
                $_SESSION['WH_CONTACT_RECORD'] = $CONTACT->GetAllContactDetails($WHID);
            }
        }

        if ($record) {
            $SMPROFILE_ARRAY = array();
            $SMPROFILE_ARRAY['first-name'] = $record['first_name'];
            $SMPROFILE_ARRAY['last-name']  = $record['last_name'];
            $SMPROFILE_ARRAY['email']      = $record['email_address'];
            $SMPROFILE_ARRAY['org-name']   = $record['company_name'];
            $SMPROFILE_ARRAY['org-id']     = $record['intel_account_id'];
            $SMPROFILE_ARRAY['lang']       = $record['language_code'];
            $SMPROFILE_ARRAY['addr1']      = $record['address_1'];
            $SMPROFILE_ARRAY['addr2']      = $record['address_2'];
            $SMPROFILE_ARRAY['city']       = $record['city'];
            $SMPROFILE_ARRAY['state']      = $record['state'];
            $SMPROFILE_ARRAY['ctry']       = $record['country_code'];
            $SMPROFILE_ARRAY['zip']        = $record['postal_code'];
            $SMPROFILE_ARRAY['phone']      = $record['phone_number'];
            $SMPROFILE_ARRAY['fax']        = $record['fax_number'];
            $SMPROFILE_ARRAY['login-id']   = ''; //$record['wh_id'];
            $SMPROFILE_ARRAY['wh_id']      = $record['wh_id'];
            $SMPROFILE_ARRAY['wh_cid']     = $record['wh_cid'];

            $_SESSION['SMPROFILE'] = $SMPROFILE_ARRAY;
        }
    }

    public function LogoutSmProfile()
    {
        setcookie('SMIDENTITY', '', time() - 3600, '/', '.intel.com');
        setcookie('SMPROFILE', '', time() - 3600, '/', '.intel.com');
        setcookie('SMSESSION', '', time() - 3600, '/', '.intel.com');

        if (isset($_SESSION['SMPROFILE'])) unset($_SESSION['SMPROFILE']);

        // ---------- other system logout -------------

        if (isset($_SESSION['WHID'])) unset($_SESSION['WHID']);

        if (isset($_SESSION['ATTENDEE_ID'])) unset($_SESSION['ATTENDEE_ID']);
        if (isset($_SESSION['ATTENDEE_LOGIN_OK'])) unset($_SESSION['ATTENDEE_LOGIN_OK']);
        if (isset($_SESSION['ATTENDEE_REG_INFO'])) unset($_SESSION['ATTENDEE_REG_INFO']);
        if (isset($_SESSION['CUSTOMER_ID'])) unset($_SESSION['CUSTOMER_ID']);
        if (isset($_SESSION['ADMIT_REGISTRATION'])) unset($_SESSION['ADMIT_REGISTRATION']);
        if (isset($_SESSION['WH_CONTACT_RECORD'])) unset($_SESSION['WH_CONTACT_RECORD']);
    }

    public function WhidFromIntelLoginId($LOGINID='')
    {
        if ($LOGINID) {
            $WHID = db_GetValue('contacts', 'wh_id', "intel_login_id='$LOGINID'");
            if ($WHID) {
                $_SESSION['WHID'] = $WHID;
                $CONTACT = new Contacts;
                $_SESSION['WH_CONTACT_RECORD'] = $CONTACT->GetAllContactDetails($WHID);
            }
            return $WHID;
        } else {
            return 0;
        }
    }


} // ------------- end class -------------