<div class="page-header">
    <h2 class="modificar"><?= t('Add a screenshot') ?></h2>
    <hr class="r" />
</div>

<div id="screenshot-zone">
    <p id="screenshot-inner"><?= t('Take a screenshot and press CTRL+V or ⌘+V to paste here.') ?></p>
     <?= $this->form->hidden('task_id', $values) ?>
    <?= $this->form->hidden('user_id', $values) ?>
    
    <div class="markdown-editor-small">
        <?= $this->form->textarea(
            'comment',
            $values,
            $errors,
            array(
                'autofocus',
                'required',
                'placeholder="'.t('Leave a comment').'"',
                'data-mention-search-url="'.$this->url->href('UserHelper', 'mention', array('project_id' => $task['project_id'])).'"',
            ),
            'markdown-editor'
        ) ?>
    
</div>

<form class="popover-form" action="<?= $this->url->href('TaskFile', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>" method="post">

    <input type="hidden" name="screenshot"/>
    <?= $this->form->csrf() ?>


    <div class="form-actions">
        <button type="submit" class="btn btn-blue"><?= t('Save') ?></button>
        <div class="botoncancel">
        <?= $this->url->link(t('cancel'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'close-popover') ?>
    </div>
    </div>
</form>

<p class="alert alert-info"><?= t('This feature does not work with all browsers.') ?></p>