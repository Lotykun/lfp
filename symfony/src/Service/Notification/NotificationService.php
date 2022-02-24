<?php


namespace App\Service\Notification;

use App\Entity\Team;
use App\Entity\Trainer;
use App\Entity\Player;
use App\Service\Notification\Channels\Mail\MailManager;
use App\Service\Notification\Channels\Whatsapp\WhatsAppManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class NotificationService
 * @package App\Service\Notification
 */
class NotificationService
{
    private $conf;
    private $channels;


    /**
     * NotificationService constructor.
     * @param ParameterBagInterface $params
     * @param MailManager $mailManager
     * @param WhatsAppManager $whatsAppManager
     */
    public function __construct(ParameterBagInterface $params, MailManager $mailManager, WhatsAppManager $whatsAppManager)
    {
        $this->conf = $params->get('notificationservice');
        $this->channels = array();
        foreach ($this->conf['channels'] as $channel){
            if ($channel == 'mail') {
                $this->channels[$channel] = $mailManager;
            } elseif ($channel == 'whatsapp') {
                $this->channels[$channel] = $whatsAppManager;
            }
        }
    }

    /**
     * @return array
     */
    public function getChannels(): array
    {
        return $this->channels;
    }

    /**
     * @param array $channels
     */
    public function setChannels(array $channels): void
    {
        $this->channels = $channels;
    }

    /**
     * @param Team $team
     * @param Trainer $trainer
     */
    public function addTrainerToTeamNotification(Team $team, Trainer $trainer){
        $data = array(
            'subject' => 'Entrenador Contratado!',
            'text' => 'Entrenador: ' . $trainer->getName() . ' Ha sido contratado por el Equipo: ' . $team->getName()
        );
        foreach ($this->conf['channels'] as $channel){
            $data['from'] = $this->conf[$channel]['from'];
            $data['to'] = $this->conf[$channel]['to'];
            $notification = $this->channels[$channel]->createNotification($data);
            $this->channels[$channel]->send($notification);
        }
    }

    /**
     * @param Team $team
     * @param Player $player
     */
    public function addPlayerToTeamNotification(Team $team, Player $player){
        $data = array(
            'subject' => 'Jugador Contratado!',
            'text' => 'Jugador: ' . $player->getName() . ' Ha sido contratado por el Equipo: ' . $team->getName()
        );
        foreach ($this->conf['channels'] as $channel){
            $data['from'] = $this->conf[$channel]['from'];
            $data['to'] = $this->conf[$channel]['to'];
            $notification = $this->channels[$channel]->createNotification($data);
            $this->channels[$channel]->send($notification);
        }
    }

    /**
     * @param Team $team
     * @param Trainer $trainer
     */
    public function removeTrainerFromTeamNotification(Team $team, Trainer $trainer){
        $data = array(
            'subject' => 'Entrenador Dado de Baja!',
            'text' => 'Entrenador: ' . $trainer->getName() . ' Ha sido dado de baja del Equipo: ' . $team->getName()
        );
        foreach ($this->conf['channels'] as $channel){
            $data['from'] = $this->conf[$channel]['from'];
            $data['to'] = $this->conf[$channel]['to'];
            $notification = $this->channels[$channel]->createNotification($data);
            $this->channels[$channel]->send($notification);
        }
    }

    /**
     * @param Team $team
     * @param Player $player
     */
    public function removePlayerFromTeamNotification(Team $team, Player $player){
        $data = array(
            'subject' => 'Jugador Dado de Baja!',
            'text' => 'Jugador: ' . $player->getName() . ' Ha sido dado de baja del Equipo: ' . $team->getName()
        );
        foreach ($this->conf['channels'] as $channel){
            $data['from'] = $this->conf[$channel]['from'];
            $data['to'] = $this->conf[$channel]['to'];
            $notification = $this->channels[$channel]->createNotification($data);
            $this->channels[$channel]->send($notification);
        }
    }
}