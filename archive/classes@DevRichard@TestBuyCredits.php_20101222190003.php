<?php
class DevRichard_TestBuyCredits extends BaseClass
{
    
    public $show_query = true;
    public $sessions_id = 666;
    public $Table_Credits = 'credits';
    public $WH_ID = 1000000001;
    
    
    public function  __construct()
    {
        parent::__construct();
    }
    
    

    
    
    
    public function Execute()
    {
        global $PAGE;
        
        
        $product_id = 6; // 1 credit
        
        
        # INITIALIZE THE CLASS
        # ==============================================================================
        $SO = new Store_YogaStoreOrder();
        $SO->Default_Return_Page = $PAGE['pagelinkquery'];
        
        
        # ADD ITEM TO SHOPPING CART
        # ==============================================================================
        $SO->Ajax_Cart = false;
        $SO->Use_Cart = true;                   // Set this so you can add a product
        $SO->AddToCart($product_id);            // Add item to the cart
        
        
        # BEGIN CHECKOUT PROCESS
        # ==============================================================================
        //$_POST['CHECKOUT'];                     // Set variable so cart goes to correct page
        
        
        $SO->Buyer_Form_Data = array(
            'text|First Name|first_name|Y|30|40',
            'text|Last Name|last_name|Y|30|40',
            'email|Email|email_address|Y|30|80',
        );
        
        
        
        $SO->Use_Cart = false;                  // Set this so you can start checkout process
        $SO->ProcessOrderPage();                // Show the shopping cart
        //echo $SO->GetBillingInfo();
        
        
        
        
        
        
        
    }
    
    
}