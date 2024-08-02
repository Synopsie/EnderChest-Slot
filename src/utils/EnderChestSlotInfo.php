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
 * @version 1.0.2
 *
 */

declare(strict_types=1);

namespace slots\utils;

final class EnderChestSlotInfo {
	private int $slots;
	private string $permission;

	public function __construct(
		int $slots,
		string $permission
	) {
		$this->slots      = $slots;
		$this->permission = $permission;
	}

	public function getSlots() : int {
		return $this->slots;
	}

	public function getPermission() : string {
		return $this->permission;
	}

}
