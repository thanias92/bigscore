<?php

use yii\helpers\Html;

$this->title = 'TICKETING';
$this->registerCssFile('/themes/css/contact.css'); // Pastikan CSS custom lo ke-load

?>

<div class="container">
    <h1><?= Html::encode($this->title) ?></h1>

    <div class="tab-buttons">
        <button class="tab-button <?= $source === 'staff' ? 'active' : '' ?>" data-source="staff">
            Ticket by Staff
        </button>
        <button class="tab-button <?= $source === 'customer' ? 'active' : '' ?>" data-source="customer">
            Ticket by Customer
        </button>
    </div>

    <div class="action-bar">
        <select class="filter-select">
            <option value="">Filter</option>
            <option value="status">Status</option>
            <option value="priority">Priority</option>
            </select>

            <div class="search-container">
        <?= Html::input('text', '', '', ['class' => 'search-input', 'placeholder' => 'Search...']) ?>
        <button class="search-button">
            <i class="fas fa-search"></i> </button>
    </div>

    <button class="add-ticket-button" id="add-vendor-button" style="<?= $source !== 'staff' ? 'display: none;' : '' ?>">
        <i class="fas fa-plus"></i> Ticket
    </button>
    </div>

    <?php if ($source !== 'staff'): ?>
        <div class="alert alert-warning">
            Halaman <strong>Ticket by Customer</strong> masih dalam pengembangan.
        </div>
    <?php else: ?>

        <table class="ticket-table">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Customer</th>
                    <th>No Ticket</th>
                    <th>Date</th>
                    <th>Via</th>
                    <th>Modul</th>
                    <th>Judul</th>
                    <th>Label</th>
                    <th>Prioritas</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($ticketData)): ?>
                    <?php foreach ($ticketData as $index => $ticket): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= Html::encode($ticket->customer->nama_customer ?? '-') ?></td>
                            <td><?= Html::encode($ticket->code_ticket) ?></td>
                            <td><?= Html::encode(date('d/m/Y', strtotime($ticket->date_ticket))) ?></td>
                            <td><?= Html::encode($ticket->via) ?></td>
                            <td><?= Html::encode($ticket->modul) ?></td>
                            <td><?= Html::encode($ticket->title) ?></td>
                            <td>
                                <?php
                                $labels = explode(',', $ticket->label_ticket);
                                foreach ($labels as $label) {
                                    echo "<span class='badge bg-info'>" . Html::encode(trim($label)) . "</span> ";
                                }
                                ?>
                            </td>
                            <td>
                                <?php
                                $priorityClass = match ($ticket->priority_ticket) {
                                    'High' => 'bg-danger',
                                    'Medium' => 'bg-warning',
                                    default => 'bg-success',
                                };
                                echo "<span class='badge $priorityClass'>" . Html::encode($ticket->priority_ticket) . "</span>";
                                ?>
                            </td>
                            <td>
                                <?php
                                $statusColors = ['Open' => 'bg-warning', 'In Progress' => 'bg-primary', 'Done' => 'bg-success'];
                                $statusClass = $statusColors[$ticket->status_ticket] ?? 'bg-secondary';
                                echo "<span class='badge $statusClass'>" . Html::encode($ticket->status_ticket) . "</span>";
                                ?>
                            </td>
                            <td>
                                <?= Html::a('📝', ['default/update', 'id' => $ticket->id_ticket], ['class' => 'btn btn-sm btn-outline-secondary']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">Tidak ada data tiket.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

    <?php endif; ?>

</div>