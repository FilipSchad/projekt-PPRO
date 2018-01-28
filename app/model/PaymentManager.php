<?php

namespace App\Model;

use Nette\Application\UI;
use App\Model\Entities\Payment;
use App\Model\PlayerManager;
use App\Model\SeasonManager;

/*
 * Payment management
 */

class PaymentManager
{
    const PAYMENT_ENTITY = '\App\Model\Entities\Payment';
    
    public function __construct(\Kdyby\Doctrine\EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getPayments()
    {
        return $this->em->getRepository($this::PAYMENT_ENTITY)->findBy(array());
    }
    
    public function getPaymentById($id)
    {
        return $this->em->find($this::PAYMENT_ENTITY, $id);
    }
    
    public function getPaymentForm()
    {
        $form = new UI\Form;
        
        $playerMan = new PlayerManager($this->em);
        $players = $playerMan->getPlayers();
        $teamArr = array( "" => "");
        foreach ($players as $player) {
            $playerArr[$player->getPlayerId()] = $player->getSurname() . " " . $player->getName();
        }
        
        $form->addSelect('player_id', 'Hráč:')
                ->setItems($playerArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber hráče');
        
        $form->addText('purpose', 'Účel platby:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Účel platby');
        $form->addInteger('sum', 'Částka:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Částka');
        $form->addText('dueDate', 'Datum splatnosti:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Datum splatnosti')
                ->setAttribute('class', 'date_duedate');
        $form->addText('payedOn', 'Datum zaplacení:')
                ->setAttribute('placeholder', 'Datum zaplacení')
                ->setAttribute('class', 'date_payedon');
        $form->addText('variableSymbol', 'Variabilní symbol:')
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Variabilní symbol');
        
        $seasonMan = new SeasonManager($this->em);
        $seasons = $seasonMan->getSeasons();
        $seasArr = array( "" => "");
        foreach ($seasons as $season) {
            $seasArr[$season->getSeasonId()] = $season->getSeasonName();
        }
        
        $form->addSelect('season_id', 'Sezóna:')
                ->setItems($seasArr)
                ->setRequired('Položka je povinná.')
                ->setAttribute('placeholder', 'Vyber sezónu');
        return $form;
    }
    
    public function deletePaymentById($id)
    {
        $payment = $this->em->find($this::PAYMENT_ENTITY, $id);
        $this->em->remove($payment);
        $this->em->flush();
    }
            
    public function createOrUpdatePaymentFromForm($form, $id)
    {
        try {
            if ($id > 0) {
                $payment = $this->getPaymentById($id);
            }
            else
            {
                $payment = new Payment();
            }
            $values = $form->getValues();
            $playerMan = new PlayerManager($this->em);
            $seasonMan = new SeasonManager($this->em);

            $player = $playerMan->getPlayerById($values['player_id']);
            $season = $seasonMan->getSeasonById($values['season_id']);
            $payment->setPlayer($player);
            if (isset($values['team_id'])) {
                $teamMan = new TeamManager($this->em);
                $payment->setTeam($teamMan->getTeamById($values['team_id']));
            }
            else {
                $payment->setTeam($player->getTeam());
            }
            $payment->setSeason($season);
            $payment->setPurpose($values['purpose']);
            $payment->setSum($values['sum']);
            $payment->setDueDate(new \DateTime($values['dueDate']));
            if ($values['payedOn'] && strtotime($values['payedOn'])) {
                $payment->setPayedOn(new \DateTime($values['payedOn']));
            }
            $payment->setVariableSymbol($values['variableSymbol']);        
            $this->em->persist($payment);
            $this->em->flush();
            return TRUE;
        } catch (Exception $ex) {
            return FALSE;
        }
    }
}
