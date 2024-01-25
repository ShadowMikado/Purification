<?php

namespace ShadowMikado\Purification\listeners;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use ShadowMikado\Purification\Main;
use ShadowMikado\Purification\task\PurifTask;

class move implements Listener
{
    public function onMove(PlayerMoveEvent $e) {
        $player = $e->getPlayer();

        if ($this->isInPurifZone($player)) {
            PurifTask::$isInZone = true;
        } else {
            $this->cancelPurifTask($player);
        }

        if (PurifTask::$isInZone == true && PurifTask::$task == null) {
            $task = new PurifTask($player);
            Main::getInstance()->getScheduler()->scheduleRepeatingTask($task, 20)->getTask();
        }
    }

    private function isInPurifZone(Player $player): bool {
        $pos1 = Main::$config->getNested("position.position_1");
        $pos2 = Main::$config->getNested("position.position_2");

        $playerPos = $player->getPosition();
        return (
            $playerPos->x >= min($pos1[0], $pos2[0]) &&
            $playerPos->x <= max($pos1[0], $pos2[0]) &&
            $playerPos->y >= min($pos1[1], $pos2[1]) &&
            $playerPos->y <= max($pos1[1], $pos2[1]) &&
            $playerPos->z >= min($pos1[2], $pos2[2]) &&
            $playerPos->z <= max($pos1[2], $pos2[2])
        );
    }

    public function onQuit(PlayerQuitEvent $e) {
        $this->cancelPurifTask($e->getPlayer());
    }

    private function cancelPurifTask(Player $player) {
        $task = PurifTask::$task;
        if ($task instanceof Task) {
            $task->getHandler()?->cancel();
            PurifTask::$task = null;
            $player->sendPopup(Main::$config->getNested("messages.canceled"));
        }
        PurifTask::$isInZone = false;
    }
}