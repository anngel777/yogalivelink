<?php

// FILE: /Lib/Lib_AdminUsers.php

class AdminUsers extends Lib_AdminUsers
{

    public function  __construct()
    {
        parent::__construct();
        $this->Password_Size_Options  = '6,40|autocomplete="off"';
        $this->Field_Titles['contacts_id'] = 'Contact ID';

    } // -------------- END __construct --------------


    public function ProcessAjax()
    {

        $action = Get('action');
        switch ($action) {
            case 'contact':
                $contacts_id = intOnly(Get('contacts_id'));

                if ($contacts_id) {
                    $info = $this->SQL->GetRecord(array(
                        'table' => 'contacts',
                        'keys'  => 'email_address,company_name,first_name,last_name',
                        'where' => "contacts_id=$contacts_id",
                        'joins' => 'LEFT JOIN companies ON companies.companies_id=contacts.companies_id'
                    ));

                    echo json_encode($info);
                }

            break;
        }
        exit;
    }


    public function SetFormArrays()
    {
        parent::SetFormArrays();
        if ($this->Updating_Profile) {
            return;
        }

        $BC = new BaseClass;
        /*
        $eq_contact = EncryptQuery($BC->Ac_Contact);
        
        $contact = array(
            "autocomplete|Contact|contacts_id|N|60|80||addAutoCompleteFunctionality|$this->Auto_Complete_Helper?eq=$eq_contact",
            "js|
                function setContact() {
                    var contacts_id = $('#FORM_contacts_id').val();

                    if (contacts_id) {
                        $('#FORM_first_name').addClass('formitem_loading');
                        $('#FORM_last_name').addClass('formitem_loading');
                        $('#FORM_company_name').addClass('formitem_loading');
                        $('#FORM_email_address').addClass('formitem_loading');
                        $.getJSON('@@AJAXLINK@@&action=contact&contacts_id=' + contacts_id, '', function(data) {
                            if (data) {
                                for(var name in data) {
                                    $('#FORM_' + name).val(data[name]);
                                }
                            }
                            $('#FORM_first_name').removeClass('formitem_loading');
                            $('#FORM_last_name').removeClass('formitem_loading');
                            $('#FORM_company_name').removeClass('formitem_loading');
                            $('#FORM_email_address').removeClass('formitem_loading');
                        });
                    }
                }
            ",
            'info||<a href="#" class="stdbuttoni" onclick="setContact(); return false;">Use Contact Info</a>'
        );
        */

        if ($this->Action == 'ADD') {
            $array = explode('|' . chr(27), $this->Form_Data_Array_Add);
            //array_splice($array, 1, 0, $contact);
            $this->Form_Data_Array_Add = $array;
        } else {
            $array  = explode('|' . chr(27), $this->Form_Data_Array_Edit);
            //array_splice($array, 1, 0, $contact);
            $this->Form_Data_Array_Edit = $array;
        }



    }


}  // -------------- END CLASS --------------





