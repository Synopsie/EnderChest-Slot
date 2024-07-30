<?php
declare(strict_types=1);

namespace slots;

use olymp\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use slots\listener\InventoryTransactionListener;
use slots\utils\EnderChestSlotCache;
use slots\utils\EnderChestSlotInfo;

class Main extends PluginBase {
    use SingletonTrait;

    private EnderChestSlotCache $enderchestCache;
    private PermissionManager $permissionManager;

    protected function onLoad() : void {
        $this->getLogger()->info('§6Chargement du plugin Enderchest-Slots...');

        $this->saveResource('config.yml', true);

        self::setInstance($this);
    }

    protected function onEnable() : void {
        require $this->getFile() . 'vendor/autoload.php';

        $this->enderchestCache = new EnderChestSlotCache();
        $this->permissionManager = new PermissionManager();

        $config = $this->getConfig();
        foreach ($config->get('permission.slots') as $key => $value) {
            $this->registerEnderchestSlotPermission($value['permission'], $key, $value['default']);
        }

        $this->getServer()->getPluginManager()->registerEvents(new InventoryTransactionListener(), $this);
        $this->getLogger()->info('§aPlugin Enderchest-Slots activé !');
    }

    public function registerEnderchestSlotPermission(string $permission, int $slot, string $defaultGroup) : void {
        $this->permissionManager->registerPermission($permission, '', $this->permissionManager->getType($defaultGroup));
        $this->enderchestCache->slots[$permission] = new EnderChestSlotInfo($slot, $permission);
    }

    public function getEnderChestSlotCache() : EnderChestSlotCache {
        return $this->enderchestCache;
    }

}