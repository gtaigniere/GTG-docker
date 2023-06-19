<?php
use Core\Html\Form;
use Core\Util\ErrorManager;
use Core\Util\SuccessManager;

if (isset($form)) {
?>

    <section id="section_contact">

        <h1>Contact</h1>

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

        <?php if ($form instanceof Form) : ?>

        <form class="form_contact" action="?target=admin&admTarg=contact&action=insert" method="POST">

            <div>
                <?= $form->input('firstname', 'Prénom :', ['required' => 'required']); ?>
            </div>

            <div>
                <?= $form->input('mail', 'Mail :', ['type' => 'email', 'required' => 'required']); ?>
            </div>

            <div>
                <?= $form->input('object', 'Objet :', ['required' => 'required']); ?>
            </div>

            <div class="message_contact">
                <?= $form->textarea('message', 'Message :', ['class' => 'contact', 'rows' => '8', 'maxlength' => '400', 'required' => 'required']); ?>
            </div>

            <button class="btn btn-info">Envoyer</button>

        </form>

        <?php endif; ?>

    </section>
<?php
}
?>
