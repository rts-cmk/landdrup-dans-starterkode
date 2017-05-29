<?php

	if(secCheckLevel() > 90){
		$options = [
			'class' => 'striped responsive-table',
			'actions' => [
				'selector' => 'id',
				'edit' => 'index.php?side=editUser&id=',
				'delete' => 'index.php?side=deleteUser&id=',
				'create' => 'index.php?side=opretBruger'
			]
		];
	} else {
		$options = [
			'class' => 'striped responsive-table',
			'actions' => [
				'selector' => 'id',
				'edit' => 'index.php?side=editUser&id='
			]
		];
	}

	echo buildTable(
		['Dato','Fornavn', 'Efternavn', 'Rolle', 'id'], 
		sqlQueryAssoc('SELECT profil.oprettet, profil.fornavn, profil.efternavn, brugerroller.navn, brugere.id FROM `brugere`
						INNER JOIN `brugerroller` ON `brugere`.`fkBrugerrolle` = `brugerroller`.`id`
						INNER JOIN `profil` ON `profil`.`id` = `brugere`.`fkProfil`'),
		$options
	);
?>
