<section id="main">
    <div class="page-header">
        <ul>
        <span class="color">
            <li><i class="fa fausers fa-fw"></i><?= $this->url->link(t('View all groups'), 'group', 'index') ?></li>
        </ul>
    </div>
    <form method="post" action="<?= $this->url->href('group', 'save') ?>" autocomplete="off">
        <?= $this->form->csrf() ?>

        <?= $this->form->label(t('Name'), 'name') ?>
        <?= $this->form->text('name', $values, $errors, array('autofocus', 'required', 'maxlength="100"')) ?>

        <div class="form-actions">
            <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <div class="botoncancel">
            <?= $this->url->link(t('cancel'), 'group', 'index') ?>
        </div>
        </div>
    </form>
</section>
