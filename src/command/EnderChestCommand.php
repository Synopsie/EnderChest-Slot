<?php
declare(strict_types=1);

namespace slots\command;

use iriss\CommandBase;
use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\block\tile\EnderChest;
use pocketmine\block\VanillaBlocks;
use pocketmine\command\CommandSender;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use slots\Main;
use slots\utils\Utils;

class EnderChestCommand extends CommandBase {

    public static array $inventory = [];

    public function __construct(string $name, Translatable|string $description, string $usageMessage, array $subCommands = [], array $aliases = []) {
        parent::__construct($name, $description, $usageMessage, $subCommands, $aliases);
        $this->setPermission(Main::getInstance()->getConfig()->getNested('command.permission.name'));
    }

    public function getCommandParameters() : array {
        return [];
    }

    protected function onRun(CommandSender $sender, array $parameters) : void {
        if(!$sender instanceof Player) return;

        $position = $sender->getPosition();
        $position->y += 3;
        Utils::sendFakeBlock($sender, VanillaBlocks::ENDER_CHEST(), 0, 3, 0, str_replace('%player%', $sender->getName(), Main::getInstance()->getConfig()->get('enderchest.name', '§8EnderChest de §e%player%')), EnderChest::class);
        self::$inventory[$sender->getName()] = true;
        $sender->setCurrentWindow(new EnderChestInventory($position, $sender->getEnderInventory()));
    }

}