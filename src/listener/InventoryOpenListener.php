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
 * @version 1.0.3
 *
 */

declare(strict_types=1);

namespace slots\listener;

use pocketmine\block\inventory\EnderChestInventory;
use pocketmine\block\tile\EnderChest;
use pocketmine\event\inventory\InventoryOpenEvent;
use pocketmine\event\Listener;
use pocketmine\item\StringToItemParser;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use slots\Main;
use function count;

class InventoryOpenListener implements Listener {

    public function onInventoryOpen(InventoryOpenEvent $event) : void {
        $inventory = $event->getInventory();
        if ($inventory instanceof EnderChestInventory) {
            $player = $event->getPlayer();
            $config = Main::getInstance()->getConfig();
            $customNameFormat = str_replace('%player%', $player->getName(), $config->get('enderchest.name', '§8EnderChest de §e%player%'));
            $customName = str_replace('%player%', $player->getName(), $customNameFormat);

            $tile = $inventory->getHolder()->getWorld()->getTile($inventory->getHolder());
            if ($tile instanceof EnderChest) {
                $nbt = $tile->saveNBT();
                $nbt->setTag('CustomName', new StringTag($customName));
                $packet = BlockActorDataPacket::create(
                    new BlockPosition(
                        (int)$tile->getPosition()->getX(),
                        (int)$tile->getPosition()->getY(),
                        (int)$tile->getPosition()->getZ()
                    ),
                    new CacheableNbt($nbt)
                );
                $player->getNetworkSession()->sendDataPacket($packet);
            }

            $enderchestPermissions = Main::getInstance()->getEnderChestSlotCache()->slots;
            $enderchestSlotsCounts = 0;
            foreach ($enderchestPermissions as $permissionName => $enderchestSlotInfo) {
                if (!$player->hasPermission($permissionName)) {
                    continue;
                }
                $enderchestSlotsCounts = $enderchestSlotInfo->getSlots();
            }

            $item = StringToItemParser::getInstance()->parse($config->getNested('item.id'))->setCustomName($config->getNested('item.name', '§o§cBloqué'));
            $currentContents = $inventory->getContents();
            $availableSlots = $enderchestSlotsCounts - count($currentContents);
            $totalSlots = $inventory->getSize();
            for ($i = count($currentContents) + $availableSlots; $i < $totalSlots; $i++) {
                $inventory->setItem($i, $item);
            }
        }
    }
}
