<?php
	function buildTable($names, $data, $options = []){
		$html = '<div class"row">'; 
		$html .=  (isset($options['actions']['create'])) && $options['actions']['create'] !== '' ? '<div class="col s12 right"><a href="'.$options['actions']['create'].'" class="btn-floating btn-large waves-effect waves-light teal"><i class="material-icons">add</i></a></div>': '';
			$html .= '<div class="col s12">';
				$html .= (isset($options['class'])) && $options['class'] !== '' ? '<table class="'.$options['class'].'">' : '<table>';
					$html .= '<thead><tr>';
					for($i = 0; $i < sizeof($names); $i++){
						if(isset($options['actions']['selector']) && $options['actions']['selector'] != $names[$i]){
							$html .= '<th>'.$names[$i].'</th>';
						} else {
							$html .= '<th></th>';
						}
					}
					$html .= '</tr></thead>';
					$html .= '<tbody>';
					for($i = 0; $i < sizeof($data); $i++){
						$html .= '<tr>';
						foreach($data[$i] as $key => $value){
							if(isset($options['actions']['selector']) && $options['actions']['selector'] != $key){
								$html .= '<td>'.$value.'</td>';
							} else {
								$html .= isset($options['actions']['edit']) && $options['actions']['edit'] !== '' ? '<td><a href="'.$options['actions']['edit'].$value.'"><i class="material-icons">mode_edit</i></a></td>' : '';
								$html .= isset($options['actions']['delete']) && $options['actions']['delete'] !== '' ? '<td><a href="'.$options['actions']['delete'].$value.'"><i class="material-icons">delete</i></a></td>' : '';
							}
						}
						$html .= '</tr>';
					}
					$html .= '</tbody>';
				$html .= '</table>';
			$html .= '</div>';
		$html .= '</div>';
		return $html;
	}