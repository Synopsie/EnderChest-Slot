<?php
declare(strict_types=1);

namespace slots\utils;

use pocketmine\block\Block;
use pocketmine\block\tile\Nameable;
use pocketmine\block\tile\Tile;
use pocketmine\block\tile\TileFactory;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\BlockActorDataPacket;
use pocketmine\network\mcpe\protocol\types\BlockPosition;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\UpdateBlockPacket;
use pocketmine\player\Player;

class Utils {

    public static function sendFakeBlock(Player $player, Block $blocks, int $positionX, int $positionY, int $positionZ, ?string $customName = null, ?string $class = null) : void {
        $position = $player->getPosition();
        $position->x += $positionX;
        $position->y += $positionY;
        $position->z += $positionZ;
        $blockPosition = BlockPosition::fromVector3($position);
        $player->getNetworkSession()->sendDataPacket(UpdateBlockPacket::create(
            $blockPosition,
            TypeConverter::getInstance()->getBlockTranslator()->internalIdToNetworkId($blocks->getStateId()),
            UpdateBlockPacket::FLAG_NETWORK,
            UpdateBlockPacket::DATA_LAYER_NORMAL
        ));
        if (!is_null($customName) && !is_null($class)){
            $player->getNetworkSession()->sendDataPacket(
                BlockActorDataPacket::create(
                    $blockPosition,
                    new CacheableNbt(
                        CompoundTag::create()
                            ->setString(Tile::TAG_ID, TileFactory::getInstance()->getSaveId($class))
                            ->setString(Nameable::TAG_CUSTOM_NAME, $customName)
                    )
                )
            );
        }
    }

}