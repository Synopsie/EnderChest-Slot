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

namespace slots;

use iriss\IrissCommand;
use olymp\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use slots\command\EnderChestCommand;
use slots\listener\InventoryCloseListener;
use slots\listener\InventoryOpenListener;
use slots\listener\InventoryTransactionListener;
use slots\utils\EnderChestSlotCache;
use slots\utils\EnderChestSlotInfo;
use sofia\Updater;

class Main extends PluginBase {
	use SingletonTrait;

	private EnderChestSlotCache $enderchestCache;
	private PermissionManager $permissionManager;

	protected function onLoad() : void {
		$this->getLogger()->info('§6Chargement du plugin Enderchest-Slots...');

		$this->saveResource('config.yml');

		self::setInstance($this);
	}

	protected function onEnable() : void {

        if (!file_exists($this->getFile() . 'vendor')) {
            $this->getLogger()->error('Merci d\'installer une release du plugin et non le code source. (https://github.com/Synopsie/EnderChest-Slot/releases)');
            $this->getServer()->getPluginManager()->disablePlugin($this);
            return;
        }

		require $this->getFile() . 'vendor/autoload.php';
		Updater::checkUpdate('EnderChest-Slot', $this->getDescription()->getVersion(), 'Synopsie', 'EnderChest-Slot');
        IrissCommand::register($this);
		$this->enderchestCache   = new EnderChestSlotCache();
		$this->permissionManager = new PermissionManager();
		$config                  = $this->getConfig();

        $this->permissionManager->registerPermission(
            $config->getNested('command.permission.name'),
            'EnderChest',
            $this->permissionManager->getType($this->permissionManager->getType($config->getNested('command.permission.default')))
        );

        $this->getServer()->getCommandMap()->register('EnderChest-Slot', new EnderChestCommand(
            $config->getNested('command.name'),
            $config->getNested('command.description'),
            $config->getNested('command.usage'),
            [],
            $config->getNested('command.aliases')
        ));

		foreach ($config->get('permission.slots') as $key => $value) {
			$this->registerEnderchestSlotPermission($value['permission'], $key, $value['default']);
		}


		$this->getServer()->getPluginManager()->registerEvents(new InventoryTransactionListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new InventoryOpenListener(), $this);
		$this->getServer()->getPluginManager()->registerEvents(new InventoryCloseListener(), $this);
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
