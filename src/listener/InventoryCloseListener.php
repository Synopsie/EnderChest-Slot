<?php

/*
 *  ____   __   __  _   _    ___    ____    ____    ___   _____
 * / ___|  \ \ / / | \ | |  / _ \  |  _ \  / ___|  |_ _| | ____|
 * \___ \   \ V /  |  \| | | | | | | |_) | \___ \   | |  |  _|
 *  ___) |   | |   | |\  | | |_| | |  __/   ___) |  | |  | |___
 * |____/    |_|   |_| \_|  \___/  |_|     |____/  |___| |_____|
 *
 * Ce plugin permet de limiter les slots disponible dans l'enderchest
 *
 * @author Synopsie
 * @link https://github.com/Synopsie
 * @version 1.1.0
 *
 */

declare(strict_types=1);

namespace slots\listener;

use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\inventory\InventoryCloseEvent;
use pocketmine\event\Listener;
use slots\command\EnderChestCommand;
use slots\utils\Utils;

class InventoryCloseListener implements Listener {
	public function onInventoryClose(InventoryCloseEvent $event) : void {
		$player = $event->getPlayer();

		if($event->getInventory() instanceof EnderChestInventory) {
			if(isset(EnderChestCommand::$inventory[$player->getName()])) {
				unset(EnderChestCommand::$inventory[$player->getName()]);
				Utils::sendFakeBlock($player, VanillaBlocks::AIR(), 0, 3, 0);
			}
		}
	}
}
