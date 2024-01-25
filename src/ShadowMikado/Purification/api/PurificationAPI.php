<?php

namespace ShadowMikado\Purification\api;

use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\utils\Config;
use ShadowMikado\Purification\Main;

class PurificationAPI
{
    private Config $config;
    public function __construct()
    {
        $this->config = Main::$config;
    }

    public function getRewardType(): string {
        return $this->config->getNested("purification.type");
    }

    private function getStringToPurifie(): array {
        return explode("|", $this->config->getNested("purification.to_purifie"));
    }

    private function getStringPurifiedItem(): array {
        return explode("|", $this->config->getNested("purification.purified"));
    }

    public function getMoneyMessage(): string {
        return str_replace("{money}", $this->getMoneyNumber(),$this->config->getNested("messages.money"));
    }

    public function getMoneyNumber(): int {
        return $this->config->getNested("purification.purified");
    }

    public function getToPurifieItem(): Item
    {
        return StringToItemParser::getInstance()->parse($this->getStringToPurifie()[1]);
    }

    public function getToPurifieItemCount(): int
    {
        return intval($this->getStringToPurifie()[0]);
    }

    public function getPurifiedItem(): Item
    {
        return StringToItemParser::getInstance()->parse($this->getStringPurifiedItem()[1]);
    }

    public function getPurifiedItemCount(): int
    {
        return intval($this->getStringPurifiedItem()[0]);
    }

    public function getNotGoodItemMessage(): string {
        return str_replace(["{item}", "{count}"], [$this->getToPurifieItem()->getName(), $this->getToPurifieItemCount()], $this->config->getNested("messages.not_good_item"));
    }

    public function getNotCountMessage(): string {
        return str_replace(["{item}", "{count}"], [$this->getToPurifieItem()->getName(), $this->getToPurifieItemCount()], $this->config->getNested("messages.not_count"));
    }

    private function getLoadingMessage(): string
    {
        return $this->config->getNested("messages.loading");
    }

    public function getFormatedLoadingMessage(int $time) {
        $msg = $this->getLoadingMessage();
        $timeStr = $this->getCooldownBarMessage($time);
        return str_replace("{time}", $timeStr, $msg);
    }

    public function getCooldownBarMessage(int $time): string
    {
        $unit = $this->config->getNested("time.time_bar_unit");
        $totalTime = $this->config->getNested("time.time_to_purifie");
        $colorOK = $this->config->getNested("time.color_purifie");
        $colorNot = $this->config->getNested("time.color_not_purifie");
        $number = $totalTime - $time;
        $remaining = $totalTime - $number;
        $msg = $colorOK . str_repeat($unit, $number) . $colorNot . str_repeat($unit, $remaining);
        return $msg;

    }
}