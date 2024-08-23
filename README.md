# EnderChest-Slot Plugin 📚

## Features 🛠️

- **Configurable**: Various customization options.
- **Permissions**: Control slots access.
- **Messages**: Inform players of actions.

## Configuration 📝

```yaml
# EnderchestSlots config.

permission.slots:
  4: # Le nombre ici correspond au nombre de slot accordé au joueur
    permission: "slots.enderchest.4"
    default: user

item:
  id: "minecraft:barrier"
  name: '§o§cBloqué'
enderchest.name: '§8EnderChest de §e%player%'

command:
  name: enderchest
  description: Permet d'ouvrir l'enderchest.
  usage: /enderchest
  aliases:
    - ec
  permission:
    name: enderchest.use
    default: operator # console, operator, user
```
---
![EnderChest](enderchest-slot.png)