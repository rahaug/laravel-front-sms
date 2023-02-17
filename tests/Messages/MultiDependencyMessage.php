<?php

namespace Tests\Messages;

use RolfHaug\FrontSms\Notifications\SmsNotification;
use Tests\Article;

class MultiDependencyMessage extends SmsNotification
{
    public $message = 'Hi %s, you have a new comment on your "%s" article. Read it here: %s';

    /**
     * @var Article
     */
    private $article;

    public function __construct($message = null, Article $article)
    {
        $this->article = $article;
        if ($message) {
            $this->message = $message;
        }
        parent::__construct($this->message);
    }

    public function getMessage($notifiable)
    {
        return vsprintf($this->message, [
            $notifiable->name,
            $this->article->title,
            $this->article->link
        ]);
    }
}
