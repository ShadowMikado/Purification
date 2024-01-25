<?php

namespace ShadowMikado\Purification;

use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;
use ShadowMikado\Purification\listeners\move;

class Main extends PluginBase
{
    use SingletonTrait;

    public static Config $config;

    protected function onLoad(): void
    {
        $this->getLogger()->info("Loading...");
        self::setInstance($this);
    }

    protected function onEnable(): void
    {
        $this->getLogger()->info("Enabling...");
        $this->saveDefaultConfig();
        self::$config = $this->getConfig();
        $this->getServer()->getPluginManager()->registerEvents(new move(), $this);

        $typePurification = self::$config->getNested("purification.type");
        $toPurifie = self::$config->getNested("purification.purified");

        if ($typePurification == "money" && !is_int($toPurifie)) {
            $this->handleWarning("money", "de l'argent");
        }

        if ($typePurification == "items" && !is_string($toPurifie)) {
            $this->handleWarning("items", "un item");
        }
    }

    protected function onDisable(): void
    {
        $this->getLogger()->info("Disabling...");
    }

    private function handleWarning($selectedType, $expectedType)
    {
        $this->getLogger()->warning("Le type de purification sélectionné est '$selectedType' mais la récompense de la purification semble ne pas être $expectedType");
        $this->getLogger()->critical("Arrêt du serveur");
        $this->getServer()->shutdown();
    }
}