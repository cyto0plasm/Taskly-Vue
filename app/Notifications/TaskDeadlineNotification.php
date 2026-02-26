<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TaskDeadlineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public $task)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via( $notifiable): array
    {
        return ['mail', 'database'];
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toMail( $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Task Deadline Reminder')
            ->line("Your task '{$this->task->title}' is due soon.")
            ->action('View Task', url('/tasks/'.$this->task->id))
            ->line('Please complete it before the deadline.');
            }

    /**
     * Get the array representation of the notification for database storage.
     */
    public function toDatabase($notifiable)
        {
            return [
                'task_id' => $this->task->id,
                'message' => "Task '{$this->task->title}' is due soon."
            ];
        }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray( $notifiable): array
    {
        return [
            //
        ];
    }
}
