<?PHP

// FILE: class.DatabaseSwap.php

class Translation_DatabaseSwap extends BaseClass
{
	public $SwapFromArray;
	public $PreSwapArray;
	
    public function  __construct()
    {
		parent::__construct();
	
		$this->ClassInfo = array(
			'Created By'  => 'Richard Witherspoon',
			'Description' => 'Create and manage language translations',
			'Created'     => '2008-12-08',
			'Updated'     => '2008-12-08'
		);
		
		$this->SwapFromArray = '';
		$this->PreSwapArray = '';

	} #END CONSTRUCT
	
	public function SwapDatabaseText($TEXT, $DB_ARRAY, $DBID_FLAG='') {

		#PRE_SWAP ANY DATA IF NEEDED
		if ($this->PreSwapArray != '') {
			$TEXT = $this->mbstr_replace($this->PreSwapArray, $TEXT);
		}

		#DO THE DATABASE SWAPS
		if (!$DBID_FLAG)
		{
		
			$TRANS_ARRAY = TextBetweenArray('[D~', ']', $TEXT);	

			$TransArray = array();
			for ($z = 0; $z<count($TRANS_ARRAY); $z++)
			{
				$FROM = "[D~{$TRANS_ARRAY[$z]}]";
				$TO = $DB_ARRAY[$TRANS_ARRAY[$z]];
				
				if (strpos($TO, '[NO TRANSLATION FOR') !== false)
				{
					$this->WriteDatabaseMissingLog($LANGUAGE, $TRANS_ARRAY[$z]);
				}
				
				$TransArray[$FROM] = $TO;
			}
		
			$TEXT = $this->mbstr_replace($TransArray, $TEXT);
		}
		return $TEXT;
	}


	function mbstr_replace($array, $str)
	{
		$old = array('[',']','~');
		$new = array('\[','\]','\~');
	
		foreach ($array as $key=>$value) {
			$key = str_replace($old,$new,$key);
			//$str = mb_ereg_replace($key, htmlentities($value, ENT_NOQUOTES, 'UTF-8'), $str);
			$str = mb_ereg_replace($key, $value, $str);
		}
	
		return $str;
	}
	
	
	function WriteDatabaseMissingLog($LANGUAGE, $IDENTIFIER)
	{
		global $PAGE,$SITECONFIG,$ROOT;
		$tid = $_SESSION['starttime'].substr(session_id(),-4);
		$logfile = $ROOT.$SITECONFIG['logdir'].'/databaseloglog-'.date('Y-m').'.dat';
		$line="$tid|{$PAGE['pagename']}|$LANGUAGE|$IDENTIFIER\n";
		append_file($logfile,$line);
	}


} #END CLASS
?>