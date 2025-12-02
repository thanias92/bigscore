<?php

use yii\helpers\Html;

/** @var \app\models\DealsHistory[] $histories */
?>

<style>
    /* --- CSS KHUSUS UNTUK HISTORY TIMELINE (SAMA SEPERTI QUOTATION) --- */
    .history-section-wrapper {
        padding: 20px;
        background-color: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
    }

    .history-timeline {
        position: relative;
        padding-left: 15px;
        list-style: none;
        margin-left: 0;
        margin-top: 1rem;
    }

    .history-timeline::before {
        content: '';
        position: absolute;
        top: 5px;
        bottom: 5px;
        left: 5px;
        width: 2px;
        background-color: #dee2e6;
    }

    .history-timeline-item {
        position: relative;
        margin-bottom: 20px;
        padding-left: 25px;
    }

    .history-timeline-item::before {
        content: '';
        position: absolute;
        top: 5px;
        left: 0;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background-color: #6c757d;
        /* Warna default (abu-abu) */
        border: 2px solid #ffffff;
        z-index: 1;
    }

    /* Warna titik berdasarkan tipe aktivitas untuk DEALS */
    .history-timeline-item.new_deal::before,
    .history-timeline-item.create::before {
        background-color: #28a745;
        /* Hijau untuk deal baru */
    }

    .history-timeline-item.label_change::before,
    .history-timeline-item.update::before {
        background-color: #007bff;
        /* Biru untuk update/perubahan label */
    }

    .history-timeline-item.quotation_linked::before {
        background-color: #fd7e14;
        /* Oranye untuk pengaitan quotation */
    }

    .history-date {
        font-size: 0.8em;
        color: #6c757d;
        margin-bottom: 3px;
    }

    .history-description {
        font-size: 0.9em;
        color: #343a40;
        line-height: 1.4;
        font-weight: 500;
    }

    .history-author {
        font-size: 0.75em;
        color: #888;
        margin-top: 4px;
        font-style: italic;
    }
</style>

<div class="history-section-wrapper">
    <h5 class="mb-3">Deals History</h5>

    <?php if (empty($histories)) : ?>
        <p class="text-muted text-center mt-4">No history recorded yet.</p>
    <?php else : ?>
        <ul class="history-timeline">
            <?php foreach ($histories as $history) : ?>
                <?php $activityClass = Html::encode($history->activity_type); ?>
                <li class="history-timeline-item <?= $activityClass ?>">
                    <div class="history-date">
                        <?= Yii::$app->formatter->asDatetime($history->created_at, 'php:d M Y') ?>
                    </div>
                    <div class="history-description">
                        <?= nl2br(Html::encode($history->description)) ?>
                    </div>
                    <div class="history-author">
                        By: <?= Html::encode($history->createdBy->username ?? 'System') ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>