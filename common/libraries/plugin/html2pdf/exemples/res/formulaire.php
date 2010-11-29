<?php
	$url = 'http://';
	$url.= $_SERVER['HTTP_HOST'];
	if (ISSET($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT']!=80) $url.= ':'.$_SERVER['SERVER_PORT'];
	$url.= $_SERVER['REQUEST_URI'];
?>
<page footer="form">
	<h1>Test de formulaire</h1><br>
	<br>
	<form action="<?php echo $url; ?>">
		<input type="hidden" name="test" value="1">
		Vous utilisez cette librairie dans le cadre :
		<ul style="list-style: none">
			<li><input type="checkbox" name="cadre_boulot" checked="checked"> du boulot</li>
			<li><input type="checkbox" name="cadre_perso" > perso</li>
		</ul>
		Vous �tes :
		<ul style="list-style: none">
			<li><input type="radio" name="sexe" value="homme"> un homme</li>
			<li><input type="radio" name="sexe" value="femme"> une femme</li>
		</ul>
		Vous avez : 
		<select name="age" >
			<option value="15">moins de 15 ans</option>
			<option value="20">entre 15 et 20 ans</option>
			<option value="25">entre 20 et 25 ans</option>
			<option value="30">entre 25 et 30 ans</option>
			<option value="40">plus de 30 ans</option>
		</select><br>
		<br>
		Vous aimez : 
		<select name="aime[]" size="5" multiple="multiple">
			<option value="ch1">l'informatique</option>
			<option value="ch2">le cin�ma</option>
			<option value="ch3">le sport</option>
			<option value="ch4">la litt�rature</option>
			<option value="ch5">autre</option>
		</select><br>
		<br>
		Votre phrase f�tiche : <input type="text" name="phrase" value="cette lib est g�niale !!!" style="width: 100mm"><br>
		<br>
		Un commentaire ?<br>
		<textarea name="comment" rows="3" cols="30">rien de particulier</textarea><br>
		<br>
		<input type="reset" name="btn_reset" value="Initialiser">
		<input type="button" name="btn_print" value="Imprimer" onclick="print(true);">
		<input type="submit" name="btn_submit" value="Envoyer">
	</form>
</page>