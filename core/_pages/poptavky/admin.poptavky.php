<?php

cleanGetData();

if(prava('admin-poptavky'))
{

if($_action == "detail" && isset($_aid) && $_aid > 0)
{
	if(prava('admin-poptavky'))
{
	if (prava("admin-zastavy-vsechnypobocky"))
	{
		$addWHERE = "";
	}
	else
	{
		$addWHERE = "AND FIND_IN_SET(pobocka,'".clearUnsafeChars($INFO["pobocka"])."') ";
	}


	if (!empty($core->loggedUser('stat_limit')))
	{
		$addWHERE .= " AND FIND_IN_SET(stat,'".clearUnsafeChars($core->loggedUser('stat_limit'))."') ";
		//$addWHEREArray[] = clearUnsafeChars($core->loggedUser('stat_limit'));
	}


	$sql = "SELECT * FROM {$C->db_prefix}poptavky WHERE id = '{$_aid}' $addWHERE LIMIT 1";
	$sql_query = mysql_query($sql);
	if(mysql_num_rows($sql_query)==0)
	{
		echo box_info(l("Záznam nenalezen. Chyba."),2);
	}
	else
	{
		$_POST = mysql_fetch_assoc($sql_query);

		echo '<div class="page-header"><h1>'.l('Poptávka').' id '.$_POST["id"].' <small><span class="label label-info">'.l('Komunikace').'</span></small></h1></div>';

		if (!is_valid_email($_POST["email"]))
		{
			echo boxInfo(l('Pozor email zadaný přes formulář je neplatný'),true);
		}

		$sendFrom = getConfig('email-poptavka-from-'.$_POST['stat']);
		$sendFromName = getConfig('email-poptavka-jmeno-'.$_POST['stat']);

		if (empty($sendFrom))
		{
			$sendFrom = getConfig('email-poptavka-from');
		}

		if (empty($sendFromName))
		{
			$sendFromName = getConfig('email-poptavka-jmeno');
		}

		if (is_valid_email($sendFrom))
		{
			//all good
		}
		else
		{
			$sendFrom = $INFO['email'];
		}

		if (find_in_set($_POST['stat'],'sk,cz'))
		{
			if (strpos($_POST['jmeno']," ")!==false)
			{
				$_POST['jmeno'] = explode(" ",$_POST['jmeno'],2);
				$_POST['jmeno'] = sklonJmeno5pad($_POST['jmeno'][0]);
			}
		}
		else
		{
			$_POST['jmeno'] = $_POST['jmeno'];
		}

		echo '<div class="row"><div class="col-sm-8">';
		echo '<form action="" method="post" class="" enctype="multipart/form-data" id="form">';

		echo '<script> var emailjezdi = '.json_encode(l(SablonyGet('email-jezdi','text',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailjezdinejista = '.json_encode(l(SablonyGet('email-jezdi-nejista','text',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailkalkulace = '.json_encode(l(SablonyGet('email-kalkulace','text',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailnejezdi = '.json_encode(l(SablonyGet('email-nejezdi','text',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailnezajem = '.json_encode(l(SablonyGet('email-nemamzajem','text',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var email6 = '.json_encode(l(SablonyGet('email-financovane','text',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%cena%'=>$_POST['vuz_castka'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var email7 = '.json_encode(l(SablonyGet('email-pod-minimum','text',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%cena%'=>$_POST['vuz_castka'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var email8 = '.json_encode(l(SablonyGet('email-pouzeosobni','text',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%cena%'=>$_POST['vuz_castka'],'%mojejmeno%'=>$sendFromName))).'; </script>';

		echo '<script> var emailjezdiPredmet = '.json_encode(l(SablonyGet('email-jezdi','predmet',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailjezdinejistaPredmet = '.json_encode(l(SablonyGet('email-jezdi-nejista','predmet',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailkalkulacePredmet = '.json_encode(l(SablonyGet('email-kalkulace','predmet',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailnejezdiPredmet = '.json_encode(l(SablonyGet('email-nejezdi','predmet',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailnezajemPredmet = '.json_encode(l(SablonyGet('email-nemamzajem','predmet',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailP6 = '.json_encode(l(SablonyGet('email-financovane','predmet',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailP7 = '.json_encode(l(SablonyGet('email-pod-minimum','predmet',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';
		echo '<script> var emailP8 = '.json_encode(l(SablonyGet('email-pouzeosobni','predmet',$_POST['stat']),array('%jmeno%'=>$_POST['jmeno'],'%mojejmeno%'=>$sendFromName))).'; </script>';

		echo createInputBlockStart(l("Nový email"));
			echo createInputHidden('stat',$_POST['stat']);
			echo createInputHidden('jmeno',$_POST['jmeno']);
			echo createInputInfo(l('Info'),l('Od').': '.$sendFrom.' ('.$sendFromName.')');
			echo createInputText("email",$_POST["email"],l("Posíláte na tento email"),"form-group",true,"form-control input-sm");
			echo createInputText("predmet",'',l("Předmět"),"form-group",true,"form-control input-sm");

			echo '<strong>'.l('Předdefinované zprávy').'</strong><br />';
			echo createInputButton('addText',l('Jezdí, jisté nacenění'),'form-group','btn btn-default',false,' onclick="chat_addToText(emailjezdi);chat_addToText(emailjezdiPredmet,\'#predmet\');" ',true).' ';
			echo createInputButton('addText',l('Jezdí, ale nejisté nacenění'),'form-group','btn btn-default',false,' onclick="chat_addToText(emailjezdinejista);chat_addToText(emailjezdinejistaPredmet,\'#predmet\');" ',true).' ';
			echo createInputButton('addText',l('Nejezdí'),'form-group','btn btn-default',false,' onclick="chat_addToText(emailnejezdi);chat_addToText(emailnejezdiPredmet,\'#predmet\');" ',true).' ';
			echo createInputButton('addText',l('Kalkulace'),'form-group','btn btn-default',false,' onclick="chat_addToText(emailkalkulace);chat_addToText(emailkalkulacePredmet,\'#predmet\');" ',true).' ';
			echo createInputButton('addText',l('Nemáme zájem'),'form-group','btn btn-default',false,' onclick="chat_addToText(emailnezajem);chat_addToText(emailnezajemPredmet,\'#predmet\');" ',true).' ';
			echo createInputButton('addText',l('Příliš málá částka'),'form-group','btn btn-default',false,' onclick="chat_addToText(email6);chat_addToText(emailP6,\'#predmet\');" ',true).' ';
			echo createInputButton('addText',l('Vůz je už financován'),'form-group','btn btn-default',false,' onclick="chat_addToText(email7);chat_addToText(emailP7,\'#predmet\');" ',true).' ';
			echo createInputButton('addText',l('Pouze osobní vozy'),'form-group','btn btn-default',false,' onclick="chat_addToText(email8);chat_addToText(emailP8,\'#predmet\');" ',true).' ';
			echo cls().'<br />';

			echo createInputTextarea("message",'',l("E-mail"),"form-group",8,false,"form-control wysiwyg",false,'messageSend');

			echo createInputFile('priloha1',l('Příloha 1 (pdf, doc, jpg, png, gif, xls)'));
			echo createInputFile('priloha2',l('Příloha 2 (pdf, doc, jpg, png, gif, xls)'));
			echo createInputFile('priloha3',l('Příloha 3 (pdf, doc, jpg, png, gif, xls)'));
			echo createInputText("telefon",$_POST["telefon"],l("Informační SMS půjde na telefon"),"form-group",false,"form-control input-sm");

			echo createInputSubmit("sendAction",l("Odeslat email"),"form-group","btn-primary btn-lg btn-block marginT2",false,'onclick="return confirm(\''.l("Opravdu chcete poslat email?").'\')"');

		echo createInputBlockEnd();

		echo '</form>';


		$sql = coreDBSel("SELECT * FROM ".$C->db_prefix."poptavky_email WHERE id_poptavka = ? ORDER BY id DESC",array($_aid));
		if ($sql)
		{
			if ($sql->recordCount()==0)
			{
				echo boxInfo(l("Zatím zde není žádný poslaný email."),false);
			}
			else
			{
				while ($x = $sql->fetchRow())
				{
					echo '<hr>';
					echo '<h2>'.predkdy_dny($x['date_add'],4).': '.$x['predmet'].'</h2>';
					echo '<p>'.$x['text'].'</h2>';

					if (!empty($x['priloha1']))
					{
						echo '<a href="/'.$x['priloha1'].'" target="_blank">'.l('Příloha č.1').'</a><br />';
					}
					if (!empty($x['priloha2']))
					{
						echo '<a href="/'.$x['priloha2'].'" target="_blank">'.l('Příloha č.2').'</a><br />';
					}
					if (!empty($x['priloha1']))
					{
						echo '<a href="/'.$x['priloha3'].'" target="_blank">'.l('Příloha č.3').'</a><br />';
					}
				}
			}
		}


		echo '</div>';
		echo '</div>';

		echo '<hr>';

	}

}
else
{
	echo boxInfo(l('Nemáte oprávnění'));
}
}


	if ($_action == "smazane")
	{
		$subHeading = '<span class="label label-danger">'.l('Smazané').'</span>';

	}
	elseif ($_action == "archiv")
	{
		$subHeading = '<span class="label label-info">'.l('Archiv').'</span>';

	}
	elseif ($_action == "all")
	{
		$subHeading = '<span class="label label-warning">'.l('Všechny').'</span>';
	}
	else
	{
		$subHeading = '<span class="label label-default">'.l('Aktuální').'</span>';
	}

	echo '<div class="page-header"><h1>'.l('Poptávky').' <small>'; echo $subHeading; echo'</small>';


		echo ' <div class="btn-group" id="uu'.$DATA['id'].'">
			<button class="btn btn-default btn-sm dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars fa-fw fa-lg" aria-hidden="true"></i> '.l('Možnosti').'</button>
			<ul class="dropdown-menu">';

		if (prava('admin-poptavky-export-excel'))
		{
			echo '<li><a class="" href="/object/poptavky/admin.export-excel/'.$_action.clear_request_url('non',true,'?').'"><i class="fa fa-file-excel-o fa-fw"></i> Export poptávek (xls)</a></li>';
		}

		echo '</ul></div>';

	echo '</h1></div>';

	$novePobocky = array();
	$novePobockyWrap = array('x'=>array('optgroupstart'=>true,'label'=>l('Státy')));
	$novePobockyPostWrap['x2'] = array('optgroupend'=>true);
	$novePobockyPostWrap['x3'] = array('optgroupstart'=>true,'label'=>l('Pobočky'));
	$novePobockyStaty = array('czsk'=>'cz+sk');
	$stat2pobocka = array();
	foreach ($C->pobocky as $k=>$v)
	{
		if (!empty($core->loggedUser('stat_limit')))
		{
			if(!find_in_set($v['stat'],$core->loggedUser('stat_limit')))
			{
				continue;
			}
		}

		$novePobocky[$k] = $v;
		$novePobockyStaty[$v['stat']] = $v['stat'];
		$stat2pobocka[$v['stat']][] = $k;
		if ($v['stat'] == 'sk' || $v['stat'] == 'cz')
		{
			$stat2pobocka['czsk'][] = $k;
		}
	}

	if(!isset($novePobockyStaty['cz'],$novePobockyStaty['sk']))
	{
		unset($novePobockyStaty['czsk']);
	}

	$novePobocky = $novePobockyWrap+$novePobockyStaty+$novePobockyPostWrap+$novePobocky;
	$novePobocky['x4'] = array('optgroupend'=>true);


	$addWHERE = "";
	$addWHEREArray = array();

	if (isset($_GET["pobocka"]) && !empty($_GET["pobocka"]) && prava("admin-poptavky-vsechnypobocky"))
	{
		//save me
		$_SESSION['pobocka'] = $_GET["pobocka"];

		if ($_GET["pobocka"]=='czsk')
		{
			$addWHERE .= "AND (stat='cz' or stat='sk')";
		}
		elseif (isset($stat2pobocka[$_GET["pobocka"]]))
		{
			$addWHERE .= "AND (stat=?)";
			//$addWHERE .= "AND (FIND_IN_SET(pobocka,'".implode(',',$stat2pobocka[$_GET["pobocka"]])."') or stat=?)";
			$addWHEREArray[] = clearUnsafeChars($_GET["pobocka"]);
		}
		else
		{
			$addWHERE .= "AND pobocka = ?";
			$addWHEREArray[] = clearUnsafeChars($_GET["pobocka"]);
		}
	}
	elseif (isset($_GET["pobocka"]) && empty($_GET["pobocka"]))
	{
		//unset me
		unset($_SESSION['pobocka']);
	}
	else
	{
		if (prava("admin-poptavky-vsechnypobocky"))
		{

		}
		else
		{
			$addWHERE .= "AND FIND_IN_SET(pobocka,'".clearUnsafeChars($INFO["pobocka"])."') ";
		}
		//$addWHERE .= "AND FIND_IN_SET(akce,'".implode(',',$akceAktivni)."') ";
	}

	if (!empty($core->loggedUser('stat_limit')))
	{
		$addWHERE .= " AND FIND_IN_SET(stat,?) ";
		$addWHEREArray[] = clearUnsafeChars($core->loggedUser('stat_limit'));
	}


	if ($_action == "smazane")
	{
		$addWHEREpay = "AND stav < '0' ";
	}
	elseif ($_action == "archiv")
	{
		$addWHEREpay = "AND stav = '1' ";
	}
	elseif ($_action == "all")
	{
		$addWHEREpay = " ";
	}
	else
	{
		$addWHEREpay = "AND (stav = '0' or stav = -6) ";
	}



	echo '<form action="';
	echo clear_request_url(array("page","od","do","filtrSkupiny","s"),false,"?");
	echo '" method="get" role="form" id="" class="form-inline pull-right">';

	if($_action == 'all')
	{
		$poptavkyStavy = $C->poptavky_stav;

		if (isset($_GET["stav"]) && (!empty($_GET["stav"]) || $_GET["stav"]==="0" || $_GET["stav"]===-1))
		{

			$addWHERE .= "AND stav LIKE ? ";
			$addWHEREArray[] = (int)$_GET["stav"];
			echo createInputSelect("stav",$poptavkyStavy,$_GET["stav"],"Akce","form-group has-feedback has-success",false,l("Stav"),"form-control",false,false,false,'style="width: 150px"',false);
		}
		else
		{
			echo createInputSelect("stav",$poptavkyStavy,999,l("Stav"),"form-group",false,l("Stav"),"form-control",false,false,false,'style="width: 130px"');
		}
	}

	if (prava("admin-poptavky-vsechnypobocky"))
	{
		if (isset($_GET["pobocka"]) && !empty($_GET["pobocka"]))
		{
			echo createInputSelect("pobocka",$novePobocky,$_GET["pobocka"],"Akce","form-group has-feedback has-success",false,l("Stát / pobočka"),"form-control",false,false,false,'style="width: 150px"',false);
		}else{
			echo createInputSelect("pobocka",$novePobocky,false,l("Akce"),"form-group",false,l("Stát / pobočka"),"form-control",false,false,false,'style="width: 130px"');
		}
	}

	$zdroje =  poptavkyGetZdroje();
	if (isset($_GET["source"]) && !empty($_GET["source"]) && is_array($_GET["source"]))
	{
		$addWHERE .= "AND FIND_IN_SET(source,?) ";
		$addWHEREArray[] = clearUnsafeChars(implode(',',$_GET["source"]));
		echo createInputSelect("source[]",$zdroje,$_GET["source"],"Akce","form-group has-feedback has-success",false,false,"form-control selectpicker",false,false,false,' multiple="multiple" data-actions-box="true" data-selected-text-format="count > 3"  data-width="200px"  title="'.l('Zdroj').'"',false);
	}
	else
	{
		echo createInputSelect("source[]",$zdroje,false,l("Akce"),"form-group",false,false,"form-control selectpicker",false,false,false,' multiple="multiple" data-actions-box="true" data-selected-text-format="count > 3"  data-width="200px"  title="'.l('Zdroj').'"');
	}

	if (isset($_GET["jmeno"]) && !empty($_GET["jmeno"]))
	{
		$addWHERE .= "AND jmeno LIKE ? ";
		$addWHEREArray[] = '%'.clearUnsafeChars($_GET["jmeno"]).'%';
		echo createInputText("jmeno",$_GET["jmeno"],l("Jméno"),"form-group has-feedback has-success",false,"form-control",false,"p:Jméno",'size="7"',20,false,'<a class="form-control-feedback" href="'.clear_request_url(array("jmeno","s"),false,true).'" title="Zrušit filtr"><i class="fa fa-times-circle"></i></a>');
	}else{
		echo createInputText("jmeno","",l("jmeno"),"form-group",false,"form-control",false,"p:Jméno",'size="9"');
	}

	if (isset($_GET["telefon"]) && !empty($_GET["telefon"]))
	{
		$addWHERE .= "AND telefon LIKE ? ";
		$addWHEREArray[] = '%'.clearUnsafeChars($_GET["telefon"]).'%';
		echo createInputText("telefon",$_GET["telefon"],l("telefon"),"form-group has-feedback has-success",false,"form-control",false,"p:".l('Telefon'),'size="7"',20,false,'<a class="form-control-feedback" href="'.clear_request_url(array("telefon","s"),false,true).'" title="Zrušit filtr"><i class="fa fa-times-circle"></i></a>');
	}else{
		echo createInputText("telefon","",l("telefon"),"form-group",false,"form-control",false,"p:".l('Telefon'),'size="9"');
	}

	if (isset($_GET["email"]) && !empty($_GET["email"]))
	{
		$addWHERE .= "AND email LIKE ? ";
		$addWHEREArray[] = '%'.clearUnsafeChars($_GET["email"]).'%';
		echo createInputText("email",$_GET["email"],l("Email"),"form-group has-feedback has-success",false,"form-control",false,"p:Email",'size="7"',20,false,'<a class="form-control-feedback" href="'.clear_request_url(array("email","s"),false,true).'" title="Zrušit filtr"><i class="fa fa-times-circle"></i></a>');
	}else{
		echo createInputText("email","",l("Email"),"form-group",false,"form-control",false,"p:Email",'size="9"');
	}

	if (isset($_GET["od"]) && !empty($_GET["od"]))
	{
		$addWHERE .= "AND DATE(date_add) >= ? ";
		$addWHEREArray[] = clearUnsafeChars($_GET["od"]);
		echo createInputText("od",$_GET["od"],false,"form-group has-feedback has-success",false,"form-control datum",false,'p:'.l('Datum od'),'size="7"',20,false,'<a class="form-control-feedback" href="'.clear_request_url(array("od","s"),false,true).'" title="'.l('Zrušit filtr').'"><i class="fa fa-times-circle"></i></a>');
	}else{
		echo createInputText("od","",false,"form-group",false,"form-control datum",false,'p:'.l('Datum od'),'size="9"');
	}

	echo ' - ';

	if (isset($_GET["do"]) && !empty($_GET["do"]))
	{
		$addWHERE .= "AND DATE(date_add) <= ? ";
		$addWHEREArray[] = clearUnsafeChars($_GET["do"]);
		echo createInputText("do",$_GET["do"],false,"form-group has-feedback has-success",false,"form-control datum",false,'p:'.l('Datum do'),'size="7"',20,false,'<a class="form-control-feedback" href="'.clear_request_url(array("do","s"),false,true).'" title="'.l('Zrušit filtr').'"><i class="fa fa-times-circle"></i></a>');
	}else{
		echo createInputText("do","",false,"form-group",false,"form-control datum",false,'p:'.l('Datum do'),'size="9"');
	}

	if (isset($_GET["vocalls"]) && !empty($_GET["vocalls"]))
	{
		$addWHERE .= " AND stav_vocalls > 0 ";
		echo createInputCheckbox("vocalls",1,$_GET["vocalls"],'Voláno vocalls',"checkbox",false,"form-control");
	}else{
		echo createInputCheckbox("vocalls",1,false,'Voláno vocalls',"checkbox",false,"form-control");
	}

	if (isset($_GET["unique"]) && !empty($_GET["unique"]))
	{
		$addWHEREpay .= " GROUP BY email ";
		echo createInputCheckbox("unique",1,$_GET["unique"],'Unikátní poptávky',"checkbox",false,"form-control");
	}else{
		echo createInputCheckbox("unique",1,false,'Unikátní poptávky',"checkbox",false,"form-control");
	}



	echo createInputSubmit("s",l("Vyhledávat"));
	echo '</form>';


	if (isset($_GET["admrazeni"]) && !empty($_GET["admrazeni"]))
	{
		$addORDERBY = "ORDER BY {$_GET["admrazeni"]} ";
	}
	else
	{
		$addORDERBY = "ORDER BY id DESC ";
	}

	$sql = coreDBSel("SELECT * FROM ".$C->db_prefix."poptavky WHERE 1=1 $addWHERE $addWHEREpay $addORDERBY LIMIT $STR_start, $STR_view_number",$addWHEREArray);
	if ($sql)
	{

		$sqlAll = coreDBSel("SELECT * FROM ".$C->db_prefix."poptavky WHERE 1=1 $addWHERE $addWHEREpay",$addWHEREArray);
		$sqlAllCount = $sqlAll->recordCount();
		echo '<a class="btn btn-primary" href="'.$C->dir.'poptavky/admin.poptavka/edit'.'">'.l('Přidat poptávku ručně').'</a>';
		echo '<a class="btn btn-warning" href="'.$C->dir.'ecomail/object.admin.poptavka-export'.'">'.l('Export pro Ecomail').'</a>';
		echo '<span class="btn btn-default marginL1" style="cursor:auto">'.$sqlAllCount.' '.l('záznamů').'</span>';

        // Added by Tom
        if ( prava( 'admin-poptavky' ) && $sqlAllCount < 500 ) {
            $daktela_all_items = $sqlAll->getAll();
            $daktela_all_ids = implode(',', array_column($daktela_all_items, 'id'));
            echo '<br style="clear: both" />';
            echo '<div class="btn-group marginT1 pull-right">';
            echo '<span class="btn btn-primary" href="#" id="poptavky-export-daktela-auto" onclick="exportToDaktela(\'auto\');">' . l('Daktela - Auto') . '</span>';
            echo '<span class="btn btn-warning marginL1" href="#" id="poptavky-export-daktela-hot" onclick="exportToDaktela(\'hot-leads\');">' . l('Daktela - Hot Leads') . '</span>';
            echo '<span class="btn btn-default marginL1" href="#" id="poptavky-export-daktela-cold" onclick="exportToDaktela(\'cold-leads\');">' . l('Daktela - Cold') . '</span>';
            echo '<span class="hidden" data-poptavky-ids="'.$daktela_all_ids.'" id="data-poptavky-ids"></span>';
            echo '</div>';
        }

        // End of added by Tom

	echo cls();
	echo "<br />";

		if ($sql->recordCount()==0)
		{
			echo box_info("Zatím zde není žádný záznam.",2);
		}
		else
		{


?>
<form action="" method="post" class="marginB4">
<div class="table-responsive hidden-xs">
	<table class="table table-condensed table-striped table-hover">
	<thead>
	<tr>
		<th><?php echo admin_razeni_link("id",l("id"))?></th>
		<th><small><?php echo admin_razeni_link("stat",l("Stát"))?></small></th>
		<th><small><?php echo admin_razeni_link("pobocka",l('Pobočka'))?></small></th>
		<th class="text-right"><small><?php echo admin_razeni_link("date_add",l("Datum"))?></small></th>
		<th><small><?php echo admin_razeni_link("jmeno",l("Jméno"))?></small></th>
		<th><small><?php echo admin_razeni_link("prijmeni",l("Příjmení"))?></small></th>

		<?php
		if (prava('admin-poptavky'))
		{	?>
			<th><small><?php echo admin_razeni_link("email",l("Email"))?></small></th>
			<th><small><?php echo admin_razeni_link("telefon",l("Telefon"))?></small></th>
<?php	}	?>

		<th><small><?php echo admin_razeni_link("vuz",l("Auto"))?></small></th>
		<th><small><?php echo admin_razeni_link("vuz_najeto",l("Najeto"))?></small></th>
		<th><small><?php echo admin_razeni_link("vuz_vyroba",l("RV"))?></small></th>
		<th><small><?php echo admin_razeni_link("auto_prevodovka",l("Převodovka"))?></small></th>
		<th><small><?php echo admin_razeni_link("auto_spz",l("SPZ"))?></small></th>
		<th><small><?php echo admin_razeni_link("auto_financovane",l("Financované"))?></small></th>
		<th class="text-right"><small><?php echo admin_razeni_link("vuz_castka",l("Částka"))?></small></th>
		<th class=""><small><?php echo admin_razeni_link("info",l("Poznámka"))?></small></th>
		<th><small><?php echo admin_razeni_link("source",l("Zdroj"))?></small></th>
		<th><small><?php echo admin_razeni_link("stav",l("Stav"))?></small></th>

		<?php
		if (prava('admin-poptavky'))
		{	?>

		<th><small><?php echo admin_razeni_link("zpusob_zastavy",l("Zástava"))?></small></th>
		<th><small><?php echo admin_razeni_link("delka_smlouvy",l("Délka zást."))?></small></th>
		<th><small><?php echo admin_razeni_link("stav_sms1",l("Vítací<br />SMS"))?></small></th>
		<th><small><?php echo admin_razeni_link("stav_email1",l("Vítací<br />E-mail"))?></small></th>
		<th><small><?php echo admin_razeni_link("stav_odpovezeno",l("Odp."))?></small></th>

		<th ><small title="<?php echo l('Zákazník má zájem');?>" class="tooltips"><?php echo admin_razeni_link("stav_customerinterested",l("Zájem"))?></small></th>
		<th ><small title="<?php echo l('Zákazníka kalkulace/nabídka zajímá');?>" class="tooltips"><?php echo admin_razeni_link("stav_customerinteresting",l("Kalk."))?></small></th>

		<th class="text-center" style="min-width: 48px;"><small><?php echo admin_razeni_link("stav_volano",'<i class="fa fa-phone" aria-hidden="true"></i>')?></small></th>
		<th class="text-center"><small><?php echo admin_razeni_link("stav_technicak",l("TP<br>máme?"))?></small></th>
		<th><small><?php echo admin_razeni_link("stav_nabidka",l("Nabíd."))?></small></th>
		<th><small><?php echo admin_razeni_link("stav_pojistky",l("Pojis.?"))?></small></th>
		<th><small><?php echo admin_razeni_link("stav_smsobchodni",l("Obch. SMS"))?></small></th>
		<?php	}	?>
		<th></th>
	</tr>
	</thead>
	<tbody>
<?php

/*
stav_odpovezeno
stav_technicak
stav_nabidka
stav_pojistky
*/

$echo = "";
$echoXS = "";
$arr_prevodovka = getConfig('bazar-prevodovka');

while ($x = $sql->fetchRow())
{
	$x["vuz_castka"] = str_replace(' ','',$x["vuz_castka"]);

	$addAlertEmail = '';
	$addAlertIP = '';
	$addStav = 'default';

	if ($_action == "detail" && $_aid == $x["id"])
	{
		$addStav = 'danger';
	}

	$echo .=  "<tr id='uu{$x["id"]}' class=\"{$addStav} c-{$x["stat"]}\">";
	$echo .= "<td><strong>{$x["id"]}</strong></td>";
	$echo .= "<td>{$x["stat"]}</td>";
	$echo .= "<td>{$x["pobocka"]}</td>";

	$echo .= "<td class=\"$datumaddprice text-right\"><small>".predkdy_dny($x["date_add"],1)."</small></td>";

	$echo .= "<td><small title=\"".htmlentities($x["jmeno"])."\">".mb_substr($x["jmeno"],0,25,'utf-8')."</small></td>";
	$echo .= "<td><small title=\"".htmlentities($x["prijmeni"])."\">".mb_substr($x["prijmeni"],0,25,'utf-8')."</small></td>";

	if (prava('admin-poptavky'))
	{
		$echo .= "<td><a href='mailto:{$x["email"]}'><small>{$x["email"]}</small></a></td>";
		$echo .= "<td><a href=\"tel:{$x["telefon"]}\"><small>{$x["telefon"]}</small></a></td>";
	}

	$echo .= "<td><small>{$x["vuz"]}</small></td>";
	$echo .= "<td><small>{$x["vuz_najeto"]}</small></td>";
	$echo .= "<td><small>{$x["vuz_vyroba"]}</small></td>";
	$echo .= "<td><small>{$arr_prevodovka[$x["auto_prevodovka"]]}</small></td>";

	$echo .= "<td><small>{$x["auto_spz"]}</small></td>";
	$echo .= "<td class='text-center'>".($x["auto_financovane"]==1?l('ANO'):l('NE'))."</td>";

	$echo .= "<td class=\"text-right\">".(is_numeric($x["vuz_castka"])?number_format($x["vuz_castka"],0,',',' ').'&nbsp;'.(isset($C->stat_mena[$x['stat']])?$C->cena_mena[$C->stat_mena[$x['stat']]]['label']:''):$x["vuz_castka"]);
	$echo .= "<td><small>";


	if($x['stav_vocalls']>=1)
	{
		$echo .= '<i class="fa fa-phone-square text-success fs-3" aria-hidden="true"></i> <strong>VOCALLS voláno</strong><br>';
		$find = coreDBSel('SELECT * FROM '.$C->db_prefix.'poptavky_vocalls WHERE id_poptavka = ? ORDER BY id DESC',array($x['id']));
		{
			while($y = $find->fetchRow())
			{
				$echo .=  '<div class="marginB1 bg-success paddingT1 paddingL1 paddingR1 paddingB1">'.$y['callResult'].(!empty($y['lastState'])?' ('.$y['lastState'].') ':'').' '.(!$y['date_edit']?predkdy_dny($y['date_add']):predkdy_dny($y['date_edit'])).'</div>';
			}
		}
	}

	$echo .= "{$x["info"]}</small></td>";
	$echo .= "<td><small>".($x["source"]).(!empty($x['source_orig'])?'<br><s>'.$x['source_orig'].'</s>':'')."</small>";

	if((strtolower($x['source'])=='lead-adtraction' || strtolower($x['source'])=='adtraction') && $x['stav_adtraction']==0 && $x['stav']>=0)
	{
		$echo .= "<a id='row-poptavky-adtraction-{$x["id"]}' class='btn btn-success btn-xs ' style='margin: 3px 0 0 3px' href='javascript:deleteLine(\"poptavky-adtraction\",{$x["id"]});'><i class=\"fa fa-check fa-fw\"></i> ".l('Report lead')."</a>";
	}
	elseif((strtolower($x['source'])=='lead-crezu') && $x['stav_adtraction']==0 && $x['stav']>=0)
	{
		$echo .= "<a id='row-poptavky-adtraction-{$x["id"]}' class='btn btn-success btn-xs ' style='margin: 3px 0 0 3px' href='javascript:deleteLine(\"poptavky-adtraction\",{$x["id"]});'><i class=\"fa fa-check fa-fw\"></i> ".l('Report lead')."</a>";
	}
	elseif((strtolower($x['source'])=='lead-finansi') && $x['stav_adtraction']==0 && $x['stav']>=0)
	{
		$echo .= "<a id='row-poptavky-adtraction-{$x["id"]}' class='btn btn-success btn-xs ' style='margin: 3px 0 0 3px' href='javascript:deleteLine(\"poptavky-adtraction\",{$x["id"]});'><i class=\"fa fa-check fa-fw\"></i> ".l('Report lead')."</a>";
	}

	$echo .= "</td>";
	$echo .= "<td class='active'><small>{$C->poptavky_stav[$x["stav"]]}</small></td>";

	if (prava('admin-poptavky'))
	{

		$echo .= "<td><small>".($x["zpusob_zastavy"])."</small></td>";
		$echo .= "<td><small>".($x["delka_smlouvy"])."</small></td>";
		$echo .= "<td class='text-center'>".($x["stav_sms1"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-sms1\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-sms1\','.$x["id"].',\'toggle\',this);"></i>')."</td>";
		$echo .= "<td class='text-center'>".($x["stav_email1"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-email1\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-email1\','.$x["id"].',\'toggle\',this);"></i>')."</td>";
		$echo .= "<td class='text-center'>".($x["stav_odpovezeno"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-odpoved\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-odpoved\','.$x["id"].',\'toggle\',this);"></i>')."</td>";

		$echo .= "<td class='text-center'>".($x["stav_customerinterested"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-customerinterested\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-customerinterested\','.$x["id"].',\'toggle\',this);"></i>')."</td>";
		$echo .= "<td class='text-center'>".($x["stav_customerinteresting"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-customerinteresting\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-customerinteresting\','.$x["id"].',\'toggle\',this);"></i>')."</td>";

		$echo .= "<td class='text-center'>".($x["stav_volano"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-volano\','.$x["id"].',\'toggle\',this);"></i>' . ((int)$x['pocet_volano'] !== 0 ? ' (' . $x['pocet_volano'] . ')' : '') :'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-volano\','.$x["id"].',\'toggle\',this);"></i>')."</td>";
		$echo .= "<td class='text-center'>".($x["stav_technicak"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-technicak\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-technicak\','.$x["id"].',\'toggle\',this);"></i>')."</td>";
		$echo .= "<td class='text-center'>".($x["stav_nabidka"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-nabidka\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-nabidka\','.$x["id"].',\'toggle\',this);"></i>')."</td>";
		$echo .= "<td class='text-center'>".($x["stav_pojistky"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-pojistky\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-pojistky\','.$x["id"].',\'toggle\',this);"></i>')."</td>";
		$echo .= "<td class='text-center'>".($x["stav_smsobchodni"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-smsobchodni\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-smsobchodni\','.$x["id"].',\'toggle\',this);"></i>')."</td>";

	}
//	$echo .= "<td>";
/*
	if ($x["stav"] == 0)
	{
		$echo .= '<strong class="btn btn-sm btn-static btn-success">Nový</strong>';
	}
	elseif ($x["stav"] == 1)
	{
		$echo .= '<a class="btn btn-sm btn-info" href="'.$C->dir.'zastavy/admin.zastavy-nahled/id/'.$x["id_zastavy"].'/">Založená zástava</strong>';
	}
	elseif ($x["stav"] == -1)
	{
		$echo .= '<strong class="btn btn-sm btn-static btn-danger">Smazaná</strong>';
	}
	else
	{
		//$echo .= "<a class=\"btn btn-default btn-sm\" onclick=\"return confirm('Opravdu prodloužit?');\" href='{$C_dir}zastavy/admin.zastavy/prodlouzit/{$x["id"]}'><i class=\"fa fa-clock-o fa-fw\"></i> Prodloužit</a>";
	}
*/
//	$echo .= "</td>";
	$echo .=  "<td>";
if (prava('admin-poptavky'))
{
	$echo .=  '
				<div class="dropdown">
						<button class="btn btn-default btn-sm dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">Možnosti <span class="caret"></span></button>
			  			<ul class="dropdown-menu dropdown-menu-right">';

							$echo .= '
							<li class="dropdown-header">Odpověďět</li>';
							$echo .= "<li><a class=\"\" href='{$C_dir}poptavky/admin.poptavky/detail/{$x["id"]}/'><i class=\"fa fa-envelope-o fa-fw\"></i> Odeslat zprávu</a></li>";
							$echo .= "<li><a class=\"\" href='{$C_dir}poptavky/admin.poptavky/smsobchodni/{$x["id"]}/'><i class=\"fa fa-comment-o fa-fw\"></i> ".l('Odeslat obchodní SMS')."</a></li>";
							$echo .= "<li><a class=\"\" href='{$C_dir}poptavky/admin.poptavka/edit/{$x["id"]}/'><i class=\"fa fa-edit fa-fw\"></i> ".l('Upravit')."</a></li>";
						//	$echo .= "<li><a class=\"\" href='{$C_dir}poptavky/admin.poptavky-poznamka/detail/{$x["id"]}/'><i class=\"fa fa-info fa-fw\"></i> ".l('Přidat poznámku')."</a></li>";

						$echo .= '<li class="divider"></li>
							<li class="dropdown-header">'.l('Zástava').'</li>';
						/*
						if ($x['id_zastavy']>0)
						{
							$echo .= '<li><a class="" href="'.$C->dir.'zastavy/admin.zastavy-nahled/id/'.$x["id_zastavy"].'/"><i class=\"fa fa-eye fa-fw\"></i> Založená zástava</strong></li>';
						}
						*/
						$echo .= "<li><a class=\"\" href='{$C_dir}zastavy/admin.zastavy-edit/?poptavka={$x["id"]}'><i class=\"fa fa-edit fa-fw\"></i> ".l('Vytvořit zástavu')."</a></li>";


						$echo .= '<li class="divider"></li>
							<li class="dropdown-header">'.l('Zrušení poptávky').'</li>';

						foreach($C->poptavky_stav as $k=>$v)
						{
							//chceme jen nove typy kodu, nepotrebne neukazeme
							if(in_array($k,array(1,0,-1,-4,-5),true))
							{
								continue;
							}

							$echo .= "<li><a href='javascript:deleteLine(\"poptavky{$k}\",{$x["id"]});'><i class=\"fa fa-trash-o fa-fw\"></i> ".$v."</a></li>";
						}

						/*
						$echo .= "<li><a href='javascript:deleteLine(\"poptavky-text\",{$x["id"]});'><i class=\"fa fa-trash-o fa-fw\"></i> ".l('Nemáme zájem (skrýt + poslat e-mail)')."</a></li>";
						$echo .= "<li><a href='javascript:deleteLine(\"poptavky-duplicita\",{$x["id"]});'><i class=\"fa fa-trash-o fa-fw\"></i> ".l('Duplicitní')."</a></li>";
						$echo .= "<li><a href='javascript:deleteLine(\"poptavky-nemaauto\",{$x["id"]});'><i class=\"fa fa-trash-o fa-fw\"></i> ".l('Nemá auto')."</a></li>";
						$echo .= "<li><a href='javascript:deleteLine(\"poptavky-neexistuje\",{$x["id"]});'><i class=\"fa fa-trash-o fa-fw\"></i> ".l('Neexistující kontakt')."</a></li>";
						*/

						$echo .=  "</ul>
				</div>
				";
	}
	$echo .=  "</td></tr>";


	/**
	 *
	 * MOBIL
	 *
	 *
	 */
	$echoXS .= '
	<div class="panel panel-'.$addStav.'">
		<div class="panel-heading" role="tab" id="headX'.$x['id'].'">
		<h4 class="panel-title"><a role="button" class="collapsed" data-toggle="collapse" data-parent="#accordion" href="#collX'.$x['id'].'" aria-expanded="true" aria-controls="collX'.$x['id'].'"><strong>'.$x["id"].' | '.mb_substr($x["jmeno"],0,35,'utf-8').'</strong> | '.$x["vuz"].' | '.$x["vuz_najeto"].' | '.$x["vuz_vyroba"].'</a></h4>
	    </div>
	<div id="collX'.$x['id'].'" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headX'.$x['id'].'">
		<div class="panel-body">
       <table class="table">
		<tbody>
        ';


			$echoXS .= "<tr><td>ID</td><td> {$x["id"]} </td></tr>";
			$echoXS .= "<tr><td>Pobočka</td><td> {$x["pobocka"]} </td></tr>";
			$echoXS .= "<tr><td>Přidané</td><td> ".predkdy_dny($x["date_add"],1)." </td></tr>";
			$echoXS .= "<tr><td>Jméno</td><td> ".$x["jmeno"]." </td></tr>";

			if (prava('admin-poptavky'))
			{
				$echoXS .= "<tr><td>E-mail</td><td> <a href='mailto:{$x["email"]}'>{$x["email"]}</a> </td></tr>";
				$echoXS .= "<tr><td>Telefon</td><td><a href=\"tel:{$x["telefon"]}\">{$x["telefon"]}</a> </td></tr>";
			}

			$echoXS .= "<tr><td>Vůz</td><td> ".$x["vuz"]." </td></tr>";
			$echoXS .= "<tr><td>Vůz - najeto</td><td> ".$x["vuz_najeto"]." </td></tr>";
			$echoXS .= "<tr><td>Vůz - výroba</td><td> ".$x["vuz_vyroba"]." </td></tr>";
			$echoXS .= "<tr><td>Vůz - chce peněz</td><td> ".(is_numeric($x["vuz_castka"])?number_format($x["vuz_castka"],0,',',' ').'&nbsp;'.($x['stat']=='cz'?'Kč':($x['stat']=='sk'?'EUR':'')):$x["vuz_castka"])." </td></tr>";

			if (prava('admin-poptavky'))
			{
				$echoXS .= "<tr><td>Způsob výkupu</td><td><small>".($x["zpusob_zastavy"])."</small></td></tr>";
				$echoXS .= "<tr><td>Uvítací SMS</td><td class=''>".($x["stav_sms1"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-sms1\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-sms1\','.$x["id"].',\'toggle\',this);"></i>')."</td></tr>";
				$echoXS .= "<tr><td>Uvítací e-mail</td><td class=''>".($x["stav_email1"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-email1\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-email1\','.$x["id"].',\'toggle\',this);"></i>')."</td></tr>";
				$echoXS .= "<tr><td>Odpovězeno?</td><td class=''>".($x["stav_odpovezeno"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-odpoved\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-odpoved\','.$x["id"].',\'toggle\',this);"></i>')."</td></tr>";
				$echoXS .= "<tr><td>Voláno?</td><td class=''>".($x["stav_volano"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-volano\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-volano\','.$x["id"].',\'toggle\',this);"></i>')."</td></tr>";
				$echoXS .= "<tr><td>Máme techničák?</td><td class=''>".($x["stav_technicak"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-technicak\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-technicak\','.$x["id"].',\'toggle\',this);"></i>')."</td></tr>";
				$echoXS .= "<tr><td>Poslaná nabídka?</td><td class=''>".($x["stav_nabidka"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-nabidka\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-nabidka\','.$x["id"].',\'toggle\',this);"></i>')."</td></tr>";
				$echoXS .= "<tr><td>Pojistky?</td><td class=''>".($x["stav_pojistky"]==1?'<i class="fa fa-check-circle text-success fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-pojistky\','.$x["id"].',\'toggle\',this);"></i>':'<i class="fa fa-times-circle text-danger fa-lg click" aria-hidden="true" onclick="changeLine(\'poptavka-pojistky\','.$x["id"].',\'toggle\',this);"></i>')."</td></tr>";
			}
			$echoXS .= "<tr><td>Stav</td><td> ";

			if ($x["stav"] == 0)
			{
				$echoXS .= '<strong class="btn btn-sm btn-static btn-success">'.l('Nový').'</strong>';
			}
			elseif ($x["stav"] == 1)
			{
				$echoXS .= '<strong class="btn btn-sm btn-static btn-info">'.l('Založená zástava').'</strong>';
			}
			elseif ($x["stav"] == -1)
			{
				$echoXS .= '<strong class="btn btn-sm btn-static btn-danger">'.l('Smazaná').'</strong>';
			}

			$echoXS .= "</td></tr>";

			$echoXS .= "</tbody></table>";
			if (prava('admin-poptavky'))
			{
				$echoXS .=  '<ul class="dropdown-menu" style="position: static; display: block !important; float: none; ">';
				$echoXS .= '<li class="dropdown-header">'.l('Poptávky').'</li>';

				$echoXS .= "<li><a class=\"\" href='{$C_dir}poptavky/admin.poptavky/detail/{$x["id"]}/'><i class=\"fa fa-envelope-o fa-fw\"></i> ".l('Odeslat zprávu')."</a></li>";
				$echoXS .= "<li><a class=\"\" href='{$C_dir}poptavky/admin.poptavky/smsobchodni/{$x["id"]}/'><i class=\"fa fa-comment-o fa-fw\"></i> ".l('Odeslat obchodní SMS')."</a></li>";
				$echoXS .= "<li><a class=\"\" href='{$C_dir}poptavky/admin.poptavky-poznamka/detail/{$x["id"]}/'><i class=\"fa fa-info fa-fw\"></i> ".l('Přidat poznámku')."</a></li>";

				$echoXS .= '<li class="divider"></li>
						<li class="dropdown-header">Výkupy</li>';
				$echoXS .= "<li><a class=\"\" href='{$C_dir}zastavy/admin.zastavy-edit/?poptavka={$x["id"]}'><i class=\"fa fa-edit fa-fw\"></i> ".l('Vytvořit zástavu')."</a></li>";
				$echoXS .= "<li><a href='javascript:deleteLine(\"poptavky\",{$x["id"]});'><i class=\"fa fa-trash-o fa-fw\"></i> ".l('Nemá zájem (skrýt)')."</a></li>";
				$echoXS .= "<li><a href='javascript:deleteLine(\"poptavky-text\",{$x["id"]});'><i class=\"fa fa-trash-o fa-fw\"></i> ".l('Nemáme zájem (skrýt + poslat e-mail)')."</a></li>";

				if (prava('admin-poptavky-delete'))
				{
					$echoXS .= '<li class="divider"></li>';
					$echoXS .= "<li><a href='javascript:deleteLine(\"poptavky-hard\",{$x["id"]});'><i class=\"fa fa-trash fa-fw\"></i> ".l('Smazat navždy')."</a></li>";
				}
				$echoXS .=  "</ul>";
			}

		$echoXS .= '</div></div></div>';







} echo $echo;?>

</tbody>
</table>
</div>

<div class="panel-group visible-xs-block hidden-sm hidden-md hidden-lg" id="accordion" role="tablist" aria-multiselectable="true">
<?php echo $echoXS; ?>
</div>

<?php

		echo coreStrankovani($sqlAllCount,$page,$STR_view_number);
		echo '</form>';

		}
	}

}
else
{
	redirect($C->dir.ERROR_PAGE.'/forbidden');
}