<?php

class Tabs
{
    public $Tab_Spacer;
    public $Tab_Folder_Start;
    public $Tab_Folder_End;
    public $Tab_Group_Name;
	public $Tab_Name;
    public $Tab_Menu;
    public $Tab_Date;
    public $Tab_Count;
    public $Tab_Set_Function_Name;
    

    public function  __construct($tab_name = 'tab')
    {
		$this->Tab_Count = 0; 
		$this->Tab_Spacer       = "<div class=\"tabspacer\">&nbsp;</div>\n";
        $this->Tab_Folder_Start = "<div class=\"tabfolder\">\n";
        $this->Tab_Folder_End   = "</div>\n";
        $this->Tab_Group_Name   = $tab_name;
        $this->Tab_Class_Name   = 'tablink';
        $this->Tab_Selected_Class_Name = 'tabselect';
        $this->Tab_Set_Function_Name   = 'setTab';
        
		$this->Tab_Name = '';
		$this->Tab_Menu = '';
		$this->Tab_Data = '';
    }

public function SetTabsetName($NAME)
{
	$this->Tab_Group_Name  = $NAME;
}

public function AddTab($TITLE, $DATA)
{
	#CREATE THE TAB ID
    if (!$DATA) return;  // abort if no data
    
	$this->TabCountAdd();
    $count              = $this->Tab_Count;
    $group_name         = $this->Tab_Group_Name;
    $tab_class          = $this->Tab_Class_Name;
    $tab_selected_class = $this->Tab_Selected_Class_Name;
    $set_tab_function   = $this->Tab_Set_Function_Name;
    
	$table_link_id = $group_name . 'link'. $count;
	$table_data_id = $group_name . $count;
	
	if ($this->Tab_Count == 1) {
		#FIRST TAB
        //setTab(num, group, tablink, tabselect)
		$this->Tab_Menu .= 
            "<a id='$table_link_id' class='tabselect' href='#' 
                  onclick=\"$set_tab_function($count, '$group_name', '$tab_class', '$tab_selected_class'); return false;\">$TITLE</a>\n";
		$this->Tab_Data .= 
            "<div id=\"$table_data_id\" style=\"display:block;\">\n$DATA\n</div>";
	} else {
		$this->Tab_Menu .= 
            "<a id='$table_link_id' class='tablink' href='#'
                onclick=\"$set_tab_function($count, '$group_name', '$tab_class', '$tab_selected_class'); return false;\">$TITLE</a>\n";
		$this->Tab_Data .= 
            "<div id=\"$table_data_id\" style=\"display:none;\">\n$DATA\n</div>\n";
	}


}


private function TabCountAdd()
{
	$this->Tab_Count++;
}


public function OutputTabs($return=false)
{
    if ($return) {
        $output = '';
        $output .= $this->Tab_Menu;
        $output .= $this->Tab_Spacer;
        $output .= $this->Tab_Folder_Start;
        $output .= $this->Tab_Data;
        $output .= $this->Tab_Folder_End;
        return $output;
    } else {
        echo $this->Tab_Menu;
        echo $this->Tab_Spacer;
        echo $this->Tab_Folder_Start;
        echo $this->Tab_Data;
        echo $this->Tab_Folder_End;
    }
}


}