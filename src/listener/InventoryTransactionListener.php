<?php
declare(strict_types=1);

namespace slots\listener;

use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\inventory\transaction\action\SlotChangeAction;
use slots\Main;

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