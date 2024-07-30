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
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use slots\Main;
use function array_keys;
use function count;
use function time;

class InventoryTransactionListener implements Listener {
	/** @var string[] */
	private static array $checkTransaction = [];

	public function onInventoryTransaction(InventoryTransactionEvent $event) : void {
		$player = $event->getTransaction()->getSource();
		foreach ($event->getTransaction()->getActions() as $transaction) {
			if ($transaction instanceof SlotChangeAction) {
				$inventory = $transaction->getInventory();
				if ($inventory instanceof EnderChestInventory) {
					$inventories = $event->getTransaction()->getInventories();
					if ($inventories[array_keys($inventories)[0]] instanceof EnderChestInventory) {
						return;
					}

					$enderchestPermissions = Main::getInstance()->getEnderChestSlotCache()->slots;
					$enderchestSlotsCounts = 0;
					foreach ($enderchestPermissions as $permissionName => $enderchestSlotInfo) {
						if(!$player->hasPermission($permissionName)) {
							continue;
						}
						$enderchestSlotsCounts = $enderchestSlotInfo->getSlots();
					}

					if (count($inventory->getContents()) >= $enderchestSlotsCounts && !$inventory->contains($transaction->getSourceItem())) {
						if (isset(self::$checkTransaction[$player->getName()]) && self::$checkTransaction[$player->getName()] > time()) {
							$player->removeCurrentWindow();
						}
						self::$checkTransaction[$player->getName()] = time() + 1;
						$player->sendMessage(Main::getInstance()->getConfig()->get('enderchest.no.space'));
						$event->cancel();
					}
					return;
				}
			}
		}
	}

}
