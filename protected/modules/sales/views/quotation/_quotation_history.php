<?php

use yii\helpers\Html;

/** @var \app\models\QuotationHistory[] $histories */
?>

<style>
    /* --- CSS KHUSUS UNTUK HISTORY TIMELINE --- */
    .history-section-wrapper {
        padding: 20px;
        height: 100%;
        max-height: 550px;
        /* Batas tinggi maksimal */
        overflow-y: auto;
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

    /* Warna titik berdasarkan tipe aktivitas */
    .history-timeline-item.create::before {
        background-color: #28a745;
        /* Hijau */
    }

    .history-timeline-item.update::before {
        background-color: #007bff;
        /* Biru */
    }

    .history-timeline-item.notification::before {
        background-color: #17a2b8;
        /* Cyan */
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
    <h5 class="mb-3">Quotation History</h5>

    <?php if (empty($histories)) : ?>
        <p class="text-muted text-center mt-4">No history recorded yet.</p>
    <?php else : ?>
        <ul class="history-timeline">
            <?php foreach ($histories as $history) : ?>
                <?php
                // Menentukan kelas CSS berdasarkan tipe aktivitas dari database
                $activityClass = Html::encode($history->activity_type);
                ?>
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