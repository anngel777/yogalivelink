<?php

// FILE: class.Contacts.php

class Contacts extends BaseClass
{

    public $Companies_Id = '';

    public function  __construct()
    {
        parent::__construct();

        $this->ClassInfo = array(
            'Created By'  => 'MVP',
            'Description' => 'Create and manage contacts',
            'Created'     => '2009-10-23',
            'Updated'     => '2009-10-23'
        );

        //-------------- SET PARAMETERS -------------
        $this->SetParameters(func_get_args());
        $this->Companies_Id = $this->GetParameter(0);

        if ($this->Companies_Id) {
            $this->Default_Values['companies_id'] = $this->Companies_Id;
        }

        // if (function_exists('addmessage')) {
            // AddMessage('Companies Id=' . $this->Companies_Id);
        // }

        if ($this->Companies_Id) {
            $this->AddDefaultWhere("`contacts`.`companies_id`=$this->Companies_Id");
        }

        $this->Table  = 'contacts';

        $this->Add_Submit_Name  = 'CONTACTS_SUBMIT_ADD';
        $this->Edit_Submit_Name = 'CONTACTS_SUBMIT_EDIT';

        $this->Index_Name = 'contacts_id';

        $this->Flash_Field = 'contacts_id';

        $this->Default_Sort  = 'company_name';  // field for default table sort

        $this->Field_Titles = array(
            'contacts_id' => 'Contacts Id',
            'companies.company_name' => 'Company',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'middle_name' => 'Middle Name',
            'title' => 'Title',
            'contacts.address_1' => 'Address 1',
            'contacts.address_2' => 'Address 2',
            'contacts.address_3' => 'Address 3',
            'contacts.city' => 'City',
            'contacts.state' => 'State',
            'countries.country_name' => 'Country',
            'contacts.postal_code' => 'Postal Code',
            'contacts.phone_number' => 'Phone Number',
            'contacts.cell_number' => 'Cell Number',
            'contacts.fax_number' => 'Fax Number',
            'contacts.email_address' => 'Email Address',
            'contacts.gender' => 'Gender',
            "IF(`contacts`.`salesperson`=1, 'Yes', 'No') AS SALESPERSON" => 'Salesperson',
            'contacts.commission_rate' => 'Commission Rate',
            'contacts.type' => 'Type',
            'contact_comments' => 'Contact Comments',
            'contacts.notes' => 'Notes',
            'contacts.active' => 'Active',
            'contacts.updated' => 'Updated',
            'contacts.created' => 'Created'
        );

        $this->Join_Array = array(
            'countries' => 'LEFT JOIN countries ON countries.country_code = contacts.country_code',
            'companies' => 'LEFT JOIN companies ON companies.companies_id = contacts.companies_id'
        );


        $this->Default_Fields = 'company_name,first_name,last_name,city,state,country_code,postal_code';

        $this->Unique_Fields = '';

    } // -------------- END __construct --------------

    public function ProcessAjax()
    {
        global $FORM_STATE_CHAR_CODES;
        $action = Get('action');
        switch ($action) {
            case 'address':
                $companies_id = intOnly(Get('companies_id'));
                if ($companies_id) {
                    $address = $this->SQL->GetRecord(array(
                        'table' => 'companies',
                        'keys'  => 'address_1,address_2,city,state,country_code,postal_code,phone_number,fax_number',
                        'where' => "companies_id=$companies_id",
                    ));
                    $country_code = $address['country_code'];
                    if ($country_code == 'US') {
                        $address['US_STATES_state'] = $address['state'];
                    } elseif ($country_code == 'CA') {
                        $address['CANADA_PROVINCES_state'] = $address['state'];
                    } elseif ($country_code != '') {
                        $address['OTHER_STATES_state'] = $address['state'];
                    }
                    echo json_encode($address);
                }

            break;
        }
        exit;
    }

    public function SetFormArrays()
    {

        $eq_company = EncryptQuery($this->Ac_Company);

        $base_array = array(
            "form|$this->Action_Link|post|db_edit_form",
            "js|
                function setCompanyAddress() {
                    var companies_id = $('#FORM_companies_id').val();
                    if (companies_id) {
                        $('#addressinfo .formitem').addClass('formitem_loading');
                        $.getJSON('@@AJAXLINK@@&action=address&companies_id=' + companies_id, '', function(data) {
                            if (data) {
                                for(var name in data) {
                                    $('#FORM_' + name).val(data[name]);
                                }
                                formCountryState('country_code', 'state');
                            }
                            $('#addressinfo .formitem').removeClass('formitem_loading');
                        });
                    }
                }
            ",

            "autocomplete|Company|companies_id|N|55|80||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_company|||" . $this->GetAddCompanyLink(),

            "text|First Name|first_name|N|60|100",
            "text|Last Name|last_name|N|60|100",
            "text|Middle Name|middle_name|N|60|100",
            "text|Title|title|N|60|100",

            'info||<a href="#" class="stdbuttoni" onclick="setCompanyAddress(); return false;">Use Company Address</a>',
            'code|<div id="addressinfo">',
            "text|Address 1|address_1|N|60|100",
            "text|Address 2|address_2|N|60|100",
            "text|Address 3|address_3|N|60|100",
            "text|City|city|N|60|100",
            "countrystate|Country|country_code:state|N",
            "text|Postal Code|postal_code|N|10|20",
            "text|Phone Number|phone_number|N|20|20",
            "text|Cell Number|cell_number|N|20|20",
            "text|Fax Number|fax_number|N|20|20",
            'code|</div>',
            "email|Email Address|email_address|N|60|100",
            "radioh|Gender|gender|N||Male|Female",
            "checkbox|Salesperson|salesperson||1|0",
            "text|Commission Rate|commission_rate|N|5|5",
            //"text|Type|type|N|1|1",
            "textarea|Contact Comments|contact_comments|N|60|4",
            "textarea|Notes|notes|N|60|4",
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

}  // -------------- END CLASS --------------