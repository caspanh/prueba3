<div class="sidebar sidebar-icons">
    <h2><?= t('Task #%d', $task['id']) ?></h2>
    <ul>
        <li <?= $this->app->checkMenuSelection('task', 'show') ?>>
            <i class="fa fanewspaper-o fa-fw"></i>
            <?= $this->url->link(t('Summary'), 'task', 'show', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('activity', 'task') ?>>
            <i class="fa fadashboard fa-fw"></i>
            <?= $this->url->link(t('Activity stream'), 'activity', 'task', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('task', 'transitions') ?>>
            <i class="fa faarrows-h fa-fw"></i>
            <?= $this->url->link(t('Transitions'), 'task', 'transitions', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <li <?= $this->app->checkMenuSelection('task', 'analytics') ?>>
            <i class="fa fabar-chart fa-fw"></i>
            <?= $this->url->link(t('Analytics'), 'task', 'analytics', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php if ($task['time_estimated'] > 0 || $task['time_spent'] > 0): ?>
        <li <?= $this->app->checkMenuSelection('task', 'timetracking') ?>>
            <i class="fa faclock-o fa-fw"></i>
            <?= $this->url->link(t('Time tracking'), 'task', 'timetracking', array('task_id' => $task['id'], 'project_id' => $task['project_id'])) ?>
        </li>
        <?php endif ?>
    </ul>

    <?php if ($this->user->hasProjectAccess('taskmodification', 'edit', $task['project_id'])): ?>
    <h2><?= t('Actions') ?></h2>
    <ul>
        <li>
            <i class="fa fapencil-square-o fa-fw"></i>
            <?= $this->url->link(t('Edit the task'), 'taskmodification', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa faalign-left fa-fw"></i>
            <?= $this->url->link(t('Edit the description'), 'taskmodification', 'description', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa farefresh fa-rotate-90 fa-fw"></i>
            <?= $this->url->link(t('Edit recurrence'), 'TaskRecurrence', 'edit', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa fafaplus fa-fw"></i>
            <?= $this->url->link(t('Add a sub-task'), 'subtask', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa facode-fork fa-fw"></i>
            <?= $this->url->link(t('Add internal link'), 'TaskInternalLink', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa faexternal-link fa-fw"></i>
            <?= $this->url->link(t('Add external link'), 'TaskExternalLink', 'find', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa facomment-o fa-fw"></i>
            <?= $this->url->link(t('Add a comment'), 'comment', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa fafile fa-fw"></i>
            <?= $this->url->link(t('Attach a document'), 'TaskFile', 'create', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa facamera fa-fw"></i>
            <?= $this->url->link(t('Add a screenshot'), 'TaskFile', 'screenshot', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa fafiles-o fa-fw"></i>
            <?= $this->url->link(t('Duplicate'), 'taskduplication', 'duplicate', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa faclipboard fa-fw"></i>
            <?= $this->url->link(t('Duplicate to another project'), 'taskduplication', 'copy', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <i class="fa faclone fa-fw"></i>
            <?= $this->url->link(t('Move to another project'), 'taskduplication', 'move', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
        </li>
        <li>
            <?php if ($task['is_active'] == 1): ?>
                <i class="fa fatimes fa-fw"></i>
                <?= $this->url->link(t('Close this task'), 'taskstatus', 'close', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            <?php else: ?>
                <i class="fa facheck-square-o fa-fw"></i>
                <?= $this->url->link(t('Open this task'), 'taskstatus', 'open', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            <?php endif ?>
        </li>
        <?php if ($this->task->canRemove($task)): ?>
            <li>
                <i class="fa fatrash-o fa-fw"></i>
                <?= $this->url->link(t('Remove'), 'task', 'remove', array('task_id' => $task['id'], 'project_id' => $task['project_id']), false, 'popover') ?>
            </li>
        <?php endif ?>
    </ul>
    <?php endif ?>

    <?= $this->hook->render('template:task:sidebar', array('task' => $task)) ?>
</div>