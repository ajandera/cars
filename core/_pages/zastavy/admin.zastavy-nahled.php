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



if($DATA)
{
	$_POST = $DATA;
	$subHeading = '<span class="label label-info">'.l('Náhled').'</span>';
	$subHeading .= ' <span class="label label-info">id '.$DATA['id'].'</span>';

	echo '<div class="page-header"><h1>'.l('Vozidlo').' <small>';
	echo $subHeading;
	echo'</small>';

	echo ' <div class="btn-group" id="uu'.$DATA['id'].'">
				<button class="btn btn-default btn-sm dropdown-toggle" type="button"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"><i class="fa fa-bars fa-fw fa-lg" aria-hidden="true"></i> '.l('Možnosti').'</button>
			  	<ul class="dropdown-menu">';

				$coreTemplate->assign('x',$DATA);
				$coreTemplate->display('listZastavyDropdownNahled.tpl');

				echo  "</ul>";
	echo  "</div>";

	echo '</h1></div>';


	echo '<div>'; //tabs

	$tab = 1;
	if (isset($_GET['tab']) && is_numeric($_GET['tab']))
	{
		$tab = (int)$_GET['tab'];
	}


		echo '<ul class="nav nav-tabs" role="tablist">';
		echo '	<li role="presentation" '.($tab==1?'class="active"':'').'><a data-target="#tab1" class="click"  aria-controls="tab1" role="tab" data-toggle="tab">'.l('Základní smlouva').'</a></li>';
		echo '	<li role="presentation"><a data-target="#tab4" class="click"  aria-controls="tab4" role="tab" data-toggle="tab">'.l('Přílohy').'</a></li>';
		echo '	<li role="presentation" '.($tab==3?'class="active"':'').'><a data-target="#tab3" class="click"  aria-controls="tab3" role="tab" data-toggle="tab">'.l('Splátky').'</a></li>';
		echo '	<li role="presentation" '.($tab==8?'class="active"':'').'><a data-target="#tab8" class="click"  aria-controls="tab8" role="tab" data-toggle="tab">'.l('Vystavené doklady').'</a></li>';
		if($_POST['saldo'])
		{
			echo '	<li role="presentation"><a data-target="#tab5" class="click"  aria-controls="tab5" role="tab" data-toggle="tab">'.l('Saldo').' <span class="badge">!</span></a></li>';
		}
		else
		{
			//echo '	<li role="presentation"><a data-target="#tab5" class="click"  aria-controls="tab5" role="tab" data-toggle="tab">'.l('Vymáhání').'</a></li>';
		}
		echo '	<li role="presentation" '.($tab==6?'class="active"':'').'><a data-target="#tab6" class="click" aria-controls="tab6" role="tab" data-toggle="tab">'.l('Komunikace s klientem').($_POST['notifikace_komunikace']>0?' <span class="badge">'.$_POST['notifikace_komunikace'].'</span>':'').'</a></li>';
		echo '	<li role="presentation"><a data-target="#tab7" class="click"  aria-controls="tab7" role="tab" data-toggle="tab">'.l('Prodej').'</a></li>';
		echo '	<li role="presentation"><a data-target="#tab2" class="click"  aria-controls="tab2" role="tab" data-toggle="tab">'.l('Dodatečné texty u smlouvy').'</a></li>';
		echo '</ul>';

		echo '<div class="tab-content paddingT3">';
		echo '<div role="tabpanel" class="tab-pane'.($tab==1?' active':'').'" id="tab1">';

		if ($_POST['stav']==-1)
		{
			echo boxInfo(l('Smazané'),true);
		}

		if ($_POST['stav_fraud']==1)
		{
			echo boxInfo(l('FRAUD: Pozor, případ označen jako FRAUD.'),true);
		}

		if (substr($_POST['date_end'],0,1)!=0)
		{
			echo boxInfo(l('Ukončeno').': '.predkdy_dny($_POST['date_end'],4),false);
		}

		if (substr($_POST['date_naprodej'],0,1)!=0)
		{
			echo boxInfo(l('Na prodej').': '.predkdy_dny($_POST['date_naprodej'],4),false);
		}

		if (substr($_POST['date_prodane'],0,1)!=0)
		{
			echo boxInfo(l('Prodané').': '.predkdy_dny($_POST['date_prodane'],4),false);
		}

		if ($_POST['faktury_stopgen']==1)
		{
			echo boxInfo(l('Zastavené generování faktur'));
		}

		if ($_POST['vyzvy_stopgen']==1)
		{
			echo boxInfo(l('Zastavené zasílání výzev'));
		}

		if ($_POST['splatky_stopgen']==1)
		{
			echo boxInfo(l('Zastavené generování splátek'));
		}

		if (substr($_POST['date_agentura'],0,1)!=0)
		{
			echo boxInfo(l('Předané agentuře').': '.predkdy_dny($_POST['date_agentura'],4),false);
		}

				echo '<form action="" method="post" class="form-horizontal">';


					$USER = getUserByID($_POST['id_user']);

					echo createInputBlockStart(l("Typ případu"),'panel-danger');
					echo createInputText("typ",$C->zastavy_typ[$_POST["typ"]]['label'],l("Typ případu"),"horizontal",true,"form-control input-sm",false);
					if ($USER)
					{
						echo createInputInfo("Založil",(!empty($USER['spolecnost'])?$USER['spolecnost'].', ':'').$USER['jmeno'].' '.$USER['prijmeni'],"horizontal",false,'lead marginB0',true);
					}


					if (find_in_set($_POST['typ'],'10,11,12,13,20,30,31,32,33'))
					{
						echo createInputText("provize",$C->zastavy_provize[$_POST["provize"]],l("Výše provize autobazaru"),"horizontal");
					}

					echo createInputText("obchfirma",$C->obchfirma[$_POST["obchfirma"]]['label'],l("Smlouva uzavřena za"),"horizontal",true,false,"form-control input-sm");

					echo createInputText("pobocka",$C->pobocky[$_POST["pobocka"]]['label'],l("Spadá pod pobočku"),"horizontal",true,'Vyberte',"form-control input-sm",false,false);

					if($_POST['id_poptavka']>0)
					{
						$POPTAVKA = poptavkyGet($_POST['id_poptavka']);
						if($POPTAVKA)
						{
							//z poptavky

							echo createInputInfo("Poptávka ID",'<a href="/poptavky/admin.poptavky/detail/'.$POPTAVKA['id'].'/" class="btn btn-default btn-xs" style="margin-top: 3px" target="_blank">'.$POPTAVKA['id'].'</a>',"horizontal",false,'marginB0',true);
							if(!empty($POPTAVKA['source']))
							{
								echo createInputText("source",$POPTAVKA["source"],l("Poptávka přišla z"),"horizontal");
							}
						}
					}

					echo createInputBlockEnd();

	echo '<div class="row"><div class="col-sm-6">';


				echo createInputBlockStart(l("Vozidlo"),"panel-info");
					echo createInputText("auto_typ",$C->zastavy_auto_typ[$_POST["auto_typ"]]['label'],l("Typ vozidla"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_znacka",$_POST["auto_znacka"],l("Tovární značka"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_model",$_POST["auto_model"],l("Typ/model"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_rokvyroby",$_POST["auto_rokvyroby"],l("Rok výroby"),"horizontal",true,"form-control input-sm digits");
					echo createInputText("auto_registrace",$_POST["auto_registrace"],l("1. Registrace"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_spz",$_POST["auto_spz"],l("SPZ vozidla"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_vin",$_POST["auto_vin"],l("VIN"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_km",$_POST["auto_km"],l("Najeto km"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_barva",$_POST["auto_barva"],l("Barva"),"horizontal",true,"form-control input-sm");
					echo createInputTextarea("auto_stav",$_POST["auto_stav"],l("Technický stav"),"horizontal",3,false,"form-control input-sm");
					echo createInputText("auto_predchozichmajitelu",$_POST["auto_predchozichmajitelu"],l("Počet předchozích majitelů"),"horizontal",false,"form-control input-sm");
					echo createInputText("auto_datumposledniregistrace",$_POST["auto_datumposledniregistrace"],l("Datum poslední registrace vozidla"),"horizontal",false,"form-control input-sm date datum");

					echo createInputText('auto_dovoz',$C->zastavy_auto_dovoz[(isset($_POST['auto_dovoz'])?($_POST['auto_dovoz']?1:0):'')]['label'],l('Dovoz ze zahraničí'),'horizontal');
					if($_POST['auto_dovoz']>0)
					{
						echo createInputText("auto_datumdovoz",$_POST["auto_datumdovoz"],l("Datum dovozu ze zahraničí"),"horizontal",false,"form-control input-sm date datum");
					}

					echo createInputTextarea("auto_vybava",$_POST["auto_vybava"],l("Výbava"),"horizontal",3,false,"form-control input-sm");

					echo createInputText("auto_havarovane",$_POST["auto_havarovane"],l("Havarované"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_cislotechnprukazu",$_POST["auto_cislotechnprukazu"],l("Číslo velkého technického průkazu"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_datumstk",$_POST["auto_datumstk"],l("platnost STK do"),"horizontal",true,"form-control input-sm datum");
					echo createInputText("auto_klicu",$_POST["auto_klicu"],l("Počet klíčů"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_vykon",$_POST["auto_vykon"],l("Výkon (kW)"),"horizontal",false,"form-control input-sm digits");
					echo createInputText("auto_doklady",$_POST["auto_doklady"],l("Doklady"),"horizontal",true,"form-control input-sm");
					echo createInputText("auto_predpnajezd",$_POST["auto_predpnajezd"],l("Předpokládaný roční nájezd"),"horizontal",false,"form-control input-sm");
					echo createInputText("auto_prevodvozu",$C->dropPrevodvozu[$_POST["auto_prevodvozu"]],l("Převod vozu"),"horizontal",true);
					$arr=getConfig('bazar-karoserie');
					echo createInputText("auto_karoserie",$arr[$_POST["auto_karoserie"]],l("Typ karoserie"),"horizontal",true,"form-control input-sm");
					$arr=getConfig('bazar-palivo');
					echo createInputText("auto_palivo",$arr[$_POST["auto_palivo"]],l("Palivo"),"horizontal",true,"form-control input-sm");
					$arr=getConfig('bazar-prevodovka');
					echo createInputText("auto_prevodovka",$arr[$_POST["auto_prevodovka"]],l("Převodovka"),"horizontal",true,"form-control input-sm");

					echo createInputText('auto_1majitel',$C->dropAnoNe[(isset($_POST['auto_1majitel'])?($_POST['auto_1majitel']?1:0):'')],l('První majitel?'),'horizontal');
					echo createInputText('vtp_zapujcen_agentura',$C->dropAnoNe[(isset($_POST['vtp_zapujcen_agentura'])?($_POST['vtp_zapujcen_agentura']?1:0):'')],l('Zapůjčení VTP'),'horizontal');
					echo createInputText("vtp_zapujcen_datum",$_POST["vtp_zapujcen_datum"],l("Datum zapůjčení VTP"),"horizontal",false,"form-control input-sm datum dateISO");

					echo createInputText("auto_objem",$_POST["auto_objem"],l("Zdvihový objem v cm3"),"horizontal",false,"form-control input-sm digits");
					echo createInputText("auto_hmotnost",$_POST["auto_hmotnost"],l("Provozní hmotnost v kg"),"horizontal",false,"form-control input-sm digits");
					echo createInputText("auto_kodnomenklatury",$_POST["auto_kodnomenklatury"],l("Kód nomenklatury"),"horizontal",false,"form-control input-sm digits",false,false,false,50,false,'<span style="position: absolute; top: 3px; right: 21px;" class=""><a href="/zastavy/admin.nomenklatury/" target="_blank" class="btn btn-xs btn-warning">'.l('Zobrazit nomenklatury').'</a></span>');


					echo '<div class="wrap_stat_hu">';
						echo createInputText("auto_cislomotoru",$_POST["auto_cislomotoru"],l("Číslo motoru"),"horizontal",false,"form-control input-sm");
						echo createInputText("auto_cislomalehotp",$_POST["auto_cislomalehotp"],l("Číslo malého TP"),"horizontal",false,"form-control input-sm");
					echo '</div>';

				echo createInputBlockEnd();

				echo createInputBlockStart(l("Půjčka"),"panel-warning");

					echo createInputText('jezdi',$C->dropAnoNe[$_POST["jezdi"]],l('Jezdí?'),'horizontal');


					echo ($_POST['vyplaceno_hotove']>0?createInputText('vyplaceno_hotove','Hotově: '.$_POST['vyplaceno_hotove'],l('Vyplaceno'),'horizontal'):'');
					echo ($_POST['vyplaceno']==1?createInputText('vyplaceno','Převodem/Kartou: '.($_POST['vyplaceno_prevod']>0?$_POST['vyplaceno_prevod'].' / ':'').$_POST['vyplaceno_kam'],l('Vyplaceno'),'horizontal'):'');
					echo createInputText("cena",$_POST["cena"],l("Kupní cena"),"horizontal",true,"form-control input-sm number");
					echo createInputText("cena_slovy",$_POST["cena_slovy"],l("Kupní cena (slovy)"),"horizontal",true,"form-control input-sm");
					echo createInputText("cena_dph",(is_array($C->dropDPH[$_POST["cena_dph"]])?$C->dropDPH[$_POST["cena_dph"]]['label']:$C->dropDPH[$_POST["cena_dph"]]),l("DPH"),"horizontal",true,false,"form-control input-sm");

					if (find_in_set($_POST['typ'],'10,11,12,13,20,30,31,32,33'))
					{
						if ($_POST["cena_akontace"]>0 && $_POST["cena_akontace_financovana"]==0)
						{
							//prepocitame, protoze se predelaval system
							$_POST["cena_akontace_financovana"] = $x["cena"]-$x["kauce"];
							$_POST["cena_akontace_akontace"] = $x["kauce"];
						}

						echo createInputText("cena_akontace_financovana",$_POST["cena_akontace_financovana"],l("Financovaná cena vozidla"),"horizontal",true,"form-control input-sm digits",false,false,' ',30,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
						echo createInputText("cena_akontace_akontace",$_POST["cena_akontace_akontace"],l("Akontace"),"horizontal",true,"form-control input-sm digits",false,false,' ',30,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');

						echo createInputText("cena_akontace",$_POST["cena_akontace"],l("Výše akontace v %"),"horizontal",true,"form-control input-sm digits",false,false,' max="99" min="30" ',2,false,'<span style="position: absolute; top: 5px; right: 21px;" class="x">%</span>');
					}

					if ($_POST['typ']==2)
					{
						echo '<div class="wrap_kauce">';
						echo createInputText("kauce",$_POST["kauce"],l("Výše kauce"),"horizontal",true,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
						echo createInputText("kauce_slovy",$_POST["kauce_slovy"],l("Výše kauce (slovy)"),"horizontal",true,"form-control input-sm");
						echo '</div>';
					}

					if (find_in_set($_POST['typ'],'3,4,5'))
					{
						echo createInputText("cena_doplatek",$_POST["cena_doplatek"],l("Výše konečného doplatku"),"horizontal",true,"form-control input-sm number",false,l('Platí se společně s posledním RP, pokud zůstane nevyplněná C4C spočítá doporučenou částku'),false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');
					}

					echo '<hr>';
					if (find_in_set($_POST['typ'],'1,2,20'))
					{
						//echo createInputText("dnu_odpredani",($addNew?0:$_POST["dnu_odpredani"]),"Počet dnů od předání","horizontal",true,"form-control input-sm");
						echo createInputText("rezervacni_poplatek",$_POST["rezervacni_poplatek"].' '.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK'),l("Rezervační poplatek"),"horizontal",true,"form-control input-sm number");
						echo createInputText("rezervacni_poplatek_slovy",$_POST["rezervacni_poplatek_slovy"],l("Rezervační poplatek (slovy)"),"horizontal",true,"form-control input-sm");
					}
					elseif ($_POST["rezervacni_poplatek"]>0)
					{
						echo createInputText("rezervacni_poplatek",$_POST["rezervacni_poplatek"].' '.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK'),l("Rezervační poplatek"),"horizontal",true,"form-control input-sm number");
					}

					echo createInputText("rezervacni_poplatek_dph",(is_array($C->dropDPH[$_POST["rezervacni_poplatek_dph"]])?$C->dropDPH[$_POST["rezervacni_poplatek_dph"]]['label']:$C->dropDPH[$_POST["rezervacni_poplatek_dph"]]),l("DPH"),"horizontal");

					if (find_in_set($_POST['typ'],'1,2'))
					{
						echo createInputText("urok",$_POST["urok"].' %',l("Měsíční úrok"),"horizontal",true,"form-control input-sm");
					}

					echo '<hr>';


					echo createInputText("datum_podpisukup",($addNew?date('Y-m-d'):$_POST["datum_podpisukup"]),l("Datum podpisu"),"horizontal",false,"form-control input-sm datum dateISO");
					if($_POST["datum_splatnostfakturace_override_day"]>0)
					{
						echo createInputText("datum_splatnostfakturace_override_day",$_POST["datum_splatnostfakturace_override_day"],l("Den, ke kterému vystavovat doklady"),"horizontal",false,"form-control input-sm digits",false,l('Pokud vyplníte, ignoruje se při generování splátek datum podpisu kupní smlouvy, ale využívá se tento den pro korekci splátek (vystavování faktur). K tomuto dni budou splatnosti dokladů. Datum splatnosti pro vymáhání se tímto neovlivní. To je ovlivněno datumem splatnosti rezervačního poplatku. '),' max="31" style="background-color: #dff0d8;" ',2,false,false,'digits');
					}

					if($_POST['stav_schvaleni'] == -1)
					{
						echo createInputInfo(l('Schválení smlouvy'),l('Smlouva byla zamítnuta'),'horizontal',false,'alert alert-danger');
					}
					elseif($_POST['stav_schvaleni'] == 0)
					{
						echo createInputInfo(l('Schválení smlouvy'),l('Čeká na schválení'),'horizontal',false,'alert alert-info');
					}
					elseif($_POST['stav_schvaleni'] >= 1)
					{
						echo createInputInfo(l('Schválení smlouvy'),l('Smlouva je schválená'),'horizontal',false,'alert alert-success');
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

					echo createInputText('stav_podpis',$C->dropAnoNe[(isset($_POST['stav_podpis'])?($_POST['stav_podpis']?1:0):'')],l('Smlouva podepsaná?'),'horizontal');
					echo createInputText('stav_fakturaprijata',$C->dropAnoNe[(isset($_POST['stav_fakturaprijata'])?($_POST['stav_fakturaprijata']?1:0):'')],l('Máme od klienta přijatou fakturu?'),'horizontal');
					echo '<hr>';
					echo createInputText("datum_rezpoplateksplatnost",($addNew?date('Y-m-d',strtotime('+1 month')):$_POST["datum_rezpoplateksplatnost"]),l("Rezervační poplatek splatnost"),"horizontal",false,"form-control input-sm",false,false,false,30,false,($addNew?'<span style="position: absolute; top: 5px; right: 21px;">'.l('automatické').'</span>':''));
					echo createInputText("datum_marneplneni",($addNew?date('Y-m-d',strtotime('+1 year')):$_POST["datum_marneplneni"]),l("Datum marného uplynutí"),"horizontal",false,"form-control input-sm",false,false,false,30,false,($addNew?'<span style="position: absolute; top: 5px; right: 21px;">'.l('automatické').'</span>':''));
					echo '<hr>';
					//echo createInputSelect("cislo_uctu",$C->zastavy_cislo_uctu,($addNew?2:$_POST["cislo_uctu"]),l('Číslo účtu'),"horizontal",true,false,"form-control input-sm");
					//echo '<hr>';

					echo createInputTextarea("info",$_POST["info"],l("Poznámka / informace (propisuje se do výpisu vozidel)"),"horizontal",3,false,"form-control input-sm");
					echo createInputTextarea("info_long",$_POST["info_long"],l('Poznámka / informace (k obchodnímu případu, např. specifické domluvy s klientem, kde jsou klíče apod.)'),"horizontal",6,false,"form-control input-sm");
					echo createInputText("cena_hodnotazastavy",$_POST["cena_hodnotazastavy"],l("Tržní cena vozidla"),"horizontal",true,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK').'</span>');

				echo createInputBlockEnd();

				echo createInputBlockStart(l('Korespondenční adresa').' - '.l('váže se k danému případu'),'panel-default bg-light wrap_autobazar_hide');
					echo createInputText('addr_kor_nazev',$_POST['addr_kor_nazev'],l('Jméno / Firma'),"horizontal",false,"form-control input-sm");
					echo createInputText('adresa_korespondence',$_POST['adresa_korespondence'],l('Ulice'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_kor_mesto',$_POST['addr_kor_mesto'],l('Město'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_kor_psc',$_POST['addr_kor_psc'],l('PSČ'),"horizontal",false,"form-control input-sm");
					echo createInputText('addr_kor_stat',$_POST['addr_kor_stat'],l('Stát'),"horizontal",false,"form-control input-sm");
				echo createInputBlockEnd();

				echo createInputBlockStart(l('Pojištění'));
					echo createInputText('pojisteni_pojistovna',$_POST['pojisteni_pojistovna'],l('Pojišťovna'),'horizontal',false,'form-control input-sm');
					echo createInputText('pojisteni_cislosmlouvy',$_POST['pojisteni_cislosmlouvy'],l('Číslo smlouvy'),'horizontal',false,'form-control input-sm');
					echo createInputText('pojisteni_vysepojistneho',$_POST['pojisteni_vysepojistneho'],l('Výše pojistného'),'horizontal',false,'digits form-control input-sm');
					echo createInputText('pojisteni_datum_splatnosti',$_POST['pojisteni_datum_splatnosti'],l('Datum splatnosti'),'horizontal',false,'form-control input-sm datum');
					echo createInputText('pojisteni_datum_uhrady',$_POST['pojisteni_datum_uhrady'],l('Datum úhrady'),'horizontal',false,'form-control input-sm datum');
				echo createInputBlockEnd();

				echo createInputBlockStart(l('Doplňující informace'));
					echo createInputText("auto_mistostani",$_POST['auto_mistostani'],l('Místo stání vozidla'),"horizontal",false,"form-control input-sm");
					echo createInputText('auto_klic_drzime',($_POST['auto_klic_drzime']==1?l('ANO'):l('NE')),l('Klíč k vozu v našem držení?'),'horizontal');
					echo createInputText('auto_gps',($_POST['auto_gps']==1?l('ANO'):l('NE')),l('GPS sledování ve vozidle?'),'horizontal');
					echo createInputText('stav_fraud',($_POST['stav_fraud']==1?l('ANO'):l('NE')),l('FRAUD (jedná se o podvod)'),'horizontal');
					echo createInputText('prepsano',($_POST['prepsano']==1?l('ANO'):l('NE')),l('Přepsáno na nás'),'horizontal');
					echo createInputText('datum_podpisu_change_label',$_POST['datum_podpisu_change_label'],l('Reálné datum označení podpisu'),'horizontal');

					$thisUser = '-';
					if ($_POST['id_user_exportto'] > 0 )
					{
						if($thisUser = getUser($_POST['id_user_exportto']))
						{
							$thisUser = $thisUser['jmeno']. ' ' .$thisUser['prijmeni'];
						}
					}
					echo createInputText("id_user_exportto",$thisUser,l('Smlouvy tisknout na tohoto uživatele'),"horizontal",false,"form-control input-sm");


					$thisUser = '-';
					if ($_POST['id_user_vymahani'] > 0 )
					{
						if($thisUser = getUser($_POST['id_user_vymahani']))
						{
							$thisUser = $thisUser['jmeno']. ' ' .$thisUser['prijmeni'];
						}
					}
					echo createInputText("id_user_vymahani",$thisUser,l('Zobrazit této vymáhací agentuře'),"horizontal",false,"form-control input-sm");
					echo createInputText("date_vymahani",$_POST["date_vymahani"],l("Datum předání agentuře"),"horizontal",false,"form-control input-sm datum dateISO");
					echo createInputText("smlouva_version",$_POST["smlouva_version"],l("Verze smlouvy"),"horizontal",false,"form-control input-sm");

				echo createInputBlockEnd();


			echo '</div><div class="col-sm-6">';


				//detail zakaznika
				require_once($C->root.'/_templates/zakaznikNahled.inc');


			echo '</div></div>';
			echo '</form>';

		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane" id="tab2">';

			echo '<form action="" method="post" class="form-horizontal">';
			echo createInputBlockStart(l('Přidání vlastního odstavce do KUPNÍ smlouvy'));
				echo createInputTextarea("smlouvatext_add_1",$_POST["smlouvatext_add_1"],l("Přidat na konec 1. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_2",$_POST["smlouvatext_add_2"],l("Přidat na konec 2. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_3",$_POST["smlouvatext_add_3"],l("Přidat na konec 3. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_4",$_POST["smlouvatext_add_4"],l("Přidat na konec 4. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_5",$_POST["smlouvatext_add_5"],l("Přidat na konec 5. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_6",$_POST["smlouvatext_add_6"],l("Přidat na konec 6. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_add_7",$_POST["smlouvatext_add_7"],l("Přidat na konec 7. odstavce"),"horizontal",6,false,"form-control input-sm");
			echo createInputBlockEnd();

			echo createInputBlockStart(l('Přidání vlastního odstavce do NÁJEMNÉ smlouvy'));
				echo createInputTextarea("smlouvatext_najem_1",$_POST["smlouvatext_najem_1"],l("Přidat na konec 1. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_2",$_POST["smlouvatext_najem_2"],l("Přidat na konec 2. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_3",$_POST["smlouvatext_najem_3"],l("Přidat na konec 3. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_4",$_POST["smlouvatext_najem_4"],l("Přidat na konec 4. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_5",$_POST["smlouvatext_najem_5"],l("Přidat na konec 5. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_6",$_POST["smlouvatext_najem_6"],l("Přidat na konec 6. odstavce"),"horizontal",6,false,"form-control input-sm");
				echo createInputTextarea("smlouvatext_najem_7",$_POST["smlouvatext_najem_7"],l("Přidat na konec 7. odstavce"),"horizontal",6,false,"form-control input-sm");
			echo createInputBlockEnd();
			echo '</form>';

		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane" id="tab4">';

			if ($_POST['obrazkyTP'] && !empty($_POST['obrazkyTP']))
			{
				if (find_in_set($_POST['typ'],'10,11,12,13,20,30,31,32,33'))
				{
					echo '<h3>'.l('Technický průkaz, OP klienta, ŘP klienta').'</h3>';
				}
				else
				{
					echo '<h3>'.l('Technický průkaz').'</h3>';
				}
				$coreTemplate->assign('thisObrazky',$_POST['obrazkyTP']);
				$coreTemplate->display('listObrazky.tpl');
				echo '<hr>';
			}
			if ($_POST['obrazkyAuto'] && !empty($_POST['obrazkyAuto']))
			{
				echo '<h3>'.l('Vozidlo').'</h3>';
				$coreTemplate->assign('thisObrazky',$_POST['obrazkyAuto']);
				$coreTemplate->display('listObrazky.tpl');
				echo '<hr>';
			}

			if ($_POST['obrazkyPojisteni'] && !empty($_POST['obrazkyPojisteni']))
			{
				if (find_in_set($_POST['typ'],'10,11,12,13,20,30,31,32,33'))
				{
					echo '<h3>'.l('Předávací protokol vozidla').'</h3>';
				}
				else
				{
					echo '<h3>'.l('Pojištění').'</h3>';
				}
				$coreTemplate->assign('thisObrazky',$_POST['obrazkyPojisteni']);
				$coreTemplate->display('listObrazky.tpl');
				echo '<hr>';
			}

			if ($_POST['obrazkySmlouva'] && !empty($_POST['obrazkySmlouva']))
			{
				echo '<h3>'.l('Sken podepsané smlouvy').'</h3>';
				$coreTemplate->assign('thisObrazky',$_POST['obrazkySmlouva']);
				$coreTemplate->display('listObrazky.tpl');
				echo '<hr>';
			}

			if (empty($_POST['obrazkyTP']) && empty($_POST['obrazkyAuto']) && empty($_POST['obrazkyPojisteni']) && empty($_POST['obrazkySmlouva']))
			{
				echo boxBigInfo(l("Žádné nahrané přílohy"),l('Nevedeme žádné soubory k tomuto vozidlu.'));
			}


		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane'.($tab==3?' active':'').'" id="tab3">';

			//faktury to splatky


			$sqlDoklady = coreDBSel("SELECT id FROM ".$C->db_prefix."faktury WHERE stav >= 0 AND (fa_objednavka = ? OR fa_objednavka = ?) AND FIND_IN_SET(rada,'3,4,5,6') ORDER BY id DESC",array('reg'.$_POST['id'],$_POST['id']));
			while($r = $sqlDoklady->fetchRow())
			{
				if(!isset($dokladyArr[$r['id']]))
				{
					$t = FakturaGet($r['id']);
					$t['css_bg'] = 'bg-warning';
					$_POST['splatky']['fakInject'.$t['id']] = $t;
				}
			}

			$sqlDoklady = coreDBSel("SELECT id FROM ".$C->db_prefix."zalohy WHERE stav >= 0 AND (fa_objednavka = ? OR fa_objednavka = ?) AND FIND_IN_SET(rada,'3,4,5,6') ORDER BY id DESC",array('reg'.$_POST['id'],$_POST['id']));
			while($r = $sqlDoklady->fetchRow())
			{
				if(!isset($dokladyArr[$r['id']]))
				{
					$t = ZalohaGet($r['id']);
					$t['css_bg'] = 'bg-warning';
					$_POST['splatky']['zalInject'.$t['id']] = $t;
				}
			}

			if(!empty($_POST['splatky']))
			{
				//sort
				function compareDatumSplatnost($a, $b){ return strnatcasecmp($b["date_splatnost"], $a["date_splatnost"]); }
				uasort($_POST['splatky'],'compareDatumSplatnost');
			}

			if($_POST['smlouva_typ_generace']==2)
			{
				$sqlDoklady = coreDBSel("SELECT Z.cena_vyst,Z.fa_mena,Z.cislo_faktury FROM ".$C->db_prefix."zalohy Z LEFT JOIN ".$C->db_prefix."zastavy_splatky S ON Z.id_splatky = S.id  WHERE Z.stav = 0 AND S.stav = 0 AND Z.fa_objednavka = ? AND (Z.rada = 2) AND Z.zpracovano = 0 AND S.id_zalohy != 0 ORDER BY S.date_splatnost_orig ASC",array($_POST['id']));
				$pctNezaplacenych = $sqlDoklady->recordCount();
				if($pctNezaplacenych > 0 && $pctNezaplacenych<$_POST['smlouva_vypovedpokut'])
				{
					$dluhkZaplaceni = array();
					while($r = $sqlDoklady->fetchRow())
					{
						$dluhkZaplaceni[] = $r['cena_vyst'].' '.$r['fa_mena'].' ('.$r['cislo_faktury'].')';
					}

					//pokuta1
					//check jestli existují nezaplacené pokuty ... jestli jo, tak velké tlačítko a zakázat prodlužovat.
					echo '<a class="btn btn-primary marginB confirm" href="/zastavy/admin.zastavy/prodlouzit-2-all/'.$_POST['id'].'/?redir=/'.$_display.(!empty($_action)?'/'.$_action:'').(!empty($_aid)?'/'.$_aid:'').'"><i class="fa fa-fw fa-money"></i> '.l('Klient zaplatil všechny dlužné poplatky (prodlouží případ a vystaví faktury)').': '.implode(', ',$dluhkZaplaceni).'</a> ';
					echo '<a class="btn btn-default marginB confirm" href="/zastavy/admin.zastavy/prodlouzit-2-last/'.$_POST['id'].'/?redir=/'.$_display.(!empty($_action)?'/'.$_action:'').(!empty($_aid)?'/'.$_aid:'').'"><i class="fa fa-fw fa-money"></i> '.l('Klient zaplatil jeden dluh (neprodlouží případ)').': '.$dluhkZaplaceni[0].'</a> ';
				}
				else
				{
					$sqlDoklady = coreDBSel("SELECT Z.cena_vyst,Z.fa_mena,Z.cislo_faktury FROM ".$C->db_prefix."zalohy Z LEFT JOIN ".$C->db_prefix."zastavy_splatky S ON Z.id_splatky = S.id  WHERE Z.stav = 0 AND S.stav = 0 AND Z.fa_objednavka = ? AND (Z.rada = 0) AND Z.zpracovano = 0 AND S.id_zalohy != 0 ORDER BY S.date_splatnost_orig ASC",array($_POST['id']));
					$pctNezaplacenych = $sqlDoklady->recordCount();
					if($pctNezaplacenych > 0)
					{
						$dluhkZaplaceni = array();
						while($r = $sqlDoklady->fetchRow())
						{
							$dluhkZaplaceni[] = $r['cena_vyst'].' '.$r['fa_mena'].' ('.$r['cislo_faktury'].')';
						}

						echo '<a class="btn btn-primary marginB confirm" href="/zastavy/admin.zastavy/prodlouzit-2-rezervace/'.$_POST['id'].'/?redir=/'.$_display.(!empty($_action)?'/'.$_action:'').(!empty($_aid)?'/'.$_aid:'').'"><i class="fa fa-fw fa-money"></i> '.l('Klient zaplatil rezervační poplatek').': '.$dluhkZaplaceni[0].'</a> ';
					}
					else
					{
						echo '<a class="btn pe-none btn-primary marginB"><i class="fa fa-fw fa-check-circle" aria-hidden="true"></i> '.l("Vše zaplaceno").'</a>';
					}
				}

				echo '<!-- Modal: výběr data zaplacení -->
				<div class="modal fade" id="paidDateModal" tabindex="-1" role="dialog" aria-labelledby="paidDateModalLabel">
				  <div class="modal-dialog" role="document">
				    <div class="modal-content">
				      <div class="modal-header">
				        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				          <span aria-hidden="true">&times;</span>
				        </button>
				        <h4 class="modal-title" id="paidDateModalLabel">Vyberte datum zaplacení</h4>
				      </div>
				      <div class="modal-body">
				        <div class="form-group" id="paid-date-group">
				          <label for="paid-date">Datum</label>
				          <input type="date" class="form-control" id="paid-date" max="'.date('Y-m-d').'" required>
				          <p class="help-block" id="paid-date-help" style="display:none;">Zadejte prosím platné datum.</p>
				        </div>
				        <div class="checkbox">
				          <label>
				            <input type="checkbox" id="use-today"> Použít dnešní datum
				          </label>
				        </div>
				      </div>
				      <div class="modal-footer">
				        <button type="button" class="btn btn-default" data-dismiss="modal">Zrušit</button>
				        <button type="button" class="btn btn-primary" id="paid-date-confirm">Potvrdit</button>
				      </div>
				    </div>
				  </div>
				</div>

				';

				?>

<script>
(function($){
  var targetHref = null;                 // sem se uloží cílové href z kliknutého <a.confirm>
  var paramName  = 'paid_date';          // název GET parametru do URL (změň dle potřeby)

  // ===== Pomocné funkce =====
  function getToday(){
    var d = new Date();
    var yyyy = d.getFullYear();
    var mm = ('0' + (d.getMonth()+1)).slice(-2);
    var dd = ('0' + d.getDate()).slice(-2);
    return yyyy + '-' + mm + '-' + dd;
  }

  function setToday(){
    $('#paid-date').val(getToday());
  }

  // Přidej/aktualizuj query parametr v URL (zachová stávající parametry i hash)
  function setQueryParam(url, key, value){
    // Zajisti absolutní URL pro URLSearchParams
    var a = document.createElement('a');
    var base = window.location.protocol + '//' + window.location.host;
    a.href = url.indexOf('http') === 0 ? url : base + url;

    var params = new URLSearchParams(a.search || '');
    params.set(key, value);

    a.search = '?' + params.toString();

    // Vrať relativní cestu (bez domény), ať to pěkně funguje i s interními routami
    return a.pathname + a.search + a.hash;
  }

  // ===== Handlery =====

  // Klik na libovolné <a class="confirm"> -> otevři modal s předvyplněným dneškem
  $(document).on('click', 'a.confirm', function(e){
    e.preventDefault();
    targetHref = this.href;

    setToday();
    $('#use-today').prop('checked', true);

    // reset validace
    $('#paid-date-help').hide();
    $('#paid-date-group').removeClass('has-error');

    $('#paidDateModal').modal('show');
  });

  // Zaškrtnutí "Použít dnešní datum" -> nastav dnešek
  $('#use-today').on('change', function(){
    if (this.checked) {
      setToday();
    }
  });

  // Změna data -> automaticky odškrtnout, pokud není dnešek; znovu zaškrtnout, pokud je
  $('#paid-date').on('change', function(){
    if ($(this).val() !== getToday()) {
      $('#use-today').prop('checked', false);
    } else {
      $('#use-today').prop('checked', true);
    }
  });

  // Potvrzení v modalu -> validace + redirect s parametrem
  $('#paid-date-confirm').on('click', function(){
    var val = $('#paid-date').val();

    if (!val) {
      $('#paid-date-group').addClass('has-error');
      $('#paid-date-help').show();
      return;
    }

    var finalUrl = setQueryParam(targetHref, paramName, val);
    window.location.href = finalUrl;
  });

})(jQuery);
</script>
				<?php


			}

			$coreTemplate->assign('x',$_POST);
			$coreTemplate->display('listSplatky.tpl');


		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane'.($tab==8?' active':'').'" id="tab8">';

			$dokladyArr = array();
			if(!empty($_POST['splatky']))
			{
				//projedeme splatky, mame u nich prirazene doklady
				foreach($_POST['splatky'] as $spl)
				{
					if(is_array($spl['zalohy']))
					{
						foreach($spl['zalohy'] as $iddokl=>$dokl)
						{
							$dokladyArr[$iddokl] = ZalohaGet($iddokl);
						}
					}
					if(is_array($spl['faktury']))
					{
						foreach($spl['faktury'] as $iddokl=>$dokl)
						{
							$dokladyArr[$iddokl] = FakturaGet($iddokl);
						}
					}
				}

				$sqlDoklady = coreDBSel("SELECT id FROM ".$C->db_prefix."faktury WHERE stav >= 0 AND (fa_objednavka = ? OR fa_objednavka = ?) ORDER BY id DESC",array('reg'.$_POST['id'],$_POST['id']));
				while($r = $sqlDoklady->fetchRow())
				{
					if(!isset($dokladyArr[$r['id']]))
					{
						$dokladyArr[$r['id']] = FakturaGet($r['id']);
					}
				}

				$sqlDoklady = coreDBSel("SELECT id FROM ".$C->db_prefix."zalohy WHERE stav >= 0 AND (fa_objednavka = ? OR fa_objednavka = ?) ORDER BY id DESC",array('reg'.$_POST['id'],$_POST['id']));
				while($r = $sqlDoklady->fetchRow())
				{
					if(!isset($dokladyArr[$r['id']]))
					{
						$dokladyArr[$r['id']] = ZalohaGet($r['id']);
					}
				}

				if(!empty($dokladyArr))
				{
					//sort
					function compareDatumVyst($a, $b){ return strnatcasecmp($a["datum_vyst"], $b["datum_vyst"]); }
					uasort($dokladyArr,'compareDatumVyst');
				}
			}

			echo '<div class="marginT2">';
			if($_POST['addr_stat']=='es')
			{
				echo '<a target="_blank" class="btn btn-primary marginB" href="/faktury/admin.faktury-edit/?rada=3&zastava='.$_POST['id'].'&label=Plantilla gastos de gestion"><i class="fa fa-fw fa-money"></i> '.('PLANTILLA GASTOS DE GESTIÓN').'</a> ';
				echo '<a target="_blank" class="btn btn-primary marginB" href="/faktury/admin.faktury-edit/?rada=3&zastava='.$_POST['id'].'&label=Plantilla gastos sin IVA&DPH=0"><i class="fa fa-fw fa-money"></i> '.('PLANTILLA GASTOS SIN IVA').'</a> ';
			}
			else
			{
			}

			echo '<a target="_blank" class="btn btn-primary marginB" href="/faktury/admin.faktury-edit/?rada=3&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> '.l('Vystavit fakturu za administrativní poplatky').'</a> ';
			echo '<a target="_blank" class="btn btn-primary marginB" href="/faktury/admin.faktury-edit/?rada=4&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> '.l('Vystavit fakturu za vymáhací agenturu').'</a> ';
			echo '<a target="_blank" class="btn btn-primary marginB" href="/faktury/admin.faktury-edit/?rada=5&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> '.l('Vystavit fakturu za prodej původnímu klientovi').'</a>	';
			echo '<a target="_blank" class="btn btn-danger marginB" href="/faktury/admin.faktury-edit/?rada=6&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> '.l('Vystavit fakturu za prodej třetí straně').'</a> ';
			echo '</div>';

			echo '<div class="marginT0">';
			echo '<a target="_blank" class="btn btn-default marginB" href="/zalohy/admin.zalohy-edit/?rada=3&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> '.l('Vystavit zálohu za administrativní poplatky').'</a> ';
			echo '<a target="_blank" class="btn btn-default marginB" href="/zalohy/admin.zalohy-edit/?rada=4&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> '.l('Vystavit zálohu za vymáhací agenturu').'</a> ';
			echo '<a target="_blank" class="btn btn-default marginB" href="/zalohy/admin.zalohy-edit/?rada=5&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> '.l('Vystavit zálohu za prodej původnímu klientovi').'</a>	';
			echo '<a target="_blank" class="btn btn-default marginB" href="/zalohy/admin.zalohy-edit/?rada=6&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> '.l('Vystavit zálohu za prodej třetí straně').'</a> ';
			echo '</div>';

			$coreTemplate->assign('doklady',$dokladyArr);
			$coreTemplate->display('listDoklady.tpl');

			if(prava('faktury-export'))
			{
				echo '<form action="" method="post">';
				echo createInputSubmit("sendActionDokladyDl",'<i class="fa fa-download fa-fw" aria-hidden="true"></i> '.l("Stáhnout všechny doklady ve formátu ZIP"),"form-group","btn-primary btn-sm marginTT",false);
				echo '</form>';

			}


		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane" id="tab5">';


			if ($_POST['stav_podpis']!=1)
			{
				echo boxBigInfo(l("Smlouva není podepsaná"),l('U nepodepsaných smluv je tato záložka prázdná.'));
			}
			else
			{

				if(!$_POST['saldo'])
				{
					echo boxBigInfo(l("Nemáme záznam o vymáhání"),l('Tato zástava není předaná žádné entitě k vymáhání.'));
				}
				else
				{
					echo '<form action="" method="post" class="form-horizontal">';
					echo createInputBlockStart(l('Vymáhání'));
					echo createInputText("z_bydliste",$_POST["id_user_vymahani_label"],l("Předáno"),"horizontal",false,"form-control input-sm");
					echo createInputText("date_vymahani",$_POST["date_vymahani"],l("Datum předání"),"horizontal",false,"form-control input-sm",false,false,false,false,false,false,'date');
					echo createInputBlockEnd();

					echo createInputBlockStart(l('Saldo'));
						echo '<h4>'.l('Splátky po splatnosti').'</h4>';

						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_1",$_POST['saldo']['splatka_1'],'1. '.l("Rezervační poplatek"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_1_splatnost",$_POST['saldo']['splatka_1_splatnost'],l("Splatnost"),"horizontal",false,"form-control input-sm",false,false,false,false,false,false,'date');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_1_splatnost_dni",$_POST['saldo']['splatka_1_splatnost_dni'].' '.l('dní'),l("Prodlení"),"horizontal",false,"form-control input-sm");
						echo '</div>';
						echo '</div>';


if($_POST['saldo']['splatka_2']>0)
{
						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_2",$_POST['saldo']['splatka_2'],'2. '.l("Rezervační poplatek"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_2_splatnost",$_POST['saldo']['splatka_2_splatnost'],l("Splatnost"),"horizontal",false,"form-control input-sm",false,false,false,false,false,false,'date');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_2_splatnost_dni",$_POST['saldo']['splatka_2_splatnost_dni'].' '.l('dní'),l("Prodlení"),"horizontal",false,"form-control input-sm");
						echo '</div>';
						echo '</div>';
}

if($_POST['saldo']['splatka_3']>0)
{
						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_3",$_POST['saldo']['splatka_3'],'3. '.l("Rezervační poplatek"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_3_splatnost",$_POST['saldo']['splatka_3_splatnost'],l("Splatnost"),"horizontal",false,"form-control input-sm",false,false,false,false,false,false,'date');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_3_splatnost_dni",$_POST['saldo']['splatka_3_splatnost_dni'].' '.l('dní'),l("Prodlení"),"horizontal",false,"form-control input-sm");
						echo '</div>';
						echo '</div>';
}
if($_POST['saldo']['splatka_4']>0)
{
						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_4",$_POST['saldo']['splatka_4'],'4. '.l("Rezervační poplatek"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_4_splatnost",$_POST['saldo']['splatka_4_splatnost'],l("Splatnost"),"horizontal",false,"form-control input-sm",false,false,false,false,false,false,'date');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_4_splatnost_dni",$_POST['saldo']['splatka_4_splatnost_dni'].' '.l('dní'),l("Prodlení"),"horizontal",false,"form-control input-sm");
						echo '</div>';
						echo '</div>';
}
if($_POST['saldo']['splatka_5']>0)
{
						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_5",$_POST['saldo']['splatka_5'],'5. '.l("Rezervační poplatek"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_5_splatnost",$_POST['saldo']['splatka_5_splatnost'],l("Splatnost"),"horizontal",false,"form-control input-sm",false,false,false,false,false,false,'date');
						echo '</div>';
						echo '<div class="col-xs-4">';
							echo createInputText("splatka_5_splatnost_dni",$_POST['saldo']['splatka_5_splatnost_dni'].' '.l('dní'),l("Prodlení"),"horizontal",false,"form-control input-sm");
						echo '</div>';
						echo '</div>';
}
						echo '<hr>';

						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("pojisteni",$_POST['saldo']['pojisteni'],l("Dlužné pojištění"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '</div>';

						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("penale",$_POST['saldo']['penale'],l("Neuhrazená penále"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '</div>';

						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("agentura",$_POST['saldo']['agentura'],l("Poplatek za reaktivaci smlouvy"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '</div>';

						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("dalsi",$_POST['saldo']['dalsi'],l("Další poplatky (odtahy, PHM)"),"horizontal",false,"form-control input-sm number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '</div>';

						echo '<hr>';
						echo '<div class="row">';
						echo '<div class="col-xs-4">';
							echo createInputText("celkem",$_POST['saldo']['celkem'],l("Celkem"),"horizontal",false,"form-control input-lg number",false,false,false,100,false,'<span style="position: absolute; top: 5px; right: 21px;" class="cena_mena_label">'.(isset($DATA['cena_mena'])?$DATA['cena_mena']:'CZK').'</span>');
						echo '</div>';
						echo '</div>';


						if (prava("admin-vymahani") && $_POST['saldo'])
						{
							echo '<a target="_blank" class="btn btn-primary marginB" href="/object/zastavy/object.makepdf/id/'.$_POST['id'].'/?saldo"><i class="fa fa-fw fa-download"></i> '.l('Stáhnout saldo').' (pdf)</a> ';
						}

					echo createInputBlockEnd();

						echo '</form>';
				}
				/*
				$mena = (isset($_POST['cena_mena'])?$_POST['cena_mena']:'CZK');
				echo '<form action="" method="post" class="form-horizontal">';
				echo createInputBlockStart(l('Informace o vozidlu'));
					echo createInputText("datum_podpisukup",$_POST["datum_podpisukup"],l("Datum počátku smlouvy"),"horizontal");
					echo createInputText("cena",$_POST["cena"].' '.$mena,l('Půjčka'),"horizontal");
					echo createInputText("dd",$_POST["urok"].' %',l('Sazba poplatku / nájemného (měs. úrok)'),"horizontal");
					echo createInputText("rezervacni_poplatek",$_POST["rezervacni_poplatek"].' '.$mena,l('Měsíční poplatek / nájemné'),"horizontal");

					echo '<hr>';

					//mesicu od podpisu smlouvy
					$date1 = new DateTime($_POST["datum_podpisukup"]);
					$date2 = new DateTime(date('Y-m-d'));
					$diff = $date1->diff($date2);
				    $months1 = floor($diff->y * 12 + $diff->m + $diff->d / 31);

					$date1 = new DateTime($_POST["datum_podpisukup"]);
					$date2 = new DateTime($_POST["datum_rezpoplateksplatnost"]);
					$diff = $date1->diff($date2);
					$months2 = (int)round(($diff->y * 12) + (($diff->m + $diff->d) / 30));
					$days2 = $diff->days;

					//pocet dni/mesicu po splatnosti
					$date1 = new DateTime($_POST["datum_rezpoplateksplatnost"]);
					$date2 = new DateTime(date('Y-m-d'));
					$diff = $date1->diff($date2);
					$months3 = (int)round(($diff->y * 12) + (($diff->m + $diff->d) / 30));
					$days3 = $diff->days;

					echo createInputText("dtmttt",$_POST["datum_rezpoplateksplatnost"],l('Poslední zaplacení'),"horizontal");
					echo createInputText("dtmttt",$months1,l('Akt. datum trvání smlouvy'),"horizontal");
					echo createInputText("dtmttt",$days3,l('Počet dní po splatnosti'),"horizontal");
					echo createInputText("dddddd",(is_numeric($_POST["rezervacni_poplatek"])?round($_POST["rezervacni_poplatek"]*$months1):'').' '.$mena,l('Předpokládaný výběr'),"horizontal");
					echo createInputText("dddddd",$_POST["splatky_akt_paid"].' '.$mena,l('Reálně zaplaceno'),"horizontal");

					echo '<hr>';

					echo createInputText("dddddd",$_POST["dluh_dluznacastka"].' '.$mena,l('Dlužná částka k dnešnímu dni'),"horizontal");
					echo createInputText("dddddd",$_POST["dluh_smluvnipokuta"].' '.$mena,l('Smluvní pokuta'),"horizontal");
					echo createInputText("dddddd",$_POST["dluh_vicenaklady"].' '.$mena,l('Vícenáklady'),"horizontal");
					echo createInputText("dddddd",$_POST["dluh_celkem"].' '.$mena,l('Celková výše dluhu'),"horizontal");

				echo createInputBlockEnd();
				echo '</form>';
				*/
			}
			//Datum poslední platby


		echo '</div>';
		echo '<div role="tabpanel" class="tab-pane'.($tab==6?' active':'').'" id="tab6">';


			if($_POST['komunikace_whatsapp_canwewrite'])
			{
				if($_POST['whatsapp']==0)
				{
					echo boxInfo(l('POZOR, u tohoto zákazníka není nastavena možnost zasílat WhatsApp zprávy. Je tedy možné, že si zákazník nepřeje komunikaci přes WhatsApp.'),true);
				}

				echo createInputBlockStart('<i class="fa fa-fw fa-whatsapp fa-fw fa-lg"></i> '.l('WhatsApp zpráva'),'panel-danger');
					echo '<p>'.l('Zákazníka nelze kontaktovat libovolnou zprávou přes WhatsApp do té doby, než napíše jako první, nebo odpoví na nějakou naší zprávu, kterou jsme zaslali. Jako první můžete zasílat pouze schválené šablony, např. upomínky. Pokud vidíte toto pole, znamená to, že můžete zákazníkovi odpovědět. Libovolně odpovědět můžete zákazníkovi 24 hodin po jeho poslední zprávě pro nás.').'</p>';
					echo '<form action="" method="post" class="form-horizontal" validate>';
					echo createInputHidden('typ','upominka-sms-man');
					echo createInputText('number',$_POST['komunikace_whatsapp_canwewrite'],l('Telefonní číslo'),'horizontal',true,'readonly',false,false,' readonly="readonly" ');
					echo createInputTextarea('loginfo','',l('Zpráva'),'horizontal',4,true);
					echo createInputSubmit("sendActionLogWhatsapp",l("Odeslat zprávu"),"form-group","btn-primary pull-right",false);
					echo '</form>';
				echo createInputBlockEnd();

			}
			else
			{

				echo createInputBlockStart('<i class="fa fa-fw fa-whatsapp fa-fw fa-lg"></i> '.l('Začít WhatsApp konverzaci'),'panel-danger');

					if($_POST['whatsapp']==0)
					{
						echo boxInfo(l('POZOR, u tohoto zákazníka není nastavena možnost zasílat WhatsApp zprávy. Je tedy možné, že si zákazník nepřeje komunikaci přes WhatsApp.'),true);
					}

					echo '<p>'.l('Tato funkce zákazníkovi zašle zprávu po které, když zareaguje, s ním můžete vést konverzaci přes WhatsApp. Konverzace je omezená na možnost odeslat zprávu maximálně 24 hodin po poslední zprávě klienta.').' '.l('Zpráva má přibližně toto znění:').' '.l('Vážený kliente, rádi bychom vám poskytli aktualizace a informace týkající se vašeho vozidla. Chcete pokračovat v komunikaci přes WhatsApp? Tým Cash4Car.').'</p>';
					echo '<form action="" method="post" class="form-horizontal" validate>';
					echo createInputHidden('typ','upominka-sms-start');
					echo createInputText('number',$_POST['telefon'],l('Telefonní číslo'),'horizontal',true,'readonly',false,false,' readonly="readonly" ');
					echo createInputSubmit("sendActionLogWhatsappStart",l("Odeslat žádost o komunikaci"),"form-group","btn-primary pull-right",false);
					echo '</form>';
				echo createInputBlockEnd();
			}

			//sms upominky z logu
			echo createInputBlockStart(l('Historie komunikace s klientem'));
			if (empty($_POST['konunikace_klient']))
			{
				echo l('Žádné upomínky');
			}
			else
			{
				foreach ($_POST['konunikace_klient'] as $x)
				{
					if($x['typ']=='whatsapp')
					{
						$u=false;
						if($x['id_user']>0)
						{
							$u = getUserByID($x['id_user']);
						}

						echo '<div class="row '.($x['smer']==2?($x['stav']==0?'bg-danger':'bg-warning'):'').' paddingT2 paddingB2">';
						echo '<div class="col-sm-2">';
						echo ''.($x['smer']==2?'<span class="text-'.($x['stav']==0?'danger':'warning').'">'.$C->message_type_icon[$x['message_type']].' '.$x['date_add_label'].'</span>':'<span class="text-muted">'.$C->message_type_icon[$x['message_type']].' '.$x['date_add_label'].'</span>');
						echo '</div>';
						echo '<div class="col-sm-2">';
						echo ''.($x['smer']==2?'<span class="text-'.($x['stav']==0?'danger':'warning').'"><i class="fa fa-sign-in fa-fw fa-lg" aria-hidden="true"></i> WhatsApp: '.l('Příchozí').'</span>':'<span class="text-muted"><i class="fa fa-sign-out fa-fw fa-lg" aria-hidden="true"></i> WhatsApp: '.l('Odchozí').'</span>');
						echo '</div>';
						echo '<div class="col-sm-7">';
						echo $x['info'];
						echo ($x['smer']==1 && $u?'<br><em>'.l('Odeslal').' '.$u['prijmeni'].' '.$u['jmeno'].'</em>':'');
						echo '</div>';
						echo '<div class="col-sm-1 text-right">';
						echo $C->whatsapp_stav_icon[$x['stav']].' '.($x['smer'] == 1?$C->whatsapp_stav[$x['stav']]:$C->whatsapp_stav_prichozi[$x['stav']]);
						echo '</div>';
						echo '</div>';
						echo '<hr>';
					}
					else
					{
						echo '<div class="row">';
						echo '<div class="col-sm-2">';
						echo $C->message_type_icon[$x['message_type']].' '.$x['date_add_label'];
						echo '</div>';
						echo '<div class="col-sm-2">';
						echo '<span class="text-muted"><i class="fa fa-sign-out fa-fw fa-lg" aria-hidden="true"></i> '.$x['typ_label'].'</span>';
						echo '</div>';
						echo '<div class="col-sm-6">';
						echo $x['info'];
						echo '</div>';
						echo '<div class="col-sm-1 text-right"> ';
						echo '</div>';
						echo '</div>';
						echo '<hr>';
					}
				}
			}
			echo createInputBlockEnd();


			//email upominky z logu
			echo createInputBlockStart(l('SMS'));
				echo '<form action="" method="post" class="form-horizontal" validate>';
				echo createInputHidden('typ','upominka-sms-man');
				echo createInputText('number',$_POST['telefon'],l('Telefonní číslo'),'horizontal',true);
				echo createInputText('loginfo','',l('Zpráva'),'horizontal',true);
				echo createInputSubmit("sendActionLogSMS",l("Odeslat SMS"),"form-group","btn-primary pull-right",false);
				echo '</form>';
			echo createInputBlockEnd();

			//email upominky z logu
			echo createInputBlockStart(l('Upomat (robotické volání IVR)'));
				echo '<form action="" method="post" class="form-horizontal" validate>';
				echo createInputHidden('typ','upominka-upomat-man');
				echo createInputText('number',$_POST['telefon'],l('Telefonní číslo'),'horizontal',true);
				echo createInputTextarea('loginfo','',l('Zpráva'),'horizontal',true);
				echo createInputSubmit("sendActionLogUpomat",l("Zavolat vzkaz"),"form-group","btn-primary pull-right",false);
				echo '</form>';
			echo createInputBlockEnd();


			//email upominky z logu
			echo createInputBlockStart(l('Telefonické vymáhání'));
				echo '<form action="" method="post" class="form-horizontal" validate>';
				echo createInputSelect('typ',$typ2humanT,false,l('Typ'),'horizontal',true);
				echo createInputText('loginfo','',l('Poznámka'),'horizontal',true);
				echo createInputSubmit("sendActionLogMore",l("Uložit informaci"),"form-group","btn-primary pull-right",false);
				echo '</form>';
			echo createInputBlockEnd();



			//email upominky z logu
			echo createInputBlockStart(l('Drobné komentáře ke klientovi'));
				echo '<form action="" method="post" class="form-horizontal" validate>';
				echo createInputSelect('typ',$typ2humanPen,false,l('Typ'),'horizontal',true);
				echo createInputTextarea('loginfo','',l('Poznámka'),'horizontal',true);
				echo createInputSubmit("sendActionLogMore",l("Uložit informaci"),"form-group","btn-primary pull-right",false);
				echo '</form>';

			echo createInputBlockEnd();

			//email upominky z logu
			echo createInputBlockStart(l('Odeslat e-mail'));
				echo '<form action="" method="post" class="form-horizontal" validate>';
				echo createInputHidden('typ','upominka-email-man');
				echo createInputText('email',$_POST['email'],l('E-mail'),'horizontal',true);
				echo createInputText('loginfo2','',l('Předmět'),'horizontal',true);
				echo createInputTextarea('loginfo','',l('Zpráva'),'horizontal',6,true,'wysiwyg');
				echo createInputSubmit("sendActionLogEmail",l("Odeslat e-mail"),"form-group","btn-primary pull-right",false);
				echo '</form>';

			echo createInputBlockEnd();


		echo '</div>';

		echo '<div role="tabpanel" class="tab-pane" id="tab7">';


			echo '<form action="" method="post" class="form-horizontal">';
			echo createInputBlockStart('Vozidlo prodáváme na těchto místech');
				echo createInputText("prodej_bazar",$_POST["prodej_bazar"],l("Na jakém bazaru je vůz umístěn"),"horizontal",false,"form-control input-sm");
				echo createInputText("prodej_sauto",$_POST["prodej_sauto"],l("SAUTO: Odkaz na inzerát"),"horizontal",false,"form-control input-sm");
				echo createInputText("prodej_bazos",$_POST["prodej_bazos"],l("BAZOŠ: Odkaz na inzerát"),"horizontal",false,"form-control input-sm");
				echo createInputText("prodej_mobilede",$_POST["prodej_mobilede"],l("Mobile.DE: Odkaz na inzerát"),"horizontal",false,"form-control input-sm");
				echo createInputText("prodej_tipcars",$_POST["prodej_tipcars"],l("Tipcars: Odkaz na inzerát"),"horizontal",false,"form-control input-sm");
			echo createInputBlockEnd();

			echo createInputBlockStart('Vozidlo prodané / konečná cena');

				echo createInputInfo(l("Faktura za prodej"),'<a target="_blank" class="btn btn-danger btn-lg marginB" href="/faktury/admin.faktury-edit/?rada=6&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> Vystavit fakturu za prodej třetí straně</a><br>
															 <a target="_blank" class="btn btn-primary btn-lg" href="/faktury/admin.faktury-edit/?rada=5&zastava='.$_POST['id'].'"><i class="fa fa-fw fa-money"></i> Vystavit fakturu za prodej původnímu klientovi</a>	',"horizontal",false,'lead marginB0',true);

				echo createInputText("cena_prodej",$_POST["cena_prodej"],l("Nabízená prodejní cena"),"horizontal",false,"form-control input-sm");
				echo createInputText("cena_finprodej",$_POST["cena_finprodej"],l("Konečná prodejní cena"),"horizontal",false,"form-control input-sm");
			//	echo createInputText("cena_vysledek",$_POST["cena_vysledek"],l("Výsledek obchodu"),"horizontal",false,"form-control input-sm");
				echo createInputText("cena_vysledek_aut",$_POST["cena_vysledek_aut"],l("Výsledek obchodu"),"horizontal",false,"form-control input-sm");
			echo createInputBlockEnd();


			//sms upominky z logu
			echo createInputBlockStart(l('Historie změny ceny prodeje'));
			if (empty($_POST['cena_prodej_historie']))
			{
				echo l('Žádné změny ceny');
			}
			else
			{
				foreach ($_POST['cena_prodej_historie'] as $x)
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




		echo '</form>';
		echo '</div>';





	echo '</div>';
	echo '</div>'; //tabs


	echo '</div>';
	echo '</div>';



}