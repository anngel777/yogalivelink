<?php

// FILE: class.Companies.php

class Companies extends BaseClass
{
    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => '',
            'Description' => 'Create and manage companies',
            'Created'     => '2009-10-23',
            'Updated'     => '2009-10-23'
        );

        $this->Table  = 'companies';

        $this->Add_Submit_Name  = 'COMPANIES_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'COMPANIES_SUBMIT_EDIT';

        $this->Index_Name = 'companies_id';

        $this->Flash_Field = 'companies_id';

        $this->Default_Where = '';  // additional search conditions

        $this->Default_Sort  = 'companies_id';  // field for default table sort

        $this->Field_Titles = array(
            'companies.companies_id' => 'ID',
            'companies.company_name' => 'Company Name',
            'companies.dba' => 'Dba',
            'companies.aliases' => 'Aliases',
            'companies.business_type' => 'Business Type',
            'companies.business_class' => 'Business Class',

            'principal' => 'Principal',
            'customer' => 'Customer',
            'manufacturer' => 'Manufacturer',
            'distributor' => 'Distributor',

            'companies.end_product' => 'End Product',

            "(SELECT GROUP_CONCAT(DISTINCT(CONCAT('&bull;&nbsp;', CONCAT(`first_name`, ' ', `last_name`))) SEPARATOR '<br />')
FROM `contacts`
WHERE CONCAT(',', `companies`.`salespeople`,',') LIKE CONCAT('%,',`contacts`.`contacts_id`,',%')) AS SALESPEOPLE" => 'Salespeople',
            "(SELECT CONCAT(`first_name`, ' ', `last_name`) FROM `contacts` WHERE `companies`.`primary_salesperson_id`=`contacts`.`contacts_id`) AS PRIMARY_SALESPERSON" => 'Primary Salesperson',

            "(SELECT CONCAT(`first_name`, ' ', `last_name`) FROM `contacts` WHERE `companies`.`primary_contact_id`=`contacts`.`contacts_id`) AS PRIMARY_CONTACT" => 'Primary Contact',
            'companies.address_1' => 'Address 1',
            'companies.address_2' => 'Address 2',
            'companies.address_3' => 'Address 3',
            'companies.city' => 'City',
            'companies.state' => 'State',
            'countries.country_name' => 'Country',
            'companies.postal_code' => 'Postal Code',
            'companies.phone_number' => 'Phone Number',
            'companies.fax_number' => 'Fax Number',
            'companies.email' => 'Email',
            'companies.website' => 'Website',
            '(SELECT COUNT(*) FROM contacts WHERE `contacts`.`companies_id`=`companies`.`companies_id`) AS CONTACT_COUNT' => 'Contact Count',
            '(SELECT COUNT(*) FROM principal_parts WHERE `principal_parts`.`companies_id`=`companies`.`companies_id`) AS PART_COUNT' => 'Part Count',
            'companies.comments' => 'Comments',
            'companies.active' => 'Active',
            'companies.updated' => 'Updated',
            'companies.created' => 'Created'
        );

        $this->Join_Array = array(
            'countries' => 'LEFT JOIN countries ON countries.country_code = companies.country_code'
        );

        $this->Default_Fields = 'company_name,business_type,business_class,end_product,city,state,country';

        $this->Unique_Fields = '';

    } // -------------- END __construct --------------


    public function SetFormArrays()
    {

        $ac_where = "companies_id=$this->Edit_Id";
        $eq_contact = EncryptQuery("ac_table=contacts&ac_key=contacts_id&ac_field=CONCAT(first_name, ' ', last_name, ' - ', city, ', ', state, ' ', phone_number)&ac_where=$ac_where");


        $business_type_list  = Form_ArrayToList($this->SQL->GetFieldValues($this->Table, 'business_type', 'active=1'));
        $business_class_list = Form_ArrayToList($this->SQL->GetFieldValues($this->Table, 'business_class', 'active=1'));
        $sales_people_list = Form_AssocArrayToList($this->SQL->GetAssocArray(array(
            'table' => 'contacts',
            'key'   => 'contacts_id',
            'value' => "CONCAT(first_name, ' ', last_name)",
            'where' => 'active=1 AND salesperson=1',
            'order' => 'last_name'
        )));

        $enabled = $this->Action == 'ADD'? 'disabled style="opacity:0.3;"' : '';

        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            'text|Company Name|company_name|Y|60|100',
            'text|DBA|dba|N|60|100',
            'textarea|Aliases|aliases|N|60|4',
            "selecttext|Business Type|business_type|N|20|80||$business_type_list",
            "selecttext|Business Class|business_class|N|20|80||$business_class_list",
            'checkbox|Principal|principal||Yes|No',
            'checkbox|Customer|customer||Yes|No',
            'checkbox|Manufacturer|manufacturer||Yes|No',
            'checkbox|Distributor|distributor||Yes|No',

            "text|End Product|end_product|N|60|255",


            "autocomplete|Primary Contact|primary_contact_id|N|60|80|$enabled|addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_contact",

            'text|Address 1|address_1|N|60|100',
            'text|Address 2|address_2|N|60|100',
            'text|Address 3|address_3|N|60|100',

            'text|City|city|N|60|100',
            'countrystate|Country|country_code:state|N',

            'text|Postal Code|postal_code|N|10|20',
            'text|Phone Number|phone_number|N|20|20',
            'text|Fax Number|fax_number|N|20|20',
            'email|Email|email|N|60|100',
            'text|Website|website|N|60|100',

            "checkboxlistset|Salespeople|salespeople|N||$sales_people_list",
            qq("info||<a class=`stdbuttoni` href=`#` onclick=`\$('[id^=FORM_salespeople]').attr('checked', true); return false;`>Select All</a>"),
            "Select|Primary Salesperson|primary_salesperson_id|N||$sales_people_list",
            "textarea|Comments|comments|N|60|4",
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

    public function EditRecord($id, $id_field='')  // extends parent
    {
        $TABS = new Lib_Tabs('tab', 'tab_edit');
        $RESULT = parent::EditRecordText($id, $id_field);
        $TABS->AddTab('Company', $RESULT);

        $OBJ = new Contacts($id);
        $TABS->AddTab('Contacts', $OBJ->ListTableText());

        $OBJ = new PrincipalParts($id);
        $TABS->AddTab('Parts', $OBJ->ListTableText());

        $TABS->OutputTabs();
    }

    // ----------- Process a record table cell before it is output when viewing a record  ---------------
    public function ProcessRecordCell($field, &$value, &$td_options)
    {
        if (($field == 'aliases') and $value) {
            $aliases = explode("\n", $value);
            $result = '';
            foreach ($aliases as $alias) {
                $alias = trim($alias);
                if ($alias) {
                    $result .= "&bull; $alias<br />";
                }
            }
            if ($result) {
                $result = substr($result, 0, -6); // remove trailing <br />
                $value = $result;
                $td_options = 'style="white-space:nowrap;"';
            }
        } elseif($field == 'SALESPEOPLE') {
            $td_options = 'style="white-space:nowrap;"';
        }


        return;
    }

    // ----------- Process a record table cell before it is output when viewing a table  ---------------
    public function ProcessTableCell($field, &$value, &$td_options, $id='')
    {
        parent::ProcessTableCell($field, $value, $td_options, $id);
        if (($field == 'aliases') and $value) {
            $aliases = explode("\n", $value);
            $result = '';
            foreach ($aliases as $alias) {
                $alias = trim($alias);
                if ($alias) {
                    $result .= "&bull; $alias<br />";
                }
            }
            if ($result) {
                $result = substr($result, 0, -6); // remove trailing <br />
                $value = $result;
                $td_options = 'style="white-space:nowrap;"';
            }
        } elseif($field == 'SALESPEOPLE') {
            $td_options = 'style="white-space:nowrap;"';
        }
        return;
    }

}  // -------------- END CLASS --------------