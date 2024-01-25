<?php

namespace ShadowMikado\Purification\task;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use ShadowMikado\Purification\api\PurificationAPI;
use ShadowMikado\Purification\Main;

class PurifTask extends Task
{

    public static bool $isInZone = false;
    public static $task;

    public Player $player;
    public int $time;

    public PurificationAPI $api;

    public function __construct(Player $player)
    {

        self::$task = $this;
        $this->player = $player;
        $this->api = new PurificationAPI();
        $this->time = Main::$config->getNested("time.time_to_purifie");

    }

    public function onRun(): void
    {
        $player = $this->player;
        $item = $player->getInventory()->getItemInHand();

        if ($item->getTypeId() === $this->api->getToPurifieItem()->getTypeId()) {

            if ($item->getCount() >= $this->api->getToPurifieItemCount()) {
                switch ($this->time) {
                    case $this->time:
                        $player->sendPopup($this->api->getFormatedLoadingMessage($this->time));
                        break;
                }
                if ($this->time == 0) {

                    $rewardType = $this->api->getRewardType();
                    if ($rewardType == "items" || $rewardType == "money") {
                        $player->getInventory()->setItemInHand($item->setCount($item->getCount() - $this->api->getToPurifieItemCount()));

                        if ($rewardType == "items") {
                            $player->getInventory()->addItem($this->api->getPurifiedItem()->setCount($this->api->getPurifiedItemCount()));
                        } elseif ($rewardType == "money") {
                            $player->sendTip($this->api->getMoneyMessage());

                            BedrockEconomyAPI::legacy()->addToPlayerBalance("{$player->getName()}", $this->api->getMoneyNumber());
                        }
                    }
                    $this->time = Main::$config->getNested("time.time_to_purifie");
                }
                $this->time--;
            } else {
                $player->sendPopup($this->api->getNotCountMessage());
            }

        } else {
            $player->sendPopup($this->api->getNotGoodItemMessage());
            $this->time = Main::$config->getNested("time.time_to_purifie");
        }
    }
}