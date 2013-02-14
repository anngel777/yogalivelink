<?php
class DevRichard_TestBuyCredits //extends BaseClass
{
    
    public $show_query = true;
    public $sessions_id = 666;
    public $Table_Credits = 'credits';
    public $WH_ID = 1000000001;
    
    
    public function  __construct()
    {
        //parent::__construct();
    }
    

    
    public function Execute()
    {
        global $PAGE;
   
        $product_id = 6; // 1 credit
        
        
        # INITIALIZE THE CLASS
        # ==============================================================================
        $SO = new Store_YogaStoreCreditOrder();
       
        
        # SET SHOPPING CART WITH ITEM ONLY
        # ==============================================================================
 
        $SO->SetCart($product_id);        
        echo $SO->ProcessOrderPage();  
    }
   
}