<?php

namespace app\models;

use Yii;
use app\traits\AuditTrailTrait;

/**
 * This is the model class for table "ticket".
 *
 * @property int $id_ticket
 * @property int $id_deals
 * @property int $id_task
 * @property string $code_ticket
 * @property string $user
 * @property string $role
 * @property string $priority_ticket
 * @property string $label_ticket
 * @property string $via
 * @property string $assigne
 * @property string $modul
 * @property string $title
 * @property string $date_ticket
 * @property string $status_ticket
 * @property string $description
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $duedate

 */
class Ticket extends \yii\db\ActiveRecord
{
  use AuditTrailTrait;
  /**
   * {@inheritdoc}
   */
  public static function tableName()
  {
    return 'ticket';
  }

  /**
   * {@inheritdoc}
   */
  public function rules()
  {
    return [
      [['id_deals', 'created_by', 'updated_by', 'deleted_by', 'deleted_at'], 'default', 'value' => null],
      [['id_deals', 'code_ticket', 'user', 'role', 'modul', 'title', 'date_ticket', 'description'], 'required'],
      [['date_ticket','duedate', 'created_at', 'updated_at', 'deleted_at'], 'safe'],
      [['description'], 'string'],
      [['id_deals', 'id_task', 'created_by', 'updated_by', 'deleted_by'], 'integer'],
      [['code_ticket', 'user', 'role', 'priority_ticket', 'label_ticket', 'via', 'assigne', 'modul', 'title', 'status_ticket'], 'string', 'max' => 255],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function attributeLabels()
  {
    return [
      'id_ticket' => 'Id Ticket',
      'id_deals' => 'Nama Customer',
      'id_task' => 'Update Status',
      'code_ticket' => 'No Ticket',
      'user' => 'User',
      'role' => 'Role',
      'priority_ticket' => 'Priority',
      'label_ticket' => 'Label',
      'via' => 'Via',
      'assigne' => 'Assigne',
      'modul' => 'Modul',
      'title' => 'Title',
      'date_ticket' => 'Date',
      'duedate' => 'Due Date',
      'status_ticket' => 'Status',
      'description' => 'Description',
      'created_by' => 'Created By',
      'updated_by' => 'Updated By',
      'deleted_by' => 'Deleted By',
      'created_at' => 'Created At',
      'updated_at' => 'Updated At',
      'deleted_at' => 'Deleted At',

    ];
  }
  public function getDeals()
  {
    return $this->hasOne(Deals::class, ['deals_id' => 'id_deals']);
  }

  public function getTaskStatus()
  {
    return $this->hasOne(Task::class, ['id_task' => 'id_task']);
  }

  public static function data_ticket_all()
  {
    $tickets = Ticket::find()
      ->where(['role' => 'staff']) // Ambil hanya ticket dari role staff
      ->all();

    $data_ticket = [];

    foreach ($tickets as $ticket) {
      $data_ticket[] = [
        'id_ticket'       => $ticket->id_ticket,
        'id_deals'        => $ticket->id_deals,
        'id_task'         => $ticket->id_task,
        'code_ticket'     => $ticket->code_ticket,
        'user'         => $ticket->user, // Ambil id user dari ticket
        'role'            => $ticket->role,
        'priority_ticket' => $ticket->priority_ticket,
        'label_ticket'    => $ticket->label_ticket,
        'via'             => $ticket->via,
        'assigne'         => $ticket->assigne,
        'modul'           => $ticket->modul,
        'title'           => $ticket->title,
        'date_ticket'     => $ticket->date_ticket,
        'duedate'         => $ticket->duedate,
        'status_ticket'   => $ticket->status_ticket,
        'description'     => $ticket->description,
      ];
    }

    return $data_ticket;
  }
  public static function getStatusList()
  {
    return [
      'wiating' => 'Waiting',
      'open' => 'Open',
      'in_progress' => 'In Progress',
      'done' => 'Done',
    ];
  }

  public static function data_ticket_all_detail($filter = [])
  {
    $query = self::find()
      ->with(['deals', 'taskStatus']) // relasi deals (nama customer) dan taskStatus (status progress)
      ->where(['deleted_at' => null, 'role' => 'staff']);

    $tickets = $query->all();
    $list = [];

    foreach ($tickets as $ticket) {
      $list[$ticket->id_ticket] = [
        'no_ticket'        => $ticket->code_ticket ?? '-',
        'nama_customer'    => $ticket->deals->customer->nama ?? '-', // pastikan relasi deals->customer->nama ada
        'user'            => $ticket->user ?? '-',
        'judul'            => $ticket->title ?? '-',
        'modul'            => $ticket->modul ?? '-',
        'prioritas'        => $ticket->priority_ticket ?? '-',
        'label'            => $ticket->label_ticket ?? '-',
        'via'              => $ticket->via ?? '-',
        'assigne'          => $ticket->assigne ?? '-',
        'tanggal_tiket'    => Yii::$app->formatter->asDate($ticket->date_ticket),
        'duedate'          => $ticket->duedate ?? '-',
        'status_tiket'     => $ticket->status_ticket ?? '-',
        // 'status_progress'  => $ticket->taskStatus->status_task ?? '-', // relasi ke task status
        'deskripsi'        => $ticket->description ?? '-',
      ];
    }

    return $list;
  }

  public function getTask()
  {
    return $this->hasOne(Task::class, ['id' => 'id_task']);
  }
}
