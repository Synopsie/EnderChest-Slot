<?php

/*
 *  ____   __   __  _   _    ___    ____    ____    ___   _____
 * / ___|  \ \ / / | \ | |  / _ \  |  _ \  / ___|  |_ _| | ____|
 * \___ \   \ V /  |  \| | | | | | | |_) | \___ \   | |  |  _|
 *  ___) |   | |   | |\  | | |_| | |  __/   ___) |  | |  | |___
 * |____/    |_|   |_| \_|  \___/  |_|     |____/  |___| |_____|
 *
 * Plugin enderchest-slot
 *
 * @author Synopsie
 * @link https://github.com/Synopsie
 * @version 1.0.0
 *
 */

declare(strict_types=1);

namespace slots\listener;

use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\item\StringToItemParser;
use slots\Main;
use function count;

class InventoryOpenListener implements Listener {
	public function onInventoryOpen(InventoryOpenEvent $event) : void {
		$inventory = $event->getInventory();
		if($inventory instanceof EnderChestInventory) {
			$player                = $event->getPlayer();
			$enderchestPermissions = Main::getInstance()->getEnderChestSlotCache()->slots;
			$enderchestSlotsCounts = 0;
			foreach ($enderchestPermissions as $permissionName => $enderchestSlotInfo) {
				if (!$player->hasPermission($permissionName)) {
					continue;
				}
				$enderchestSlotsCounts = $enderchestSlotInfo->getSlots();
			}

			$config = Main::getInstance()->getConfig();
			$item   = StringToItemParser::getInstance()->parse($config->getNested('item.id'))->setCustomName($config->getNested('item.name', ''));

			$currentContents = $inventory->getContents();
			$availableSlots  = $enderchestSlotsCounts - count($currentContents);

			$totalSlots = $inventory->getSize();
			for ($i = count($currentContents) + $availableSlots; $i < $totalSlots; $i++) {
				$inventory->setItem($i, $item);
			}
		}
	}
}
