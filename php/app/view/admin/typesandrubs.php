<?php
use Form\{
    RubricForm,
    TypeForm
};
use Core\Util\{
    ErrorManager,
    SuccessManager
};
if (isset($typeForms, $formAddType, $rubForms, $formAddRub)) {
?>

<section class="sect-adm" id="sect-adm_typsrubs">

	<h1>Les types et les rubriques</h1>

	<?php
        foreach (SuccessManager::getMessages() as $message) : ?>
            <div class="alert alert-success" role="alert">
                <?= $message ?>
            </div>
        <?php endforeach;
        SuccessManager::destroy();

        foreach (ErrorManager::getMessages() as $message) : ?>
            <div class="alert alert-danger" role="alert">
                <?= $message ?>
            </div>
        <?php endforeach;
        ErrorManager::destroy();
    ?>

	<div>

		<table>
		
			<thead>
				<tr>
					<th>Type</th>
					<th colspan="2">Options</th>
				</tr>
			</thead>
			<tbody>

				<?php foreach($typeForms as $typeForm) :
					if ($typeForm instanceof TypeForm) : ?>

						<tr>

							<form action="/admin/type/update/<?= $typeForm->getValue('id') ?>" method="POST">

                                <?= $typeForm->input('id', null, ['style' => 'display: none;', 'type' => 'hidden']); ?>

								<td>
                                    <?= $typeForm->input('label', null, ['required' => 'required']); ?>
                                </td>
								<td class="td-modif">
									<button class="btn btn-warning">Modifier</button>
								</td>

							</form>

							<td class="td-suppr">
								<a href="/admin/type/delete/<?= $typeForm->getValue('id') ?>" class="btn btn-danger">Supprimer</a>
							</td>

						</tr>

					<?php endif;
				endforeach; ?>

				<form action="/admin/type/insert" method="POST">
				
					<tr>

						<td>
                            <?= $formAddType->input('label', null, ['required' => 'required']); ?>
                        </td>
						<td class="td-ajout" colspan="2">
							<button class="btn btn-success">Ajouter</button>
						</td>

					</tr>
					
				</form>

			</tbody>
		</table>

		<table>
			<thead>
				<tr>
					<th>Rubrique</th>
					<th>Image</th>
					<th colspan="2">Options</th>
				</tr>
			</thead>
			<tbody>

				<?php foreach($rubForms as $rubForm) :
					if ($rubForm instanceof RubricForm) : ?>

						<tr>

							<form action="/admin/rubric/update/<?= $rubForm->getValue('id') ?>" method="POST">

                                <?= $rubForm->input('id', null, ['style' => 'display: none;', 'type' => 'hidden']); ?>

								<td>
                                    <?= $rubForm->input('label', null, ['required' => 'required']); ?>
                                </td>
								<td>
                                    <?= $rubForm->input('image', null, ['required' => 'required']); ?>
                                </td>
								<td class="td-modif">
                                    <button class="btn btn-warning">Modifier</button>
                                </td>

							</form>

							<td class="td-suppr">
                                <a href="/admin/rubric/delete/<?= $rubForm->getValue('id') ?>" class="btn btn-danger">Supprimer</a>
                            </td>

						</tr>

					<?php endif;
				endforeach; ?>

				<form action="/admin/rubric/insert" method="POST">
					<tr>

						<td>
                            <?= $formAddRub->input('label', null, ['required' => 'required']); ?>
						</td>
						<td>
                            <?= $formAddRub->input('image', null, ['required' => 'required']); ?>
						</td>
						<td class="td-ajout" colspan="2">
                            <button class="btn btn-success">Ajouter</button>
                        </td>

					</tr>
				</form>

			</tbody>
		</table>

	</div>

	<p>
        <a href="/admin/contact">
            <button class="btn btn-primary">Contacts</button>
        </a>
		<a href="/admin/link">
			<button class="btn btn-primary">Liens</button>
		</a>
		<a href="/admin/user">
			<button class="btn btn-primary">Utilisateurs</button>
		</a>
		<a href="/admin/recette">
			<button class="btn btn-primary">Recettes</button>
		</a>
        <a href="/admin/snippet">
            <button class="btn btn-primary">Snippets</button>
        </a>
	</p>

</section>
<?php
}
?>

