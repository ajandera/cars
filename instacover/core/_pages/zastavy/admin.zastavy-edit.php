<?php

/**
 *
 * 2017-09-21
 * + vlastni texty do smlouvy
 *
 * 2017-09-13
 * + obchfirma
 *
 */


if(prava("admin-zastavy"))
{


	$DATA = false;
	$addWHERE = "";

	if (!prava("admin-zastavy-vsechnypobocky"))
	{
		if (!empty($core->loggedUser('pobocka')))
		{
			$addWHERE = "AND FIND_IN_SET(pobocka,'".clearUnsafeChars($INFO["pobocka"])."') ";
		}
	}

	if (prava("admin-zastavy-jenmoje"))
	{
		$subHeading = '<span class="label label-warning">'.l('Jen mé').'</span>';
		$addWHERE .= "AND id_user = '".clearUnsafeChars($INFO["id"])."' ";
	}

	if (!isset($_aid))
	{
		$addNew = true;

		if (isset($_GET['poptavka']) && is_numeric($_GET['poptavka']))
		{
			$sql = "SELECT * FROM {$C->db_prefix}poptavky WHERE id = '{$_GET['poptavka']}' $addWHERE LIMIT 1";
			$sql_query = mysql_query($sql);
			if(mysql_num_rows($sql_query)>0)
			{
				$_POST = coreDBfetch($sql_query);
				$_POST['auto_model'] = $_POST['vuz'];
				$_POST['auto_rokvyroby'] = $_POST['vuz_vyroba'];
				$_POST['auto_km'] = $_POST['vuz_najeto'];
				$_POST['addr_stat'] = $_POST['stat'];
			}
		}
	}
	else
	{
		$addNew = false;

		if (prava("admin-autobazar"))
		{
			$addWHERE .= " AND (stav_podpis < 3) ";
		}

		if (!empty($core->loggedUser('stat_limit')))
		{
			$addWHERE .= " AND FIND_IN_SET(addr_stat,'".$core->loggedUser('stat_limit')."') ";
			//$addWHEREArray[] = clearUnsafeChars($core->loggedUser('stat_limit'));
		}

		$sql = "SELECT * FROM {$C->db_prefix}zastavy WHERE id = '{$_aid}' $addWHERE LIMIT 1";
		$sql_query = mysql_query($sql);
		if(mysql_num_rows($sql_query)==0)
		{
			echo box_info(l("Záznam nenalezen. Chyba."),2);$addNew = true;
		}
		else
		{
			$_POST = coreDBfetch($sql_query);
			if ($_action =="copy")
			{
				$addNew = true;
			}
		}
	}



	if ($addNew)
	{
		if ($_action =="copy")
		{
			$subHeading = '<span class="label label-success">'.l('Kopie').'</span>';
		}
		else
		{
			$subHeading = '<span class="label label-success">'.l('Nová').'</span>';
		}

	}
	else
	{
		$DATA = zastavyGet($_aid);
		$subHeading = '<span class="label label-warning">'.l('Úprava').'</span>';
	}

	echo '<div class="page-header"><h1>'.l('Vozidlo').' <small>'; echo $subHeading; echo'</small></h1></div>';
	echo '<form action="" method="post" class="form-horizontal" enctype="multipart/form-data" id="form">';

	if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']))
	{
		echo createInputHidden('redir',$_SERVER['HTTP_REFERER']);
	}

	echo '<div>'; //tabs

		echo '<ul class="nav nav-tabs" role="tablist">';
		echo '	<li role="presentation" class="active"><a href="#tab1" aria-controls="tab1" role="tab" data-toggle="tab">'.l('Základní smlouva').'</a></li>';
		echo '	<li role="presentation"><a href="#tab4" aria-controls="tab4" role="tab" data-toggle="tab">'.l('Přílohy').'</a></li>';

		if (!prava('admin-autobazar'))
		{
			echo '	<li role="presentation"><a href="#tab2" aria-controls="tab2" role="tab" data-toggle="tab">'.l('Dodatečné texty u smlouvy').'</a></li>';
			echo '	<li role="presentation"><a href="#tab3" aria-controls="tab3" role="tab" data-toggle="tab">'.l('Prodej vozidla').'</a></li>';
		}

		echo '</ul>';

		echo '<div class="tab-content paddingT3">';
		echo '<div role="tabpanel" class="tab-pane active" id="tab1">';

					if (prava('zastavy-jenfinancovani'))
					{
						unset($C->zastavy_typ[1]);
					}

					if (prava('admin-autobazar'))
					{
						unset($C->zastavy_typ[1]);
						unset($C->zastavy_typ[2]);
						unset($C->zastavy_typ[3]);
						unset($C->zastavy_typ[4]);
						unset($C->zastavy_typ[5]);
					}

					echo createInputBlockStart(l("Typ případu"),'panel-danger');
					echo createInputHidden("id_zakaznik",$_POST["id_zakaznik"]);
					echo createInputHidden("id_poptavka",(isset($_GET['poptavka']) && is_numeric($_GET['poptavka'])?$_GET['poptavka']:$_POST['id_poptavka']));
					echo createInputHidden("cena_mena",(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK'));
					echo createInputHidden("source",$_POST["source"]);
					echo createInputHidden("source_hash",$_POST["source_hash"]);
					echo createInputHidden("smlouva_version",$_POST["smlouva_version"]);
					echo createInputHidden("smlouva_typ_generace",$_POST["smlouva_typ_generace"]);

					if ($addNew)
					{

						echo '
						<div class="row marginB1">
						<div class="col-xs-5">
							&nbsp;
						</div>
						<div class="col-xs-7">
						<div class="btn-group" data-toggle="buttons">
						  <label class="btn btn-danger active"><input type="radio" name="zakazniktyp" value="novy" checked> '.l('Nový zákazník').'</label>
						  <label class="btn btn-danger"><input type="radio" name="zakazniktyp" value="stavajici"> '.l('Stávající zákazník (vracející se)').'</label>
						</div>
						</div>
						</div>
						';

						echo '<div id="move_zakaznik_container1">';
						echo '<div id="move_zakaznik">';
						echo createInputText("prijmeni",$DATA["prijmeni"],'<span id="prijmeni-popis">'.l("Příjmení / Firma").'</span>',"horizontal",true,"form-control required zakaznik form-control-lg");
						echo createInputInfo('&nbsp;','<div id="zakaznikInfo">'.l('Vyhledávání mezi stávajícími zákazníky').' ('.l('Hledat lze podle IČ, DIČ, RČ, e-mailu, telefonu a poznámek u klienta.').')</div>',"horizontal",false,'marginB2 small',true);
						echo '</div>';
						echo '</div>';

						echo createInputSelect("addr_stat",$C->staty,$_POST["addr_stat"],l("Stát"),"horizontal",true,false);

						$firmyObsolete = array();
						foreach ($C->obchfirma as $k=>$v)
						{
							if ($v['id']==1)
							{
								continue;
							}
							$firmyObsolete[$k]=$v['label'];
						}

						echo createInputSelect("obchfirma",$firmyObsolete,$_POST["obchfirma"],l("Smlouvu uzavřít za"),"horizontal",true,false,"form-control",false);

						echo createInputSelect("typ",$C->zastavy_typ,$_POST["typ"],l("Typ případu"),"horizontal",true,false,"form-control",false);
						echo createInputInfo(l("Zakládá"),(!empty($core->loggedUser('spolecnost'))?$core->loggedUser('spolecnost').', ':'').$core->loggedUser('jmeno').' '.$core->loggedUser('prijmeni'),"horizontal",false,'lead marginB0',true);
					}
					else
					{
						$firmyObsolete = $_POST["obchfirma"];
						echo createInputHidden('obchfirma',$firmyObsolete);
						echo createInputHidden("typ",$_POST["typ"]);
						echo createInputHidden("addr_stat",$DATA["addr_stat"]);
						echo createInputInfo(l("Země případu"),$C->staty[$DATA["addr_stat"]]['label'],"horizontal",false,'lead marginB0',true);
						echo createInputInfo(l("Typ případu"),$C->zastavy_typ[$_POST["typ"]]['label'],"horizontal",false,'lead marginB0',true);
					}

					echo '<div class="wrap_autobazar">';
					echo createInputSelect("provize",$C->zastavy_provize,$_POST["provize"],l("Výše provize autobazaru"),"horizontal",true,l('Vyberte'));
					echo '</div>';

					if (prava('admin-zastavy-vsechnypobocky'))
					{
						echo createInputSelect("pobocka",$C->pobocky,$_POST["pobocka"],l("Spadá pod pobočku"),"horizontal",true,l('Vyberte'),"form-control input-sm",false,false);
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

						echo createInputSelect("pobocka",$C->pobocky,($addNew && empty($_POST["pobocka"])?$core->loggedUser('pobocka_pref'):$_POST["pobocka"]),l("Spadá pod pobočku"),"horizontal",true,l('Vyberte'),"form-control input-sm",false,false);
					}

					echo createInputBlockEnd();


			echo '<div class="row"><div class="col-sm-6">';

				echo createInputBlockStart(l("Vozidlo"),"panel-info");

					echo createInputText("auto_vin",$_POST["auto_vin"],l("VIN"),"horizontal",true,"form-control input-sm",false,false,false,50,false,'<span style="position: absolute; top: 3px; right: 21px;" class=""><a href="javascript:getC4CVINdecoder(this);" class="btn btn-xs btn-warning" id="getByVIN">'.l('Nahrát data').'</a></span>');

					echo createInputSelect("auto_typ",$C->zastavy_auto_typ,$_POST["auto_typ"],l("Typ vozidla"),"horizontal",true,false,"form-control input-sm");
					echo createInputText("auto_znacka",$_POST["auto_znacka"],l("Tovární značka"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_model",$_POST["auto_model"],l("Typ/model"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_rokvyroby",$_POST["auto_rokvyroby"],l("Rok výroby"),"horizontal",($addNew?true:false),"form-control input-sm digits");
					echo createInputText("auto_registrace",$_POST["auto_registrace"],l("1. Registrace"),"horizontal",true,"form-control input-sm datum");
					echo createInputText("auto_spz",$_POST["auto_spz"],l("SPZ vozidla"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_km",$_POST["auto_km"],l("Najeto km"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_barva",$_POST["auto_barva"],l("Barva"),"horizontal",true,"form-control input-sm");

					echo createInputTextarea("auto_stav",$_POST["auto_stav"],l("Technický stav"),"horizontal",3,false,"form-control input-sm");
					echo createInputText("auto_predchozichmajitelu",$_POST["auto_predchozichmajitelu"],l("Počet předchozích majitelů"),"horizontal",false,"form-control input-sm");
					echo createInputText("auto_datumposledniregistrace",$_POST["auto_datumposledniregistrace"],l("Datum poslední registrace vozidla"),"horizontal",false,"form-control input-sm date datum");


					echo createInputSelect("auto_dovoz",$C->zastavy_auto_dovoz,(isset($_POST['auto_dovoz'])?$_POST['auto_dovoz']:'0'),l("Dovoz ze zahraničí"),"horizontal",true,false,"form-control input-sm");
					echo createInputText("auto_datumdovoz",$_POST["auto_datumdovoz"],l("Datum dovozu ze zahraničí"),"horizontal",false,"form-control input-sm date datum");

					echo createInputTextarea("auto_vybava",$_POST["auto_vybava"],l("Výbava"),"horizontal",3,false,"form-control input-sm");
					echo createInputText("auto_havarovane",$_POST["auto_havarovane"],l("Havarované"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_cislotechnprukazu",$_POST["auto_cislotechnprukazu"],l("Číslo velkého technického průkazu"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_datumstk",$_POST["auto_datumstk"],l("platnost STK do"),"horizontal",true,"form-control input-sm datum");
					echo createInputText("auto_klicu",$_POST["auto_klicu"],l("Počet klíčů"),"horizontal",true,"form-control input-sm digits");
					echo createInputText("auto_vykon",$_POST["auto_vykon"],l("Výkon (kW)"),"horizontal",false,"form-control input-sm digits");
					echo createInputText("auto_doklady",$_POST["auto_doklady"],l("Doklady"),"horizontal",true,"form-control input-sm");

					echo createInputSelect("auto_karoserie",getConfig('bazar-karoserie'),$_POST["auto_karoserie"],l("Typ karoserie"),"horizontal",($addNew?true:false),l('Vyberte'),"form-control input-sm");
					echo createInputSelect("auto_palivo",getConfig('bazar-palivo'),$_POST["auto_palivo"],l("Palivo"),"horizontal",($addNew?true:false),l('Vyberte'),"form-control input-sm");
					echo createInputSelect("auto_prevodovka",getConfig('bazar-prevodovka'),$_POST["auto_prevodovka"],l("Převodovka"),"horizontal",($addNew?true:false),l('Vyberte'),"form-control input-sm");

					echo '<div class="wrap_autobazar_hide">';
					echo createInputText("auto_predpnajezd",$_POST["auto_predpnajezd"],l("Předpokládaný roční nájezd"),"horizontal",false,"form-control input-sm");
					echo createInputSelect("auto_prevodvozu",$C->dropPrevodvozu,$_POST["auto_prevodvozu"],l("Převod vozu"),"horizontal",true,l('Vyberte'),"form-control input-sm");
					echo createInputOptions('auto_1majitel',$C->dropAnoNe,(isset($_POST['auto_1majitel'])?$_POST['auto_1majitel']:0),l('První majitel?'),'horizontal');
					echo createInputOptions('vtp_zapujcen_agentura',$C->dropAnoNe,(isset($_POST['vtp_zapujcen_agentura'])?$_POST['vtp_zapujcen_agentura']:0),l('Zapůjčení VTP'),'horizontal');
					echo createInputText("vtp_zapujcen_datum",($addNew?'':$_POST["vtp_zapujcen_datum"]),l("Datum zapůjčení VTP"),"horizontal",false,"form-control input-sm datum dateISO");
					echo '</div>';

					echo createInputText("auto_objem",$_POST["auto_objem"],l("Zdvihový objem v cm3"),"horizontal",true,"form-control input-sm digits");
					echo createInputText("auto_hmotnost",$_POST["auto_hmotnost"],l("Provozní hmotnost v kg"),"horizontal",true,"form-control input-sm digits");
					echo createInputText("auto_kodnomenklatury",$_POST["auto_kodnomenklatury"],l("Kód nomenklatury"),"horizontal",false,"form-control input-sm digits",false,false,false,50,false,'<span style="position: absolute; top: 3px; right: 21px;" class=""><a href="/zastavy/admin.nomenklatury/" target="_blank" class="btn btn-xs btn-warning">'.l('Zobrazit nomenklatury').'</a></span>');


					echo '<div class="wrap_stat_hu">';
						echo createInputText("auto_cislomotoru",$_POST["auto_cislomotoru"],l("Číslo motoru"),"horizontal",false,"form-control input-sm");
						echo createInputText("auto_cislomalehotp",$_POST["auto_cislomalehotp"],l("Číslo malého TP"),"horizontal",false,"form-control input-sm");
					echo '</div>';

					//	echo createInputText("auto_zmenamajitele",$_POST["auto_zmenamajitele"],"Změna majitele na","horizontal",true,"form-control input-sm");
				echo createInputBlockEnd();

				echo createInputBlockStart(l('Majitel vozidla').' - '.l('pokud se liší od autobazaru'),'panel-default bg-light wrap_autobazar');

					echo '<p>'.l('Vyplňte v případě, kdy není vlastník (prodávající) vozidla autobazar.').'</p>';
					echo createInputText('addr_vlastnik_obchfirma',$_POST['addr_vlastnik_obchfirma'],l('Firma'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_vlastnik_jmeno',$_POST['addr_vlastnik_jmeno'],l('Jméno'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_vlastnik_prijmeni',$_POST['addr_vlastnik_prijmeni'],l('Přijmení'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_vlastnik_narozeniic',$_POST['addr_vlastnik_narozeniic'],l('Datum narození / IČ'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_vlastnik_zastoupeno',$_POST['addr_vlastnik_zastoupeno'],l('Firma zastoupena'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_vlastnik_adresa',$_POST['addr_vlastnik_adresa'],l('Ulice'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_vlastnik_mesto',$_POST['addr_vlastnik_mesto'],l('Město'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_vlastnik_psc',$_POST['addr_vlastnik_psc'],l('PSČ'),"horizontal",false,"form-control input-sm");
				//	echo createInputText('addr_vlastnik_stat',$_POST['addr_vlastnik_stat'],l('Stát'),"horizontal",false,"form-control input-sm");

				echo createInputBlockEnd();


				echo createInputBlockStart(l("Půjčka"),"panel-warning");

					echo '<div class="wrap_autobazar_hide">';

					echo createInputOptions('jezdi',$C->dropAnoNe,(isset($_POST['zpusob_zastavy'])?($_POST['zpusob_zastavy']=="klasický"?0:1):($addNew?1:$_POST["jezdi"])),l('Jezdí?'),'horizontal');

					echo createInputText("cena",$_POST["cena"],l("Kupní cena"),"horizontal",true,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					echo createInputText("cena_slovy",$_POST["cena_slovy"],l("Kupní cena (slovy)"),"horizontal",true,"form-control input-sm");

					echo createInputCheckbox('vyplaceno_hotove_check',1,($_POST['vyplaceno_hotove']>0?1:0),l('Vyplaceno hotově'),'horizontal');
					echo '<div class="vyplaceno_hotove_wrap">';
					echo createInputText("vyplaceno_hotove",$_POST["vyplaceno_hotove"],l('Kolik dostal klient hotově?'),"horizontal",true,"form-control input-sm");
					echo '</div>';
					echo createInputCheckbox('vyplaceno',1,(isset($_POST['vyplaceno'])?$_POST['vyplaceno']:0),l('Vyplaceno na převodem / kartou'),'horizontal');
					echo '<div class="vyplaceno_kam">';
					echo createInputText("vyplaceno_prevod",$_POST["vyplaceno_prevod"],l('Kolik dostal klient převodem?'),"horizontal",true,"form-control input-sm");
					echo createInputText("vyplaceno_kam",$_POST["vyplaceno_kam"],l('Číslo účtu - klienta pro vyplacení'),"horizontal",false,"form-control input-sm");
					echo '</div>';
					echo '<hr>';
					echo '</div>';

					echo '<div class="wrap_fix">';
					echo createInputText("cena_doplatek",$_POST["cena_doplatek"],l("Výše konečného doplatku"),"horizontal",true,"form-control input-sm number",false,l('Platí se společně s posledním RP, pokud zůstane nevyplněná C4C spočítá doporučenou částku'),false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					echo '</div>';

					echo '<div class="wrap_kauce">';
					echo createInputOptions('kauce_vyplaceno',$C->dropVyplaceno,($addNew?0:$_POST["kauce_vyplaceno"]),l('Jak placena kauce'),'horizontal');
					echo createInputText("kauce",$_POST["kauce"],l("Výše kauce"),"horizontal",true,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					echo createInputText("kauce_slovy",$_POST["kauce_slovy"],l("Výše kauce (slovy)"),"horizontal",true,"form-control input-sm");
					echo '</div>';

					echo createInputSelect("cena_dph",$C->dropDPH,($addNew?2:$_POST["cena_dph"]),l("Kupní cena - DPH"),"horizontal",true,false,"form-control input-sm");
					echo '<hr>';

					$akontace = 30;
					if ($addNew)
					{
						if ($core->loggedUser('kauce_min')!=-1)
						{
							$akontace = (int)$core->loggedUser('kauce_min');
						}
					}
					else
					{
						$userAkontace = getUserByID($_POST["id_user"],'kauce_min');
						if ($userAkontace!=-1)
						{
							$akontace = (int)$userAkontace;
						}
					}

					echo '<div class="wrap_autobazar">';
					//	echo createInputText("cena_akontacefin_count",0,l("Financovaná cena vozidla"),"horizontal",true,"form-control input-sm digits",false,false,' ',30,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					//	echo createInputText("cena_akontace_count",0,l("Akontace"),"horizontal",true,"form-control input-sm digits",false,false,' ',30,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					if (!$addNew && $_POST["cena_akontace"]>0 && $_POST["cena_akontace_financovana"]==0)
					{
						//prepocitame, protoze se predelaval system
						$_POST["cena_akontace_financovana"] = $x["cena"]-$x["kauce"];
						$_POST["cena_akontace_akontace"] = $x["kauce"];
					}
					echo createInputText("cena_akontace_financovana",$_POST["cena_akontace_financovana"],l("Financovaná cena vozidla"),"horizontal",true,"form-control input-sm digits",false,false,' ',30,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					echo createInputText("cena_akontace_akontace",$_POST["cena_akontace_akontace"],l("Akontace"),"horizontal",true,"form-control input-sm digits",false,false,' ',30,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');

					echo createInputText("cena_akontace",$_POST["cena_akontace"],l("Výše akontace (%), min %procent%!",array('%procent%'=>$akontace.'%')),"horizontal",true,"form-control input-sm digits",false,false,' max="100" min="'.$akontace.'" readonly ',3,false,'<span style="position: absolute; top: 5px; right: 21px;" class="x">%</span>');
					echo '<hr>';
					echo '</div>';

					if (empty($DATA['splatky']))
					{
						echo '<div class="wrap_autobazar">';
						echo createInputCheckbox('rezervacni_poplatek_manual',1,($_POST["rezervacni_poplatek"]>0?1:0),l('Ruční nastavení rezervačního poplatku'),'horizontal');
						echo '</div>';
					}

					echo '<div class="'.($addNew?'wrap_zast wrap_rezervacni':($_POST["rezervacni_poplatek"]>0?'wrap_zast wrap_rezervacni':'wrap_zast wrap_rezervacni')).'">';
					echo createInputText("rezervacni_poplatek",$_POST["rezervacni_poplatek"],l("Rezervační poplatek"),"horizontal",true,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					echo '</div>';

					echo '<div class="wrap_zast">';
					//echo createInputText("dnu_odpredani",($addNew?0:$_POST["dnu_odpredani"]),"Počet dnů od předání","horizontal",true,"form-control input-sm");
					echo createInputText("rezervacni_poplatek_slovy",$_POST["rezervacni_poplatek_slovy"],l("Rezervační poplatek (slovy)"),"horizontal",true,"form-control input-sm");
					echo createInputText("urok",$_POST["urok"],l("Měsíční úrok"),"horizontal",true,"form-control input-sm",false,false,' readonly="readonly" ',5,false,'<span style="position: absolute; top: 5px; right: 21px;" >%</span>');
					echo '</div>';
					echo createInputSelect("rezervacni_poplatek_dph",$C->dropDPH_rezpoplatek,$_POST["rezervacni_poplatek_dph"],l("Rezervační poplatky - DPH"),"horizontal",true,false,"form-control input-sm",false,false,false,false,false,'<div id="rezervacni_poplatek_dph_hard" class="hidn">0% DPH</div>');
					echo '<hr>';

					echo createInputText("datum_podpisukup",($addNew?date('Y-m-d'):$_POST["datum_podpisukup"]),l("Datum podpisu"),"horizontal",false,"form-control input-sm datum dateISO");
					if (!prava('admin-autobazar'))
					{
						if($addNew)
						{
							echo createInputInfo(l('Schválení smlouvy'),l('Nelze rovnou schválit novou smlouvu, uložte jí'),'horizontal',false,'alert alert-danger');
						}
						else
						{
							if(prava('admin-zastavy-schvaleni'))
							{
								if($_POST['id_user'] == $core->loggedUser('id') || ($_POST['id_user_exportto']>0 && $_POST['id_user_exportto']==$core->loggedUser('id')))
								{
									echo createInputInfo(l('Schválení smlouvy'),l('Nemůžete schválit smlouvu sám sobě'),'horizontal',false,'alert alert-danger');
								}
								else
								{
									if($_POST['stav_schvaleni'] == -1 && prava(3))
									{
										echo createInputOptions('stav_schvaleni',array(-1=>l('Zamítnout'),0=>l('Čeká'),1=>l('Schválená')),(isset($_POST['stav_schvaleni']) && $_POST['stav_schvaleni']<0?-1:(isset($_POST['stav_schvaleni']) && $_POST['stav_schvaleni']>0?1:0)),l('Schválení smlouvy'),'horizontal');
									}
									elseif($_POST['stav_schvaleni'] == -1 && !prava(3))
									{
										echo createInputInfo(l('Schválení smlouvy'),l('Smlouva byla zamítnuta, znovu jí může povolit pouze uživatel s admin právy.'),'horizontal',false,'alert alert-danger');
									}
									else
									{
										if($_POST['stav_podpis']==0)
										{
											echo createInputOptions('stav_schvaleni',array(-1=>l('Zamítnout'),0=>l('Čeká'),1=>l('Schválená')),(isset($_POST['stav_schvaleni']) && $_POST['stav_schvaleni']<0?-1:(isset($_POST['stav_schvaleni']) && $_POST['stav_schvaleni']>0?1:0)),l('Schválení smlouvy'),'horizontal');
										}
									}
								}
							}
							else
							{
								if($_POST['stav_schvaleni'] == -1)
								{
									echo createInputInfo(l('Schválení smlouvy'),l('Smlouva byla zamítnuta'),'horizontal',false,'alert alert-danger');
								}
								elseif($_POST['stav_schvaleni'] == 0)
								{
									echo createInputInfo(l('Schválení smlouvy'),l('Na schválení smlouvy nemáte oprávnění'),'horizontal',false,'alert alert-info');
								}
								elseif($_POST['stav_schvaleni'] >= 1)
								{
									echo createInputInfo(l('Schválení smlouvy'),l('Smlouva je schválená'),'horizontal',false,'alert alert-success');
								}
							}

							if($_POST['stav_schvaleni'] < 0)
							{
								if($USER = getUserByID($_POST['stav_schvaleni']))
								{
									echo createInputInfo(l('Smlouvu zamítl'),$USER['prijmeni'].' '.$USER['jmeno'],'horizontal',false,'alert alert-danger marginB0');
								}
							}
							elseif($_POST['stav_schvaleni'] > 0)
							{
								if($USER = getUserByID($_POST['stav_schvaleni']))
								{
									echo createInputInfo(l('Smlouvu schválil'),$USER['prijmeni'].' '.$USER['jmeno'],'horizontal',false,'alert alert-success marginB0');
								}
							}

							if($_POST['stav_schvaleni']==1)
							{
								echo createInputCheckbox('stav_podpis',1,(isset($_POST['stav_podpis'])?$_POST['stav_podpis']:0),l('Smlouva podepsaná'),'horizontal');
							}
							else
							{
								echo createInputInfo(l('Podpis smlouvy'),l('Nelze podepsat smlouvu, smlouva musí být schválená.'),'horizontal',false,'alert alert-danger');
							}
						}

						echo createInputCheckbox('stav_fakturaprijata',1,(isset($_POST['stav_fakturaprijata'])?$_POST['stav_fakturaprijata']:0),l('Máme od klienta přijatou fakturu?'),'horizontal');
					}

					if(prava(3))
					{
						echo createInputText("datum_splatnostfakturace_override_day",$_POST["datum_splatnostfakturace_override_day"],l("Den, ke kterému vystavovat doklady"),"horizontal",false,"form-control input-sm digits",false,l('Pokud vyplníte, ignoruje se při generování splátek datum podpisu kupní smlouvy, ale využívá se tento den pro korekci splátek (vystavování faktur). K tomuto dni budou splatnosti dokladů. Datum splatnosti pro vymáhání se tímto neovlivní. To je ovlivněno datumem splatnosti rezervačního poplatku. '),' max="31" ',2,false,false,'digits');
					}
					echo '<hr>';

					if ($addNew)
					{
						$orig = date('Y-m-d');

						$FAKDATE = explode('-',$orig);
						if (isset($FAKDATE[2]))
						{
							$NEWDATE = date('Y-m-d',strtotime('+1 month',strtotime($orig)));
							$NEWDATE = date('Y-m-d',fakturace_correctDay($NEWDATE,$FAKDATE[2]));

							$NEWDATE2 = date('Y-m-d',strtotime('+1 year',strtotime($orig)));
							$NEWDATE2 = date('Y-m-d',fakturace_correctDay($NEWDATE2,$FAKDATE[2]));

							$_POST["datum_rezpoplateksplatnost"] = $NEWDATE;
							$_POST["datum_marneplneni"] = $NEWDATE2;
						}
					}

					echo createInputText("datum_rezpoplateksplatnost",$_POST["datum_rezpoplateksplatnost"],l("Rezervační poplatek splatnost"),"horizontal",false,"form-control input-sm datum dateISO",false,false,false,30,false,($addNew?'<span style="position: absolute; top: 5px; right: 21px;">'.l('automatické').'</span>':''));


					echo '<div class="form-group"><div class="col-sm-offset-5 col-sm-7"><div class="horizontal">';
					echo '<p class="mini">'.l('Splatnost RP je zároveň také datum, ke kterému se bude určovat splatnost každý měsíc tzn. pokud například zadáte datum mm-20, tak pak každý následující měsíc bude splatnost ke 20.dni v měsíci.').'</p>';
					echo '</div> </div> </div>';

					echo createInputText("datum_marneplneni",$_POST["datum_marneplneni"],l("Datum marného uplynutí"),"horizontal",false,"form-control input-sm datum dateISO",false,false,false,30,false,($addNew?'<span style="position: absolute; top: 5px; right: 21px;">'.l('automatické').'</span>':''));

					echo '<hr>';


					echo createInputTextarea("info",$_POST["info"],l('Poznámka / informace (propisuje se do výpisu vozidla)'),"horizontal",3,false,"form-control input-sm");
					echo createInputTextarea("info_long",$_POST["info_long"],l('Poznámka / informace (k obchodnímu případu, např. specifické domluvy s klientem, kde jsou klíče apod.)'),"horizontal",6,false,"form-control input-sm");

					echo '<div class="wrap_autobazar_hide">';
					echo createInputText("cena_hodnotazastavy",$_POST["cena_hodnotazastavy"],l("Tržní cena vozidla"),"horizontal",true,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					echo '</div>';

				echo createInputBlockEnd();

				echo createInputBlockStart(l('Korespondenční adresa').' - '.l('váže se k danému případu'),'panel-default bg-light wrap_autobazar_hide');

					echo createInputText('addr_kor_nazev',$_POST['addr_kor_nazev'],l('Jméno / Firma'),"horizontal",false,"form-control input-sm");
					echo createInputText('adresa_korespondence',$_POST['adresa_korespondence'],l('Ulice'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_kor_mesto',$_POST['addr_kor_mesto'],l('Město'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_kor_psc',$_POST['addr_kor_psc'],l('PSČ'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_kor_stat',$_POST['addr_kor_stat'],l('Stát'),"horizontal",false,"form-control input-sm");

				echo createInputBlockEnd();


				echo '<div class="wrap_autobazar_hide">';
				echo createInputBlockStart(l('Pojištění'));

					echo createInputCheckbox('pojisteni_unas',1,(isset($_POST['pojisteni_unas'])?$_POST['pojisteni_unas']:0),l('Pojištění u nás'),'horizontal');
					echo createInputText('pojisteni_kontrola_zelena',$_POST['pojisteni_kontrola_zelena'],l('Datum konce povinného ručení'),'horizontal',false,'form-control input-sm datum');

					echo createInputText('pojisteni_pojistovna',$_POST['pojisteni_pojistovna'],l('Pojišťovna'),'horizontal',false,'form-control input-sm');
					echo createInputText('pojisteni_cislosmlouvy',$_POST['pojisteni_cislosmlouvy'],l('Číslo smlouvy'),'horizontal',false,'form-control input-sm');
					echo createInputText('pojisteni_vysepojistneho',$_POST['pojisteni_vysepojistneho'],l('Výše pojistného'),'horizontal',false,'digits form-control input-sm');

					echo createInputText('pojisteni_datum_splatnosti',$_POST['pojisteni_datum_splatnosti'],l('Datum splatnosti'),'horizontal',false,'form-control input-sm datum');
					echo createInputText('pojisteni_datum_uhrady',$_POST['pojisteni_datum_uhrady'],l('Datum úhrady'),'horizontal',false,'form-control input-sm datum');

				echo createInputBlockEnd();

				echo createInputBlockStart(l('Doplňující informace'));

					echo createInputText("auto_mistostani",$_POST['auto_mistostani'],l('Místo stání vozidla'),"horizontal",false,"form-control input-sm");
					echo createInputCheckbox('auto_klic_drzime',1,(isset($_POST['auto_klic_drzime'])?$_POST['auto_klic_drzime']:0),l('Klíč k vozu v našem držení'),'horizontal');
					echo createInputCheckbox('prepsano',1,(isset($_POST['prepsano'])?$_POST['prepsano']:0),l('Přepsáno na nás'),'horizontal');
					echo createInputCheckbox('auto_gps',1,(isset($_POST['auto_gps'])?$_POST['auto_gps']:0),l('GPS sledování ve vozidle'),'horizontal');
					echo createInputCheckbox('stav_fraud',1,(isset($_POST['stav_fraud'])?$_POST['stav_fraud']:0),l('FRAUD (jedná se o podvod)'),'horizontal');

					echo createInputSelect("id_user_exportto",getUsersArray(false,'20,21'),$_POST['id_user_exportto'],l("Smlouvy tisknout na tohoto uživatele"),"horizontal",false,l('Vyberte'),"form-control input-sm selectpicker",false,false,false,' data-live-search="true"');

					//echo createInputSelect("id_user_vymahani",getUsersArray(12),$_POST['id_user_vymahani'],l("Zobrazit této vymáhací agentuře"),"horizontal",false,l('Vyberte'),"form-control input-sm",false,false);
					//echo createInputText("date_vymahani",($addNew?'':(substr($_POST["date_vymahani"],0,4)=='0000'?'':$_POST["date_vymahani"])),l("Datum předání agentuře"),"horizontal",false,"form-control input-sm datum dateISO");

				echo createInputBlockEnd();
				echo '</div>';


			echo '</div><div class="col-sm-6">';

				if ($addNew)
				{
					//new, takže search
					echo '<script type="text/javascript">var zastavaZakaznikEditEmpty = ';
					ob_start();
					require_once($C->root.'/_templates/zakaznikEdit.inc');
					$jsdata = ob_get_contents();
					ob_end_clean();
					echo json_encode($jsdata).';</script>';

					echo '<div id="zakazniEditkHolder">';
					echo $jsdata;
					echo '</div>';

				}
				else
				{
					// only show! link to edit
					echo '<div id="zakazniNahledkHolder">';
					require_once($C->root.'/_templates/zakaznikNahled.inc');
					echo '</div>';

				}

			echo '</div>';
			echo '</div>';



		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane" id="tab4">';


			echo createInputBlockStart('Přílohy, soubory, fotografie');

					echo '<div class="form-group"><label class="col-sm-5 control-label"><div class="wrap_autobazar_hidealways">'.l('Technický průkaz').'</div><div class="wrap_autobazar">'.l('Technický průkaz, OP klienta, ŘP klienta').'</div></label><div class="col-sm-7">';
					echo coreSouboryUploader('technickyprukaz','zastava-tp',$_aid,1,'1/1',true);
					echo '</div></div>';

					echo '<div class="form-group"><label class="col-sm-5 control-label">'.l('Fotografie auta').'</label><div class="col-sm-7">';
					if ($_GET['poptavka']) {
						echo coreSouboryUploader('auto','instacover_poptavky',$_GET['poptavka'],1,'1/1',true);
					} else {
						echo coreSouboryUploader('auto','zastava-auto',$_aid,1,'1/1',true);
					}
					echo '</div></div>';


					echo '<div class="form-group"><label class="col-sm-5 control-label">'.l('Sken podepsané smlouvy').'</label><div class="col-sm-7">';
					echo coreSouboryUploader('smlouva','zastava-smlouva',$_aid,1,'1/1',true);
					echo '</div></div>';

					echo '<div class="form-group"><label class="col-sm-5 control-label"><div class="wrap_autobazar_hidealways">'.l('Kopie pojistné smouvy').'</div><div class="wrap_autobazar">'.l('Předávací protokol vozidla').'</div></label><div class="col-sm-7">';
					echo coreSouboryUploader('pojisteni','zastava-pojisteni',$_aid,1,'1/1',true);
					echo '</div></div>';

				echo createInputBlockEnd();


		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane" id="tab2">';

			echo createInputBlockStart('Přidání vlastního odstavce do KUPNÍ smlouvy');
				echo createInputTextarea("smlouvatext_add_1",$_POST["smlouvatext_add_1"],l("Přidat na konec 1. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_2",$_POST["smlouvatext_add_2"],l("Přidat na konec 2. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_3",$_POST["smlouvatext_add_3"],l("Přidat na konec 3. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_4",$_POST["smlouvatext_add_4"],l("Přidat na konec 4. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_5",$_POST["smlouvatext_add_5"],l("Přidat na konec 5. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_6",$_POST["smlouvatext_add_6"],l("Přidat na konec 6. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_7",$_POST["smlouvatext_add_7"],l("Přidat na konec 7. odstavce"),"horizontal",6,false,"form-control input-sm");
			echo createInputBlockEnd();
/*
			echo createInputBlockStart('Přidání vlastního odstavce do ROZHODČÍ smlouvy');
				echo createInputTextarea("smlouvatext_rozhodci_1",$_POST["smlouvatext_rozhodci_1"],"Přidat na konec 1. odstavce","horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_rozhodci_2",$_POST["smlouvatext_rozhodci_2"],"Přidat na konec 2. odstavce","horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_rozhodci_3",$_POST["smlouvatext_rozhodci_3"],"Přidat na konec 3. odstavce","horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_rozhodci_4",$_POST["smlouvatext_rozhodci_4"],"Přidat na konec 4. odstavce","horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_rozhodci_5",$_POST["smlouvatext_rozhodci_5"],"Přidat na konec 5. odstavce","horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_rozhodci_6",$_POST["smlouvatext_rozhodci_6"],"Přidat na konec 6. odstavce","horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_rozhodci_7",$_POST["smlouvatext_rozhodci_7"],"Přidat na konec 7. odstavce","horizontal",6,false,"form-control input-sm");
			echo createInputBlockEnd();
*/
			echo createInputBlockStart('Přidání vlastního odstavce do NÁJEMNÉ smlouvy');
				echo createInputTextarea("smlouvatext_najem_1",$_POST["smlouvatext_najem_1"],l("Přidat na konec 1. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_2",$_POST["smlouvatext_najem_2"],l("Přidat na konec 2. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_3",$_POST["smlouvatext_najem_3"],l("Přidat na konec 3. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_4",$_POST["smlouvatext_najem_4"],l("Přidat na konec 4. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_5",$_POST["smlouvatext_najem_5"],l("Přidat na konec 5. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_6",$_POST["smlouvatext_najem_6"],l("Přidat na konec 6. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_7",$_POST["smlouvatext_najem_7"],l("Přidat na konec 7. odstavce"),"horizontal",6,false,"form-control input-sm");
			echo createInputBlockEnd();

		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane" id="tab3">';

			echo createInputBlockStart('Prodej');
				echo createInputText('date_naprodej',$_POST['date_naprodej'],l('Datum začátku prodeje auta'),'horizontal',false,'form-control input-sm datum');
				echo createInputInfo("Informace",'Datum začátku prodeje auta se vyplní automaticky v den označení auta "Na Prodej" v možnostech případu.',"horizontal",false,'marginB0 marginT',true);
				echo createInputText('date_prodane',$_POST['date_prodane'],l('Datum prodeje auta'),'horizontal',false,'form-control input-sm datum');
				echo createInputInfo("Informace",'Datum prodeje auta se vyplní automaticky v den označení auta "Prodané" v možnostech případu.',"horizontal",false,'marginB0 marginT',true);
			echo createInputBlockEnd();

			echo createInputBlockStart('Vozidlo prodáváme na těchto místech');
				echo createInputText("prodej_bazar",$_POST["prodej_bazar"],l("Na jakém bazaru je vůz umístěn"),"horizontal",false,"form-control input-sm");
				echo createInputText("prodej_sauto",$_POST["prodej_sauto"],l("SAUTO: Odkaz na inzerát"),"horizontal",false,"form-control input-sm");
				echo createInputText("prodej_bazos",$_POST["prodej_bazos"],l("BAZOŠ: Odkaz na inzerát"),"horizontal",false,"form-control input-sm");
				echo createInputText("prodej_mobilede",$_POST["prodej_mobilede"],l("Mobile.DE: Odkaz na inzerát"),"horizontal",false,"form-control input-sm");
				echo createInputText("prodej_tipcars",$_POST["prodej_tipcars"],l("Tipcars: Odkaz na inzerát"),"horizontal",false,"form-control input-sm");
			echo createInputBlockEnd();

			echo createInputBlockStart('Vozidlo prodané / konečná cena');
				echo createInputText("cena_prodej",$_POST["cena_prodej"],l("Nabízená prodejní cena"),"horizontal",false,"form-control input-sm");
				echo createInputText("cena_finprodej",$_POST["cena_finprodej"],l("Konečná prodejní cena"),"horizontal",false,"form-control input-sm");
			//	echo createInputText("cena_vysledek",$_POST["cena_vysledek"],l("Výsledek obchodu"),"horizontal",false,"form-control input-sm");
				if ($DATA)
				{
					echo createInputText("cena_vysledek_aut",$DATA["cena_vysledek_aut"],l("Výsledek obchodu"),"horizontal",false,"form-control input-sm readonly", false,false,' readonly ');
				}
			echo createInputBlockEnd();


		if ($DATA)
		{
			//sms upominky z logu
			echo createInputBlockStart(l('Historie změny ceny prodeje'));

			if (empty($DATA['cena_prodej_historie']))
			{
				echo l('Žádné změny ceny');
			}
			else
			{
				foreach ($DATA['cena_prodej_historie'] as $x)
				{
					echo '<div class="row">';
					echo '<div class="col-sm-3">';
					echo $x['date_add_label'];
					echo '</div>';
					echo '<div class="col-sm-6">';
					echo number_format($x['info'],2,',',' ').' '.$_POST['cena_mena'];
					echo '</div>';
					echo '</div>';
					echo '<hr>';
				}
			}

			echo createInputBlockEnd();
		}





		echo '</div>';
	echo '</div>';
	echo '</div>'; //tabs


	echo createInputTwoSubmit('sendAction',l('Uložit vozidlo a odejít'),'sendActionAp',l('Aplikovat změny a zůstat'),'','','btn btn-primary btn-lg marginB5 marginT2','btn btn-deafult btn-lg marginB5 marginT2',false,false,'onclick="return confirm(\''.l("Opravdu chcete uložit vozidlo?").'\')"','onclick="return confirm(\''.l("Opravdu chcete uložit vozidlo?").'\')"');
	echo '</form>';

	?>


<script type="text/javascript">
var pobocky2stat = <?php echo json_encode($C->pobocky_staty); ?>;
var stat2pobocky = <?php echo json_encode($C->pobocky); ?>;
var stat2smlouvy = <?php echo json_encode($C->zastavy_smlouva_version); ?>;

$(document).ready(function()
{
   	reCount();
   	vyberZakaznik();
   	$(document).on('change', 'input', function()
   	{
		//console.log("recount");
   		reCount();
	});$(document).on('change', "input[name='zakazniktyp']", function()
   	{
   		vyberZakaznik();
	});
	<?php
		if ($addNew)
		{
		?>
			$('body').on("change","#datum_podpisukup", function()
			{
				var thisDay = $(this).val();

				$.ajax({
				type: "POST",
				url: "/ajax/zastavy/object.ajax.zastavy-new-dates",
    			headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
				data: {datum_podpisukup: thisDay},
				success: function(html){
					if (html=="x")
					{
						$('#datum_rezpoplateksplatnost').val('');
						$('#datum_marneplneni').val('');
					}
					else
					{
						var res = html.split(";",2);
						$('#datum_rezpoplateksplatnost').val(res[0]);
						$('#datum_marneplneni').val(res[1]);
					}
				}
				});
			});

			$('body').on("change","#ico,#narozeniic,#cisloop,#email,#telefon,#auto_vin,#auto_spz", function()
			{
				if($("input[name='zakazniktyp']").length>0)
				{
					var typzak = $("input[name='zakazniktyp']:checked").val();
					if(typzak == 'novy')
					{
						var dataICO = $('#ico').val();
						var dataRC = $('#narozeniic').val();
						var dataOP = $('#cisloop').val();
						var dataEMAIL = $('#email').val();
						var dataTel = $('#telefon').val();
						var dataVIN = $('#auto_vin').val();
						var dataSPZ = $('#auto_spz').val();

						$.ajax({
						type: "POST",
						url: "/ajax/zakaznici/object.search-duplicity",
    					headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
						data: {ico: dataICO,narozeniic:dataRC,cisloop:dataOP,email:dataEMAIL,telefon:dataTel,auto_vin:dataVIN,auto_spz:dataSPZ},
						success: function(html){
							if (html=="x")
							{
								//alert(html);
							}
							else
							{
								//var res = html.split(";",2);
								if(html == '3')
								{
									alert('<?php echo l('BAN: Pozor, zákazník s těmito údaji je v databázi označen jako ZABANOVANÝ!');?>')
								}
								else if(html == '2')
								{
									alert('<?php echo l('Pozor, toto vozidlo už je nebo bylo evidované jako případ. Nejde o vracejícího se zákazníka nebo duplicitu?');?>')
								}
								else if(html == '1')
								{
									alert('<?php echo l('Pozor, zákazníka s těmito údaji jsme našli v databázi, nejedná se o vracejícího zákazníka?');?>')
								}
							}
						}
						});
					}
				}
			});
		<?php
		}
		?>
	$('body').on("change","#cena", function()
	{
		slovy(this,$('#cena_slovy'));
		var cena = Number($("#cena").val());
		var typ = $('#typ').val();

		if($("#cena_akontace_akontace").is(":visible"))
		{
			var akontaceCena = Number($("#cena_akontace_akontace").val());
			var akontaceFinanc = Number($("#cena_akontace_financovana").val());

			if(akontaceCena == 0)
			{
				$('#cena_akontace_financovana').change();
			}
			else if(akontaceFinanc == 0)
			{
				$('#cena_akontace_akontace').change();
			}
			else
			{
				$('#cena_akontace_akontace').change();
			}
		}

		if( typ==10 || typ==11 || typ==12 || typ==13 || typ==20 || typ==30 || typ==31 || typ==32 || typ==33) //autobazar
		{
			$('#cena_hodnotazastavy').val(cena);
		}

	});

	$('body').on("change","#kauce", function()
	{
		slovy(this,$('#kauce_slovy'));
	});

	$('body').on("change","#auto_dovoz", function()
	{
		var value = $(this).val();
		if(value != '0')
		{
			$('#auto_datumdovoz').parent().parent().show();
		}
		else
		{
			$('#auto_datumdovoz').parent().parent().hide();
		}
	});

	$('body').on("change","#cena_akontace_financovana", function()
	{
		if($("#cena_akontace_akontace").is(":visible"))
		{
			var cena = Number($("#cena").val());
			var akontaceFinanc = Number($("#cena_akontace_financovana").val());
			var akontaceCena = (cena-akontaceFinanc);
			$('#cena_akontace_akontace').val(akontaceCena);

			var procenta = roundNumber(100-(akontaceFinanc/(cena/100)),0);
			$('#cena_akontace').val(procenta);
		}
	});

	$('body').on("change","#cena_akontace_akontace", function()
	{
		if($("#cena_akontace_akontace").is(":visible"))
		{
			var cena = Number($("#cena").val());
			var akontaceCena = Number($("#cena_akontace_akontace").val());
			var akontaceFinanc = (cena-akontaceCena);
			$('#cena_akontace_financovana').val(akontaceFinanc);

			var procenta = roundNumber(100-(akontaceFinanc/(cena/100)),0);
			$('#cena_akontace').val(procenta);
		}
	});

	$('body').on("change",'input[name="vyplaceno"]', function()
	{
		var vyplaceno = ($('input[name="vyplaceno"]').is(':checked') ? true : false);
		if(vyplaceno==true)
		{
			$('.vyplaceno_kam').show();
		}
		else
		{
			$('.vyplaceno_kam').hide();
			$('#vyplaceno_kam').val('');
		}
	});

	$('body').on("change",'input[name="vyplaceno_hotove_check"]', function()
	{
		var vyplaceno = ($('input[name="vyplaceno_hotove_check"]').is(':checked') ? true : false);
		if(vyplaceno==true)
		{
			$('.vyplaceno_hotove_wrap').show();
		}
		else
		{
			$('.vyplaceno_hotove_wrap').hide();
			$('#vyplaceno_hotove').val('');
		}
	});

	$('body').on("change",'input[name="vtp_zapujcen_agentura"]', function()
	{
		var vtp = $('input[name="vtp_zapujcen_agentura"]:checked').val();
		if(vtp == 1)
		{
			$('#vtp_zapujcen_datum').addClass('required');
			$('#vtp_zapujcen_datum').parent().parent().show();
		}
		else
		{
			$('#vtp_zapujcen_datum').removeClass('required');
			$('#vtp_zapujcen_datum').parent().parent().hide();
		}
	});

	$('input[name="vtp_zapujcen_agentura"]').change();
	$('#auto_dovoz').change();

	/*
	$('body').on("change","#id_user_vymahani", function()
	{
		var vtp = $('#id_user_vymahani').val();
		if(vtp != "")
		{
			$('#date_vymahani').addClass('required');
			$('#date_vymahani').parent().parent().show();
		}
		else
		{
			$('#date_vymahani').removeClass('required');
			$('#date_vymahani').parent().parent().hide();
		}
	});
	$('#id_user_vymahani').change();
	*/

	$('body').on("change","#zadatel_typ", function()
	{
		var typ = $('#zadatel_typ').val();
		if(typ == 'pravnicka')
		{
			$('#rucitel').show();
			$('#dic,#ico,#zastoupen,#spisovaznacka').parent().parent().show();
			$('#titul').parent().parent().hide();
			$('#jmeno').parent().parent().hide();
			//$('#narozeniic').parent().parent().hide();
			$('#prijmeni-popis').text('<?php echo l('Název firmy');?>');

			$("#stat_rucitel_move").appendTo("#container1_stat_rucitel_move");

		}
		else if(typ == 'osvc')
		{
			$('#rucitel').hide();
			$('#dic,#ico,#zastoupen,#spisovaznacka').parent().parent().show();
			$('#titul').parent().parent().show();
			$('#jmeno').parent().parent().show();
			//$('#narozeniic').parent().parent().show();
			$('#prijmeni-popis').text('<?php echo l('Příjmení');?>');

			$("#stat_rucitel_move").appendTo("#container2_stat_rucitel_move");
		}
		else
		{
			$('#rucitel').hide();
			$('#dic,#ico,#zastoupen,#spisovaznacka').parent().parent().hide();
			$('#titul').parent().parent().show();
			$('#jmeno').parent().parent().show();
			//$('#narozeniic').parent().parent().show();
			$('#prijmeni-popis').text('<?php echo l('Příjmení');?>');

			$("#stat_rucitel_move").appendTo("#container2_stat_rucitel_move");
		}
	});
	$('#zadatel_typ').change();

	$('body').on("change",'input[name="jezdi"]', function()
	{
		var typjizdy = $('input[name="jezdi"]:checked').val();
		var obchfirma = $('#obchfirma').val();

		if(typjizdy == 1) //jezdi
		{
			$('#urok').val(10);
			$('#auto_prevodvozu').val(2);
		}
		else //nejezdi
		{
			$('#urok').val(8);
			$('#auto_prevodvozu').val(3);
			$('#rezervacni_poplatek_dph_hard').hide();

		}


	});

	$('body').on("change","#addr_stat,#platce_dph", function()
	{
		var stat = $('#addr_stat').val();
		<?php if ($addNew)	{	?>
		var smlouvaVersion = stat2smlouvy[stat];
		$('#smlouva_version').val(smlouvaVersion);
		<?php }else{ ?>
		var smlouvaVersion = $('#smlouva_version').val();
		<?php } ?>
		stat = stat.toLowerCase();
		pobockaSel = $("#pobocka").val();
		var platce = ($("#platce_dph").is(':checked') ? true : false);

		if((stat == 'sk' || stat == 'cz') && smlouvaVersion != 'legacy')
		{
			// hmm, neco asi budeme delat
			$('#smlouva_typ_generace').val(2);
		}
		else
		{
			$('#smlouva_typ_generace').val(1);
			$("#rezervacni_poplatek_dph option[value='3']").prop('disabled',false);
		}

		if(stat == 'sk') //jezdi
		{
			$('.wrap_stat_hu').hide();
			$('.wrap_stat_es').hide();
			$('#getByICO').show();
			$('#spisovaznacka,#zastoupen').removeClass('required');

			<?php if ($addNew)	{	?>
			$('#obchfirma').val('<?php echo accountGet(2,'label');?>');
			$('#addr_stat').val('sk');
			$('#cena_mena').val('EUR');
			$('.cena_mena_label').html('EUR');

			if(platce)
			{
				$('#rezervacni_poplatek_dph').val(2);
				$('#cena_dph').val(2);
			}
			else
			{
				$('#rezervacni_poplatek_dph').val(3);
				$('#cena_dph').val(3);
			}



			//disable typs
			$("#typ option[value='2']").prop('disabled',false);
			$("#typ option[value='3']").prop('disabled',false);
			$("#typ option[value='4']").prop('disabled',false);
			$("#typ option[value='5']").prop('disabled',false);

			//bazary
			$("#typ option[value='10']").prop('disabled',false);
			$("#typ option[value='11']").prop('disabled',false);
			$("#typ option[value='12']").prop('disabled',false);
			$("#typ option[value='13']").prop('disabled',false);
			$("#typ option[value='20']").prop('disabled',false);
			$("#typ option[value='30']").prop('disabled',false);
			$("#typ option[value='31']").prop('disabled',false);
			$("#typ option[value='32']").prop('disabled',false);
			$("#typ option[value='33']").prop('disabled',false);
			<?php	}	?>
			//filter pobocky
			$("#pobocka option").each(function( index )
			{
				var val = $(this).attr('value');
				if(val != '')
				{
					if(stat2pobocky[val]['stat'] == 'sk')
					{
						$(this).css("display", "block");
					}
					else
					{
						if(pobockaSel==val) { $('#pobocka').val(''); }
						$(this).css("display", "none");
					}
				}
			});

		}
		else if(stat == 'pl') //jezdi
		{
			$('.wrap_stat_hu').hide();
			$('.wrap_stat_es').hide();
			$('#getByICO').hide();
			$('#spisovaznacka,#zastoupen').removeClass('required');

			<?php if ($addNew)	{	?>
			$('#obchfirma').val('<?php echo accountGet(3,'label');?>');
			$('#addr_stat').val('pl');
			$('#cena_mena').val('PLN');
			$('.cena_mena_label').html('PLN');

			if(platce)
			{
				$('#rezervacni_poplatek_dph').val(2);
				$('#cena_dph').val(2);
			}
			else
			{
				$('#rezervacni_poplatek_dph').val(3);
				$('#cena_dph').val(3);
			}

			//disable typs
			$("#typ option[value='2']").prop('disabled',true);
			$("#typ option[value='3']").prop('disabled',true);
			$("#typ option[value='4']").prop('disabled',true);
			$("#typ option[value='5']").prop('disabled',true);

			//bazary
			$("#typ option[value='10']").prop('disabled',true);
			$("#typ option[value='11']").prop('disabled',true);
			$("#typ option[value='12']").prop('disabled',true);
			$("#typ option[value='13']").prop('disabled',true);
			$("#typ option[value='20']").prop('disabled',true);
			$("#typ option[value='30']").prop('disabled',true);
			$("#typ option[value='31']").prop('disabled',true);
			$("#typ option[value='32']").prop('disabled',true);
			$("#typ option[value='33']").prop('disabled',true);
			<?php	}	?>

			//filter pobocky
			$("#pobocka option").each(function( index )
			{
				var val = $(this).attr('value');
				if(val != '')
				{
					if(stat2pobocky[val]['stat'] == 'pl')
					{
						$(this).css("display", "block");
					}
					else
					{
						if(pobockaSel==val) { $('#pobocka').val(''); }
						$(this).css("display", "none");
					}
				}
			});
		}
		else if(stat == 'hu') //jezdi
		{
			$('.wrap_stat_hu').show();
			$('.wrap_stat_es').hide();
			$('#getByICO').hide();
			$('#spisovaznacka,#zastoupen').removeClass('required');

			<?php if ($addNew)	{	?>
			$('#obchfirma').val('<?php echo accountGet(4,'label');?>');
			$('#addr_stat').val('hu');
			$('#cena_mena').val('HUF');
			$('.cena_mena_label').html('HUF');

			if(platce)
			{
				$('#rezervacni_poplatek_dph').val(2);
				$('#cena_dph').val(2);
			}
			else
			{
				$('#rezervacni_poplatek_dph').val(3);
				$('#cena_dph').val(3);
			}

			//disable typs
			$("#typ option[value='2']").prop('disabled',true);
			$("#typ option[value='3']").prop('disabled',true);
			$("#typ option[value='4']").prop('disabled',true);
			$("#typ option[value='5']").prop('disabled',true);

			//bazary
			$("#typ option[value='10']").prop('disabled',true);
			$("#typ option[value='11']").prop('disabled',true);
			$("#typ option[value='12']").prop('disabled',true);
			$("#typ option[value='13']").prop('disabled',true);
			$("#typ option[value='20']").prop('disabled',true);
			$("#typ option[value='30']").prop('disabled',true);
			$("#typ option[value='31']").prop('disabled',true);
			$("#typ option[value='32']").prop('disabled',true);
			$("#typ option[value='33']").prop('disabled',true);
			<?php	}	?>

			//filter pobocky
			$("#pobocka option").each(function( index )
			{
				var val = $(this).attr('value');
				if(val != '')
				{
					if(stat2pobocky[val]['stat'] == 'hu')
					{
						$(this).css("display", "block");
					}
					else
					{
						if(pobockaSel==val) { $('#pobocka').val(''); }
						$(this).css("display", "none");
					}
				}
			});
		}else if(stat == 'es') //jezdi
		{
			$('.wrap_stat_hu').hide();
			$('.wrap_stat_es').show();
			$('#getByICO').hide();
			$('#spisovaznacka,#zastoupen').addClass('required');

			<?php if ($addNew)	{	?>
			$('#obchfirma').val('<?php echo accountGet(5,'label');?>');
			$('#addr_stat').val('es');
			$('#cena_mena').val('EUR');
			$('.cena_mena_label').html('EUR');

			if(platce)
			{
				$('#rezervacni_poplatek_dph').val(2);
				$('#cena_dph').val(2);
			}
			else
			{
				$('#rezervacni_poplatek_dph').val(3);
				$('#cena_dph').val(3);
			}

			//disable typs
			$("#typ option[value='2']").prop('disabled',true);
			$("#typ option[value='3']").prop('disabled',true);
			$("#typ option[value='4']").prop('disabled',true);
			$("#typ option[value='5']").prop('disabled',true);

			//bazary
			$("#typ option[value='10']").prop('disabled',true);
			$("#typ option[value='11']").prop('disabled',true);
			$("#typ option[value='12']").prop('disabled',true);
			$("#typ option[value='13']").prop('disabled',true);
			$("#typ option[value='20']").prop('disabled',true);
			$("#typ option[value='30']").prop('disabled',true);
			$("#typ option[value='31']").prop('disabled',true);
			$("#typ option[value='32']").prop('disabled',true);
			$("#typ option[value='33']").prop('disabled',true);
			<?php	}	?>

			//filter pobocky
			$("#pobocka option").each(function( index )
			{
				var val = $(this).attr('value');
				if(val != '')
				{
					if(stat2pobocky[val]['stat'] == 'es')
					{
						$(this).css("display", "block");
					}
					else
					{
						if(pobockaSel==val) { $('#pobocka').val(''); }
						$(this).css("display", "none");
					}
				}
			});
		}
		else //cz
		{
			$('.wrap_stat_hu').hide();
			$('.wrap_stat_es').hide();
			$('#getByICO').show();
			$('#spisovaznacka,#zastoupen').removeClass('required');

			<?php if ($addNew)	{	?>

			$('#obchfirma').val('<?php echo accountGet(2,'label');?>');
			$('#addr_stat').val('cz');
			$('#cena_mena').val('CZK');
			$('.cena_mena_label').html('CZK');

			$('#rezervacni_poplatek_dph').prop("readonly",false);
			$('#cena_dph').prop("readonly",false);

			//vždy dph
			$('#rezervacni_poplatek_dph').val(3);
			$('#cena_dph').val(3);

			//disable typs
			$("#typ option[value='2']").prop('disabled',false);
			$("#typ option[value='3']").prop('disabled',false);
			$("#typ option[value='4']").prop('disabled',false);
			$("#typ option[value='5']").prop('disabled',false);

			//bazary
			$("#typ option[value='10']").prop('disabled',false);
			$("#typ option[value='11']").prop('disabled',false);
			$("#typ option[value='12']").prop('disabled',false);
			$("#typ option[value='13']").prop('disabled',false);
			$("#typ option[value='20']").prop('disabled',false);
			$("#typ option[value='30']").prop('disabled',false);
			$("#typ option[value='31']").prop('disabled',false);
			$("#typ option[value='32']").prop('disabled',false);
			$("#typ option[value='33']").prop('disabled',false);
			<?php	}	?>

			//filter pobocky
			$("#pobocka option").each(function( index )
			{
				var val = $(this).attr('value');
				if(val != '')
				{
					if(stat2pobocky[val]['stat'] == 'cz')
					{
						$(this).css("display", "block");
					}
					else
					{
						if(pobockaSel==val) { $('#pobocka').val(''); }
						$(this).css("display", "none");
					}
				}
			});
		}

		if((stat == 'sk' || stat == 'cz') && smlouvaVersion != 'legacy')
		{
			console.log('smlouvynew');
			$('#rezervacni_poplatek_dph').val(2);
			$("#rezervacni_poplatek_dph option[value=3]").attr('disabled','disabled');
			//$('#cena_dph').prop("readonly",false);

			//vždy 0 dph
			//$('#cena_dph').val(3);
		}


	});

	$('body').on("change","#typ", function()
	{
		var typ = $('#typ').val();
		if(typ == 1) //zastava
		{
			$('.wrap_kauce').hide();
			$('.wrap_fix').hide();
			$('.wrap_zast').show();
			$('.wrap_autobazar').hide();
			$('.wrap_autobazar_hide').show();
			$('#auto_spz,#auto_cislotechnprukazu,#auto_datumstk,#auto_registrace').addClass('required');
			$('label[for=auto_spz] span,label[for=auto_cislotechnprukazu] span,label[for=auto_datumstk] span,label[for=auto_registrace] span').show();
		}
		else if(typ==2)
		{
			$('.wrap_kauce').show();
			$('.wrap_fix').hide();
			$('.wrap_zast').show();
			$('.wrap_autobazar').hide();
			$('.wrap_autobazar_hide').show();
			$('#auto_spz,#auto_cislotechnprukazu,#auto_datumstk,#auto_registrace').addClass('required');
			$('label[for=auto_spz] span,label[for=auto_cislotechnprukazu] span,label[for=auto_datumstk] span').show();
		}
		else if(typ==3 || typ==4 || typ==5 || typ==10 || typ==11 || typ==12 || typ==13 || typ==20 || typ==30 || typ==31 || typ==32 || typ==33)
		{
			$('.wrap_kauce').hide();
			$('.wrap_zast').hide();

			if( typ==10 || typ==11 || typ==12 || typ==13 || typ==20 || typ==30 || typ==31 || typ==32 || typ==33) //autobazar
			{
				$('.wrap_autobazar').show();
				<?php if(prava('admin-autobazar')) { ?>$('.wrap_autobazar_hide').hide();<?php } ?>
				$('.wrap_autobazar_hidealways').hide();
				$('.wrap_fix').hide();
				$('#auto_spz,#auto_cislotechnprukazu,#auto_datumstk,#auto_registrace').removeClass('required');
				$('label[for=auto_spz] span,label[for=auto_cislotechnprukazu] span,label[for=auto_datumstk] span,label[for=auto_registrace] span').hide();
			}
			else
			{
				$('.wrap_autobazar').hide();
				$('.wrap_autobazar_hide').show();
				$('.wrap_fix').show();
				$('#auto_spz,#auto_cislotechnprukazu,#auto_datumstk,#auto_registrace').addClass('required');
				$('label[for=auto_spz] span,label[for=auto_cislotechnprukazu] span,label[for=auto_datumstk] span,label[for=auto_registrace] span').show();
			}
		}

	});

	$('body').on("change","#rezervacni_poplatek_manual", function()
	{
		rezervacni_poplatek_manual();
	});

	$('#typ').change();
	$('#addr_stat').change();
	$('#cena').change();
	rezervacni_poplatek_manual();
	$('input[name="vyplaceno"]').change();
	$('input[name="vyplaceno_hotove_check"]').change();


<?php if ($addNew)
	{	?>
		$('input[name="jezdi"]').change();
		$('#pobocka').change();
		$('#obchfirma').change();
<?php	}	?>

	$('body').on("change","#rezervacni_poplatek", function()
	{
		slovy(this,$('#rezervacni_poplatek_slovy'));
	});

	$('body').on("change","#auto_hmotnost,#auto_objem,#auto_palivo,#auto_typ", function()
	{
		var auto_hmotnost = $('#auto_hmotnost').val();
		var auto_objem = $('#auto_objem').val();
		var auto_palivo = $('#auto_palivo').val();
		var auto_typ = $('#auto_typ').val();

		$.ajax({
		type: "POST",
		url: "/ajax/zastavy/object.ajax.nomenklatury",
    	headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
		data: {auto_hmotnost: auto_hmotnost,auto_objem: auto_objem,auto_palivo:auto_palivo,auto_typ:auto_typ},
		success: function(html)
			{
				if (html=="x")
				{
					$('#auto_kodnomenklatury').val('');
				}
				else
				{
					$('#auto_kodnomenklatury').val(html);
				}
			}
		});
	});
});
function rezervacni_poplatek_manual()
{
	var rezervacni_poplatek_manual = $('#rezervacni_poplatek_manual').is(':checked');
	var typ = $('#typ').val();
	if(rezervacni_poplatek_manual == 1 || typ == 1 || typ == 2) //zastava
	{
		$('.wrap_rezervacni').show();
	}
	else
	{
		$('.wrap_rezervacni').hide();
	}
}
function slovy(_this,_toThis)
{
	var string = $(_this).val();
	$.ajax({
	type: "POST",
	url: "/ajax/core/number2word",
    headers: {'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') },
	data: {number: string},
	success: function(html){
		if (html=="x")
		{
			$(_toThis).val('');
		}
		else
		{
			$(_toThis).val(html);
		}
	}
	});
}

function coreRoundNumber(value, precision)
{
    var multiplier = Math.pow(10, precision || 0);
    return Math.round(value * multiplier) / multiplier;
}

function vyberZakaznik()
{
	if($("input[name='zakazniktyp']").length>0)
	{
		var typzak = $("input[name='zakazniktyp']:checked").val();
		console.log(typzak);
		if(typzak == 'novy')
		{
			$('#zakazniEditkHolder').html(zastavaZakaznikEditEmpty);
			$("#move_zakaznik").appendTo("#move_zakaznik_container2");
			$("#prijmeni").autocomplete("disable").val('');
			$("#id_zakaznik").val(0);
			$('#zakaznikInfo').html("<?php echo l('Nový zákazník. Bude uložen.'); ?>");
		}
		else
		{
			$("#move_zakaznik").appendTo("#move_zakaznik_container1");
			$("#prijmeni").autocomplete("enable").val('');;
			$('#zakaznikInfo').html("<?php echo l('Vyhledávání mezi stávajícími zákazníky').' ('.l('Hledat lze podle IČ, DIČ, RČ, e-mailu, telefonu a poznámek u klienta.').')'; ?>");
		}
	}
}

function reCount()
{
	var urok = Number($('#urok').val());
	var cena = Number($('#cena').val());
	var rezpopl = Number($('#rezervacni_poplatek').val());
	var urokFin = 0;

	if(cena>0 && rezpopl>0)
	{
		urokFin = coreRoundNumber((rezpopl/cena)*100,1).toFixed(1);
		$('#urok').val(urokFin).prop('readonly', true);
		$('#inputId')
	}
	else
	{
		$('#urok').val(0).prop('readonly', false);
	}
   	//nothing
}

</script>


<?php



}
else
{
	echo box_info(l("Nemáte oprávnění"),2);
}