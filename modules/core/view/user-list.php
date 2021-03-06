<?php
use zion\orm\Filter;
?>
<div class="center-content filter-page">
<div class="container-fluid">

	<form class="form-inline hide-advanced-fields ajaxform" action="/zion/rest/core/User/" method="POST" data-callback="defaultFilterCallback">
		<br>
		<div class="card">
			<div class="card-header">
				Filtro
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[userid][low]">userid</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[userid][operator]" name="filter[userid][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-integer" id="filter[userid][low]" name="filter[userid][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-integer" id="filter[userid][high]" name="filter[userid][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[login][low]">login</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[login][operator]" name="filter[login][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[login][low]" name="filter[login][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[login][high]" name="filter[login][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[password][low]">password</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[password][operator]" name="filter[password][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[password][low]" name="filter[password][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[password][high]" name="filter[password][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[force_new_password][low]">force_new_password</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[force_new_password][operator]" name="filter[force_new_password][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-integer" id="filter[force_new_password][low]" name="filter[force_new_password][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-integer" id="filter[force_new_password][high]" name="filter[force_new_password][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[redefine_password_hash][low]">redefine_password_hash</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[redefine_password_hash][operator]" name="filter[redefine_password_hash][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[redefine_password_hash][low]" name="filter[redefine_password_hash][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[redefine_password_hash][high]" name="filter[redefine_password_hash][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[name][low]">name</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[name][operator]" name="filter[name][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[name][low]" name="filter[name][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[name][high]" name="filter[name][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[email][low]">email</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[email][operator]" name="filter[email][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[email][low]" name="filter[email][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[email][high]" name="filter[email][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[phone][low]">phone</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[phone][operator]" name="filter[phone][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[phone][low]" name="filter[phone][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[phone][high]" name="filter[phone][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[docf][low]">docf</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[docf][operator]" name="filter[docf][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[docf][low]" name="filter[docf][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[docf][high]" name="filter[docf][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[doce][low]">doce</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[doce][operator]" name="filter[doce][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[doce][low]" name="filter[doce][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[doce][high]" name="filter[doce][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[docm][low]">docm</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[docm][operator]" name="filter[docm][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[docm][low]" name="filter[docm][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[docm][high]" name="filter[docm][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[validity_begin][low]">validity_begin</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[validity_begin][operator]" name="filter[validity_begin][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-datetime" id="filter[validity_begin][low]" name="filter[validity_begin][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-datetime" id="filter[validity_begin][high]" name="filter[validity_begin][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[validity_end][low]">validity_end</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[validity_end][operator]" name="filter[validity_end][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-datetime" id="filter[validity_end][low]" name="filter[validity_end][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-datetime" id="filter[validity_end][high]" name="filter[validity_end][high]" rows="1"></textarea>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-3">
						<label for="filter[status][low]">status</label>
					</div>
					<div class="col-sm-9">
						<select class="form-control filter-operator" id="filter[status][operator]" name="filter[status][operator]">
							<option value=""></option>
							<?foreach(Filter::getOperators() AS $key => $text){?>
							<option value="<?=$key?>"><?=$text?></option>
							<?}?>
						</select>
						
						<textarea class="form-control filter-low type-string" id="filter[status][low]" name="filter[status][low]" rows="1"></textarea>
						<textarea class="form-control filter-high type-string" id="filter[status][high]" name="filter[status][high]" rows="1"></textarea>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button type="submit" id="filter-button" class="btn btn-primary">Filtrar</button>
				<button type="button" id="button-toggleFilterMode" class="btn btn-outline-secondary" data-mode="simple">Alternar Modo</button>
				<a id="button-new" class="btn btn-outline-info" href="/zion/mod/core/User/new" target="_blank">Novo</a>
			</div>
		</div>
	</form>

	<div id="filter-result">
		<div class="container-fluid">Execute o filtro</div>
	</div>
</div></div>