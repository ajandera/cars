<?php

/**
 *
 * 2019-06-03
 * + file type
 *
 */

$corePage = new stdClass();
$corePage->title = l('Poptávky');
//$corePage->label = l('detail'); //vlastni popisek v detailu - u title

$corePage->db_table = 'poptavky'; //tabulka v DB
$corePage->dataCells = array('id'=>'ID','label'=>l('Služba'),'price'=>array('sort'=>true,'label'=>l('Cena'))); //ve vypisu zobrazit tyto data
$corePage->dataOrderBy = 'label ASC'; //poradi ve vypisu
$corePage->dataDB = array
	(
'ip'=>array('value'=>IP),
'jmeno',
'prijmeni',
'telefon',
'email',

'delka_smlouvy',
'vuz',
'vuz_vyroba',
'vuz_najeto',
'vuz_castka',
'auto_prevodovka',
'auto_spz',
'stat',
'info',
'source',
'source_hash',
'pobocka',
"source_savedby",
"marketing",

	);

//custom where
$corePage->addWhere = '';
$corePage->addWhereArr = array();
//$corePage->addWhere .= ' AND id_account = "'.$ACCOUNT['id'].'" ';

/**
 * script
 */

if(prava('admin-poptavky',true))
{
	if (isset($_POST["sendAction"]) or isset($_POST["sendActionSave"]))
	{
		//lets edit row
		if (!empty($_aid))
		{
			$newData = array();
			foreach ($corePage->dataDB as $k => $v)
			{
				//skip images
				if ($v == 'image' || $v == 'multiimage' || $v == 'multifile' || $v == 'file')
				{
					continue;
				}
				//skip
				if ($v == 'jmeno' || $v == 'prijmeni' || $v == 'telefon' || $v == 'email' || $v == 'source_hash' || $v == 'source' || $v == 'stat')
				{
					continue;
				}
				$newData[$k] = $v;
			}

			$sqlOLD = coreDBSel("SELECT * FROM {$C->db_prefix}{$corePage->db_table} WHERE id = '{$_aid}' LIMIT 1");
			if($sqlOLD)
			{
				$OLDDATA = coreDBfetch($sqlOLD);
			}

			$addInto = coreDBEdit($corePage->db_table,$newData,' id = "'.$_aid.'" '.$corePage->addWhere,'POSTstrict',1,false,$corePage->addWhereArr);
			if ($addInto)
			{
			  	$uid = $_aid;
				coreLog($core->loggedUser('id'),0,'Edit:'.arrayDifference($_POST,$OLDDATA),'poptavky-edit',$uid);
                // Update data on Daktela
                if (getConfig('daktelaapi') && in_array($_POST['stat'],array('cz','sk')))
                {
                    updateDaktelaRecord($uid);
                }
			}
		}
		//or add new row
		else
		{
			$newData = array();
			foreach ($corePage->dataDB as $k => $v)
			{
				//skip images
				if ($v == 'image' || $v == 'multiimage' || $v == 'multifile' || $v == 'file')
				{
					continue;
				}
				$newData[$k] = $v;
			}

			$addInto = coreDBInsert($corePage->db_table,$newData,'POSTstrict');
			if ($addInto)
			{
			  	$uid = $addInto['id'];
				coreLog($core->loggedUser('id'),0,'Added','poptavky-add',$uid);
            	poptavkyEcomail($_POST);
                if (getConfig('daktelaapi') && in_array($_POST['stat'],array('cz','sk')))
                {
                    createDaktelaRecord($uid);
					createEmergiaRecord($uid);
                }
			}
		}

		if (!coreMessagesGet())
		{
			//now lets add images
			$images = array();
			foreach ($corePage->dataDB as $k => $v)
			{
				//only images
				//only images
				if ($v == 'image')
				{
					$obrazek_nahled  =	handleUpload($k,"files/".$corePage->db_table,$corePage->db_table.$k."-$uid-".time(),true,3000,3000,false,300,200);
					if ($obrazek_nahled)
					{
						coreDBEditSingle($corePage->db_table,$k,$obrazek_nahled,' id = "'.$uid.'" ');
					}
					elseif (isset($_POST[$k.'_delete']))
					{
						//delete image
						coreDBEditSingle($corePage->db_table,$k,'',' id = "'.$uid.'" ');
					}
				}
				elseif ($v == 'file')
				{
					$obrazek_nahled  =	handleUpload($k,"files/".$corePage->db_table,$corePage->db_table.$k."-$uid-".time(),true,3000,3000,false,300,200,false,false,array('pdf','xls','xlsx','doc','docx','jpg','jpeg','gif','png'));
					if ($obrazek_nahled)
					{
						coreDBEditSingle($corePage->db_table,$k,$obrazek_nahled,' id = "'.$uid.'" ');
					}
					elseif (isset($_POST[$k.'_delete']))
					{
						//delete image
						coreDBEditSingle($corePage->db_table,$k,'',' id = "'.$uid.'" ');
					}
				}
			}

			//multiimage upload
			foreach ($corePage->dataDB as $k => $v)
			{
				if ($v == 'multiimage')
				{
					if (function_exists("obrazkyUploaderZpracuj"))
					{
						obrazkyUploaderZpracuj($k,$corePage->db_table.'-'.$k,$uid,8000,8000,500,150);
					}
				}
				elseif ($v == 'multifile')
				{
					if (function_exists("coreSouboryUploaderZpracuj"))
					{
						coreSouboryUploaderZpracuj($k,$corePage->db_table.'-'.$k,$uid,8000,8000,500,150);
					}
				}
			}
		}

		if (!coreErrorMessagesGet())
		{
			//all good, redirect
			coreMessage('Změny se provedly úspěšně.');

			if (isset($_POST["sendActionSave"]))
			{
				redirect($C->dir.$_display."/edit/$uid/");
			}
			else
			{
				redirect($C->dir.$_display."/edit/$uid/");
				//redirect($C->dir.$_display."/");
			}
		}
	}

	//editing detail
	if($_action == "edit")
	{
		if (empty($_aid))
		{
			$corePage->new = true;
		}
		else
		{
			$corePage->new = false;

			array_unshift($corePage->addWhereArr,$_aid);
			$sql = coreDBSel("SELECT * FROM {$C->db_prefix}{$corePage->db_table} WHERE id = ? {$corePage->addWhere} LIMIT 1",$corePage->addWhereArr);
			if ($sql)
			{
				if($sql->recordCount()==0)
				{
					coreErrorMessage(l("Záznam nenalezen. Chyba."));
					$corePage->new = true;
				}
				else
				{
					$_POST = coreDBfetch($sql);
				}
			}
		}
	}
	else
	{
		/*//sort
		if (isset($_GET["admrazeni"]) && !empty($_GET["admrazeni"]))
		{
			$corePage->dataOrderBy = cleanPostData(true,$_GET["admrazeni"]);
		}

		//list of things
		$sql = coreDBSel("SELECT * FROM {$C->db_prefix}{$corePage->db_table} WHERE 1=1 {$corePage->addWhere} ORDER BY {$corePage->dataOrderBy} LIMIT {$STR_start}, {$STR_view_number}",$corePage->addWhereArr);
		if ($sql)
		{
			if ($sql->recordCount()>0)
			{
				$sqlAll = coreDBSel("SELECT * FROM {$C->db_prefix}{$corePage->db_table} WHERE 1=1 {$corePage->addWhere}",$corePage->addWhereArr);
				$corePage->recordCount = $sqlAll->recordCount();
				$corePage->data = array();

				while ($x = $sql->fetchRow())
				{
					if (isset($corePage->dataCells) && is_array($corePage->dataCells))
					{
						foreach ($corePage->dataCells as $key=>$label)
						{
							if (isset($x[$key]))
							{
								$corePage->data[$x['id']][$key] = $x[$key];
							}
						}
					}
					else
					{
						$corePage->data[$x['id']] = $x;
					}

					//custom context options "možnosti"
					//$corePage->data[$x['id']]['context_zmocnit'] = '<a class="dropdown-item" href="#"><i class="fa fa-handshake-o fa-fw" aria-hidden="true"></i> Zmocnit k hlasování někoho jiného</a>' ;

				}

				$corePage->strankovani = coreStrankovani($corePage->recordCount,$page,$STR_view_number);
			}
		}*/

		redirect($C->dir.'poptavky/admin.poptavka/edit');
	}






	/**
	 * FORM
	 */



	$corePage->dataForm = array
	(
	'ss'=>'<div class="row"><div class="col-sm-6">'.createInputBlockStart(($corePage->new?l('Nová poptávka'):l('Úprava poptávky'))),
		'source'=>createInputSelect("source",poptavkyGetZdroje(),false,l("Zdroj"),"form-group",false,'- '.l('žádný').' -',"form-control form-control-lg rounded-0",false,false,'placeholder="Váš unikátní identifikátor" readonly required'),
		'source_hash'=>createInputText("source_hash",'',l("Unikátní identifikátor (pokud máte)"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'placeholder="Xxsff454GGhrhr"'),
		'jmeno'=>createInputText("jmeno",$_POST['jmeno'],l("Jméno"),"form-group",true,"form-control form-control-lg rounded-0",false,false,'placeholder="Uveďte své jméno" required'),
		'prijmeni'=>createInputText("prijmeni",$_POST['prijmeni'],l("Příjmení"),"form-group",true,"form-control form-control-lg rounded-0",false,false,'placeholder="Uveďte své příjmení" required'),
		'email'=>createInputText("email",$_POST['email'],l("E-mail"),"form-group",false,"form-control email form-control-lg rounded-0",false,false,'placeholder="@" required',200,false,'','email'),
		'telefon'=>createInputText("telefon",$_POST['telefon'],l("Telefonní číslo"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'placeholder="Uveďte své telefonní číslo" required pattern=".{9,}" title="Zadajte 9 číslic"'),
		'vuz'=>createInputText("vuz",$_POST['vuz'],l("Značka a model vozu"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'placeholder="Např. Škoda Fabia"  '),
		'vuz_vyroba'=>createInputText("vuz_vyroba",$_POST['vuz_vyroba'],l("Rok výroby"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'placeholder="Např. 2019"  min="1915" max="'.date('Y').'"'),
		'vuz_najeto'=>createInputText("vuz_najeto",$_POST['vuz_najeto'],l("Počet najetých km"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'placeholder="Např. 142 000 km" '),
		'vuz_castka'=>createInputText("vuz_castka",$_POST['vuz_castka'],l("Požadovaná částka"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'placeholder="Např. 80 000 Kč"'),
		'auto_spz'=>createInputText("auto_spz",$_POST['auto_spz'],l("SPZ"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'placeholder="CZ121645"'),
		'auto_prevodovka'=>createInputSelect("auto_prevodovka",getConfig('bazar-prevodovka'),$_POST["auto_prevodovka"],l("Převodovka"),"form-group",false,l('Vyberte'),"form-control input-sm"),
		'delka_smlouvy'=>createInputText("delka_smlouvy",$_POST['delka_smlouvy'],l("Délka smlouvy"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'placeholder="Např. 6 měsíců" '),
		'info'=>createInputTextarea("info",$_POST['info'],l("Poznámka"),"form-group",8,false,"form-control"),
		'pobocka'=>'x',
		'stat'=>createInputSelect('stat',$C->staty,$_POST['stat'],'Vyberte stát','form-group',false,false,'form-control form-control-lg rounded-0'),
		'marketingAdd'=>'',
		'marketing'=>createInputCheckbox('marketing',0,$_POST['marketing'],'Souhlas pro marketing','form-group',false, false, ''),

		'source_savedby'=>createInputHidden('source_savedby',(!$corePage->new?$_POST['source_savedby']:'manual')),
		'instacover_session_id' => createInputText("Instacover session",$_POST['instacover_session_id'],l("Instacover session"),"form-group",false,"form-control form-control-lg rounded-0",false,false,''),
		'instacover_link' => createInputText("Instacover link","https://instacar.instacover.ai/" . $_POST['instacover_session_id'],l("Instacover link"),"form-group",false,"form-control form-control-lg rounded-0",false,false,'readonly="readonly"'),
		'send'=>createInputSubmit("sendAction","Uložit"),
	'sse'=>createInputBlockEnd().'</div></div>',
	);
	/**
	 * FORM
	 */

	if($_POST['marketing']==1)
	{
		$corePage->dataForm['marketingAdd'] = createInputInfo(l('Marketingový souhlas'),$_POST['marketing_date']);
	}

	if (prava('admin-zastavy-vsechnypobocky'))
	{
		$corePage->dataForm['pobocka'] = createInputSelect("pobocka",$C->pobocky,$_POST["pobocka"],l("Spadá pod pobočku"),"form-group",false,l('Vyberte'),"form-control",false,false);
	}
	else
	{
		if (!empty($INFO["pobocka"]))
		{
			$USER_pobocky = explode(',',$INFO["pobocka"]);
			if (is_array($USER_pobocky) && !empty($USER_pobocky))
			{
				foreach ($C->pobocky as $k => $v)
				{
					if (!in_array($k,$USER_pobocky))
					{
						unset($C->pobocky[$k]);
					}
				}
			}
		}

		$corePage->dataForm['pobocka'] = createInputSelect("pobocka",$C->pobocky,(empty($_POST["pobocka"])?false:$_POST["pobocka"]),l("Spadá pod pobočku"),"form-group",false,l('Vyberte'),"form-control",false,false);
	}


	if(!$corePage->new)
	{
		unset($corePage->dataForm['source']);
		unset($corePage->dataForm['source_hash']);
		unset($corePage->dataForm['jmeno']);
		unset($corePage->dataForm['prijmeni']);
		unset($corePage->dataForm['email']);
		unset($corePage->dataForm['telefon']);
		unset($corePage->dataForm['stat']);
	}


	$coreTemplate->assign('corePage',$corePage);
	$core->setTitle($corePage->title);
}