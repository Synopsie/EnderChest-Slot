<?php
declare(strict_types=1);

namespace slots\utils;

final class EnderChestSlotInfo {

    private int $slots;
    private string $permission;

    public function __construct(
        int $slots,
        string $permission
    ) {
        $this->slots = $slots;
        $this->permission = $permission;
    }

    public function getSlots() : int {
        return $this->slots;
    }

    public function getPermission() : string {
        return $this->permission;
    }

}