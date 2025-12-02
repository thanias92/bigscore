<?php

use yii\helpers\Html;

$this->title = 'Merge Tasks';

$this->registerCssFile('https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css');
$this->registerCss("
.kanban-board { display: flex; gap: 1rem; overflow-x: auto; padding-top: 1rem; }
.kanban-column { flex: 1; background-color: #f8f9fa; border-radius: 10px; padding: 1rem; min-width: 250px; }
.kanban-header { font-weight: bold; font-size: 18px; margin-bottom: 1rem; border-bottom: 2px solid #0d6efd; padding-bottom: .5rem; }
.kanban-card { background-color: white; border-radius: 10px; box-shadow: 0 0 5px rgba(0,0,0,0.1); padding: 1rem; margin-bottom: 1rem; }
.card-label { font-size: 12px; margin-top: 0.25rem; }
.label-medium { background-color: #ffc107; color: white; padding: 2px 6px; border-radius: 5px; }
.label-high { background-color: #dc3545; color: white; padding: 2px 6px; border-radius: 5px; }
.label-low { background-color: #28a745; color: white; padding: 2px 6px; border-radius: 5px; }
.badge-tag { margin-right: 5px; font-size: 11px; }
");
?>

<div class="container-fluid">
    <div class="kanban-board">
        <div class="kanban-column">
            <div class="kanban-header"><?= Html::encode('Merge') ?></div>
            <?php if (!empty($tasks)): ?>
                <?php foreach ($tasks as $t): ?>
                    <div class="kanban-card">
                        <div class="fw-bold"><?= Html::encode($t->title) ?></div>
                        <div class="text-muted small"><?= Html::encode($t->client) ?></div>
                        <div class="small text-muted"><?= Html::encode($t->date) ?></div>
                        <div class="mt-2"><i class="bi bi-person-circle"></i> <?= Html::encode($t->pic) ?></div>
                        <div class="card-label mt-1">
                            <span class="label-<?= strtolower($t->priority) ?>"><?= Html::encode($t->priority) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Tidak ada task dengan status Merge.</p>
            <?php endif; ?>
        </div>
    </div>
</div>